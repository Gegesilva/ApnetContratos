<?php

include '../../config/database.php';
include 'insereTabSubs.php';

$user = $_SESSION['usuario'];

$contrato = $_POST['contrato'] ?? '';
$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? '';
$acessoriosSelecionados = $_POST['acessoriosSelecionados'] ?? '';

/* define e flag se possui acessrios */
if(isset($acessoriosSelecionados) && $acessoriosSelecionados != '') {
    $acessorios = 'S';
} else {
    $acessorios = 'N';
}

if (!$contrato || !$itemRetirado || !$itemAdicionado || $user == '') {
    echo "Dados incompletos.";
    exit;
}


// Verifica se o PAT de saida pertence ao contrato, se o PAT de entrada existe e se essa substituição ja foi iniciada.
$verificaSql = "SELECT COUNT(*) AS total
                FROM TB02112
                WHERE TB02112_PAT = :itemRetirado
                    AND TB02112_CODIGO = :contrato
                    AND TB02112_SITUACAO = 'A'
                    AND EXISTS (SELECT * FROM TB02054 WHERE TB02054_PAT = :itemAdicionado AND TB02054_QTPROD > TB02054_QTPRODS)
                    AND NOT EXISTS (SELECT * FROM TB02112 WHERE TB02112_PAT = :itemAdicionado3 AND  TB02112_SITUACAO = 'A')
                    AND NOT EXISTS (SELECT * FROM SUBS_CONTRATO WHERE COD_CONTRATO = :contrato2 AND PAT_ENTRADA = :itemAdicionado2 AND PAT_SAIDA = :itemRetirado2)";

$verificaStmt = $pdo->prepare($verificaSql);
$verificaStmt->execute([
    ':itemRetirado' => $itemRetirado,
    ':itemRetirado2' => $itemRetirado,
    ':itemAdicionado' => $itemAdicionado,
    ':itemAdicionado2' => $itemAdicionado,
    ':itemAdicionado3' => $itemAdicionado,
    ':contrato' => $contrato,
    ':contrato2' => $contrato
]);

$total = (INT) $verificaStmt->fetchColumn();

if ($total === 0) {
    echo "Dados incorretos. Confira se os dados informados estão corretos.";
    exit;
}

// depois (APENAS debug, sem mudar regras):
$respIns = insereTabSubs($pdo, $itemRetirado, $itemAdicionado, $contrato, $user, 'E', $acessorios);
error_log('[subsitemcontrato] resp insereTabSubs = ' . $respIns);
// echo ' | RESP_SUBS=' . $respIns;  // temporário pra você ver no alert/resposta

$respInsAcess = insereAcessorios($pdo, $itemRetirado, $itemAdicionado,$contrato, $user, $acessoriosSelecionados);
error_log('[subsitemcontrato] resp insereTabSubs = ' . $respInsAcess);
//echo ' | RESP_SUBS=' . $respInsAcess;  // temporário pra você ver no alert/resposta

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
            @Status = 'A8',
            @PatAdd = :PatAdd,
            @Acessorios = :Acessorios;
    ");

    if (
        !$stmt->execute([
            ':itemAntigo' => $itemRetirado,
            ':Contrato' => $contrato,
            ':user' => $user,
            ':PatAdd' => $itemAdicionado,
            ':Acessorios' => $acessorios
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
        @Status = '42';
    ");

    if (
        !$stmt2->execute([
            ':itemAntigo' => $itemRetirado,
            ':user' => $user
        ])
    ) {
        throw new Exception("Erro na execução da segunda consulta.");
    }

    $pdo->commit();

    echo "Alteração feita com sucesso!"/* . $itemAdicionado. ' | ' . $itemRetirado. ' | ' . $contrato . ' | ' . $acessorios */;
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erro ao alterar: " . $e->getMessage();
}


