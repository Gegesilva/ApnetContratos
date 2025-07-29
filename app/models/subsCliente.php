<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patArray'])) {
    $_SESSION['pats_selecionados'] = $_POST['patArray'];

}

// Verifica se há pats selecionados em sessão
$patArray = isset($_SESSION['pats_selecionados']) ? $_SESSION['pats_selecionados'] : [];

// Transforma o array em string: ('009677, 023650, ...')
$patFormatado = '';
if (!empty($patArray)) {
    $patFormatado = "'" . implode(',', $patArray) . "'";
    
}

header('Content-type: text/html; charset=ISO-8895-1');
include '../../config/database.php';

$ContratoAnt = $_POST['contratoAnt'];
$ContratoNov = $_POST['contrato'];
$CodCli = $_POST['cliente'];

if (!$ContratoAnt || !$ContratoNov || !$CodCli || !$patFormatado || $user == '') {
   /*  if (!empty($patArray)) {
        echo 'Array preenchido';
    } else {
        echo 'Array vazio';
    } */

    echo $ContratoAnt . ' - ' . $ContratoNov . ' - ' . $CodCli . ' - ' . $patFormatado . ' - ' . $user;
    echo "Dados incompletos.";
   
    exit;
}

try {
    $sql = "EXEC [dbo].[SUBS_CILENTE_CONTRATO]
                            @ContratoAnt = :ContratoAnt,
                            @ContratoNov = :ContratoNov,
                            @CodCli  = :CodCli,
                            @Pats = :Pats,
                            @User = :user;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':Pats' => $patFormatado,
        ':user' => $user
    ]);


} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}
