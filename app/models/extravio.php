<?php

include '../../config/database.php';
include 'insereTabSubs.php';

$user = $_SESSION['usuario'];

$contrato = $_POST['contrato'] ?? '';
$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? '';

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

// Cria orçamento
try {
    $stmt = $pdo->prepare("
                            EXEC CRIA_ORC
                                @Pat = :itemAntigo,
                                @Contrato = :Contrato,
                                @User = :user,
                                @SituacaoContrato = 'I';
");
    $stmt->execute([
        ':itemAntigo' => $itemRetirado,
        ':Contrato' => $contrato,
        ':user' => $user
    ]);

    $stmt2 = $pdo->prepare("
                            EXEC CRIA_ORC
                                @Pat = :itemNovo,
                                @Contrato = :Contrato,
                                @User = :user,
                                @SituacaoContrato = 'A';
");
    $stmt2->execute([
        ':itemNovo' => $itemAdicionado,
        ':Contrato' => $contrato,
        ':user' => $user
    ]);


    insereTabSubs($pdo, $itemRetirado, $itemAdicionado, $contrato, $user);

    if ($stmt->rowCount()) {
        echo "Alteração feita com sucesso!";
    } else {
        echo "Nenhum item foi alterado.";
    }

} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}
