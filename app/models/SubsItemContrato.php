<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Redireciona para login, se não estiver logado
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');

include '../../config/database.php'; 

$contrato = $_POST['contrato'] ?? '';
$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? '';

if (!$contrato || !$itemRetirado || !$itemAdicionado || $user == '') {
    echo "Dados incompletos.";
    exit;
}

try {
    $stmt = $pdo->prepare("EXEC CriarPedidoCompra 
                                        @CodigosPat = :itemAntigo,
                                        @User = :user;
                                    ");
    $stmt->execute([
        //':itemNovo' => $itemAdicionado,
        //':contrato' => $contrato,
        ':itemAntigo' => $itemRetirado,
        ':user' => $user
    ]);

    if ($stmt->rowCount()) {
        echo "Alteração feita com sucesso!";
    } else {
        echo "Nenhum item foi alterado.";
    }

} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}
