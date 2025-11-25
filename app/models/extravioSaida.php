<?php

include '../../config/database.php';
include 'insereTabSubs.php';



$user = $_SESSION['usuario'];

// evitar undefined index
$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? ''; // definido para evitar undefined mais abaixo

$NovOrc = null;
$NovPed = null;

$sqlContrato = "SELECT TOP 1 TB02112_CODIGO FROM TB02112 WHERE TB02112_SITUACAO = 'A' AND TB02112_PAT = :patSaida";
$stmtContrato = $pdo->prepare($sqlContrato);
$stmtContrato->execute([
    ':patSaida' => $itemRetirado,
]);

$contrato = $stmtContrato->fetchColumn();

if (!$contrato || !$itemRetirado) {
    echo "Dados incompletos.";
    exit;
}

// Verifica se o PAT de saida pertence ao contrato, se o PAT de entrada existe e se essa substituição ja foi iniciada.
$verificaSql = "SELECT COUNT(*) AS total
                FROM TB02112
                WHERE TB02112_PAT = :itemRetirado
                    AND TB02112_CODIGO = :contrato
                    AND TB02112_SITUACAO = 'A'
                 ";

$verificaStmt = $pdo->prepare($verificaSql);
$verificaStmt->execute([
    ':itemRetirado' => $itemRetirado,
    ':contrato' => $contrato
]);

$total = (int) $verificaStmt->fetchColumn();

if ($total === 0) {
    echo "Dados incorretos. Confira se os dados informados estão corretos.";
    exit;
}

// Cria orçamento
try {
    // Inicia a transação
    $pdo->beginTransaction();

    // Primeira execução (CRIA_ORC_SAIDA)
    $stmt = $pdo->prepare("
        EXEC CRIA_ORC_SAIDA
            @PatAnt = :itemAntigo,
            @Contrato = :Contrato,
            @User = :user,
            @Operacao = '71',
            @Status = 'A8'
    ");

    if (
        !$stmt->execute([
            ':itemAntigo' => $itemRetirado,
            ':Contrato' => $contrato,
            ':user' => $user
        ])
    ) {
        throw new Exception("Erro na execução da primeira consulta.");
    }

    // Segunda execução (CriarPedidoCompra)
    $stmt2 = $pdo->prepare("
        EXEC CriarPedidoCompra
            @CodigosPat = :itemAntigo,
            @User = :user,
            @Operacao = '67',
            @Status = '42'
    ");

    if (
        !$stmt2->execute([
            ':itemAntigo' => $itemRetirado,
            ':user' => $user
        ])
    ) {
        throw new Exception("Erro na execução da segunda consulta.");
    }

    // tenta ler resultados (se houver)
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $NovOrc = $result['NovOrc'] ?? null;

    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $NovPed = $result2['NovPed'] ?? null;

    $pdo->commit();

    echo "Alteração feita com sucesso!";
} catch (Exception $e) {
    // tenta rollback se possível
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erro ao alterar: " . $e->getMessage();
}

// insere tab de acompanhamento (chamada feita mesmo que NovOrc/NovPed sejam null)
$respIns = insereTabSubs($pdo, $itemRetirado, $itemAdicionado, $contrato, $user, 'ES', '', NULL, NULL, NULL, $NovOrc, $NovPed);
error_log('[subsitemcontrato] resp insereTabSubs = ' . $respIns);
// echo ' | RESP_SUBS=' . $respIns;  // temporário pra você ver no alert/resposta
