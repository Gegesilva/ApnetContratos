<?php

include '../../config/database.php';
include 'insereTabSubs.php';

ini_set('max_input_vars', 3000);
error_reporting(0);
ini_set('display_errors', '0');


$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? '';
$acessoriosSelecionados = $_POST['acessoriosSelecionados'] ?? '';

$sqlContrato = "SELECT TOP 1 TB02112_CODIGO FROM TB02112 WHERE TB02112_SITUACAO = 'A' AND TB02112_PAT = :patSaida";
$stmtContrato = $pdo->prepare($sqlContrato);
$stmtContrato->execute([
    ':patSaida' => $itemRetirado,
]);

$contrato = $stmtContrato->fetchColumn();

/* define e flag se possui acessrios */
if (isset($acessoriosSelecionados) && $acessoriosSelecionados != '') {
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
    echo "Dados incorretos. Confira se os dados informados estão corretos." . $contrato . " - " . $itemRetirado . " - " . $itemAdicionado . ' - ' . $total;
    exit;
}

// Cria pedido de compra
try {
    $stmt = $pdo->prepare("EXEC CriarPedidoCompra 
                                        @CodigosPat = :itemRetirado,
                                        @User = :user,
                                        @Operacao = '65',
	                                    @Status = '18';");
    $stmt->execute([
        ':itemRetirado' => $itemRetirado,
        ':user' => $user
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $NovPed = $result['NovPed'] ?? null;

    // depois (APENAS debug, sem mudar regras):
    $respIns = insereTabSubs($pdo, $itemRetirado, $itemAdicionado, $contrato, $user, 'S', $acessorios, NULL, NULL, NULL, NULL, $NovPed);
    error_log('[subsitemcontrato] resp insereTabSubs = ' . $respIns);
    //echo ' | RESP_SUBS=' . $respIns;  // temporário pra você ver no alert/resposta

    $respUpd = atualizaEvento($pdo, $itemRetirado);
    error_log('[UptConcluido]  = ' . $respUpd);
    //echo ' | RESP_SUBS=' . $respInsAcess;  // temporário pra você ver no alert/resposta

    if (isset($acessoriosSelecionados)) {

        $respInsAcess = insereAcessorios($pdo, $itemRetirado, $itemAdicionado, $contrato, $user, $acessoriosSelecionados);
        error_log('[subsitemcontrato] resp insereTabSubs = ' . $respInsAcess);
        //echo ' | RESP_SUBS=' . $respInsAcess;  // temporário pra você ver no alert/resposta

    }

    if ($stmt->rowCount()) {
        echo "Alteração feita com sucesso! ";
    } else {
        echo "Nenhum item foi alterado.";
    }

} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}
