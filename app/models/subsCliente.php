<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patArray'])) {
    $_SESSION['pats_selecionados'] = json_decode($_POST['patArray'], true);
}

// Recupera valores da sessão ou inicializa vazio
$patArray = isset($_SESSION['pats_selecionados']) ? $_SESSION['pats_selecionados'] : [];

// Formata como string para uso no SQL
$patFormatado = '';
if (!empty($patArray)) {
    $patFormatado = "'" . implode(',', $patArray) . "'";
}

header('Content-type: text/html; charset=ISO-8859-1');
include '../../config/database.php';

$ContratoAnt = $_POST['contratoAnt'];
$ContratoNov = $_POST['contrato'];
$CodCli = $_POST['cliente'];




if (empty($ContratoAnt) || empty($ContratoNov) || empty($CodCli) || empty($patFormatado) || empty($user)) {
    //echo "DEBUG => ContratoAnt: '$ContratoAnt', ContratoNov: '$ContratoNov', CodCli: '$CodCli', Pats: $patFormatado, User: '$user'\n";
    echo "Dados incompletos.";
    exit;
}

try {
    $sql = "EXEC [dbo].[SUBS_CILENTE_CONTRATO]
                @ContratoAnt = :ContratoAnt,
                @ContratoNov = :ContratoNov,
                @CodCli  = :CodCli,
                @Pats = $patFormatado,
                @User = :user;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        //':Pats' => $patFormatado,
        ':user' => $user
    ]);

    //echo "Substituição realizada com sucesso.";
} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}

echo "<script>alert('Dados alterados com sucesso.'); window.location.href = '../views/selecEquips.php';</script>";
exit;
