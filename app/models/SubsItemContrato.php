<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');

include '../../config/database.php'; 
include 'insereTabSubs.php';

$contrato = $_POST['contrato'] ?? '';
$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? '';

if (!$contrato || !$itemRetirado || !$itemAdicionado || $user == '') {
    echo "Dados incompletos.";
    exit;
}

// Verifica se os dois PATs pertencem ao contrato informado
$verificaSql = "SELECT COUNT(*) AS total
                FROM TB02112
                WHERE TB02112_PAT IN (:itemRetirado, :itemAdicionado)
                  AND TB02112_CODIGO = :contrato";

$verificaStmt = $pdo->prepare($verificaSql);
$verificaStmt->execute([
    ':itemRetirado'   => $itemRetirado,
    ':itemAdicionado' => $itemAdicionado,
    ':contrato'       => $contrato
]);

$total = $verificaStmt->fetchColumn();

if ($total < 2) {
    echo "Os itens não pertencem ao contrato informado.";
    exit;
}

// Cria pedido de compra
try {
    $stmt = $pdo->prepare("EXEC CriarPedidoCompra 
                                        @CodigosPat = :itemAntigo,
                                        @User = :user;");
    $stmt->execute([
        ':itemAntigo' => $itemRetirado,
        ':user'       => $user
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
