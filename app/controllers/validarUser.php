<?php
session_start();

include '../../config/config.php';
include '../../config/database.php';

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

try {


    // Busca usuário
    $stmt = $pdo->prepare("SELECT TB01066_USUARIO Usuario,TB01066_SENHA Senha FROM TB01066 WHERE TB01066_SENHA = :senha AND TB01066_USUARIO = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['Usuario'] != NULL) {
        $_SESSION['usuario'] = $user['Usuario'];
        $_SESSION['senha'] = $user['Senha'];
        header("Location: ../views/menu.php");
        exit;
    } else {
        echo "<script>window.alert('Usuario e/ou senha invalidos!')</script>";
        echo "<script>location.href='../../public/login.php'</script>";
    }

} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
