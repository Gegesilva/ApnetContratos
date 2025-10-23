<?php
session_start();

/* echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
 */

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
$vaiAcessorios = $_POST['Acessorios'];



if (empty($ContratoAnt) || empty($ContratoNov) || empty($CodCli) || empty($patFormatado) || empty($user)) {
    echo "DEBUG => ContratoAnt: '$ContratoAnt', ContratoNov: '$ContratoNov', CodCli: '$CodCli', Pats: $patFormatado, User: '$user'\n";
    echo "Dados incompletos.";
    exit;
}

// Verifica se o contrato novo já existe
$sqlCheck = "SELECT COUNT(*) FROM TB02111 WHERE TB02111_CODIGO = :ContratoNov";
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([':ContratoNov' => $ContratoNov]);
$count = $stmtCheck->fetchColumn();
if ($count > 0) {
    echo "<script>
        alert('Contrato novo já existe.');
        window.location.href = '../views/subsRazSocial.php';
    </script>";
    exit;
}

try {
    $sql = "EXEC [dbo].[SUBS_CILENTE_CONTRATO]
                @ContratoAnt = :ContratoAnt,
                @ContratoNov = :ContratoNov,
                @CodCli  = :CodCli,
                @Pats = $patFormatado,
                @User = :user,
                @Acessorios = :Acessorios;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':Acessorios' => $vaiAcessorios,
        ':user' => $user
    ]);

    //cria pedido para cliente anterior
    $stmt2 = $pdo->prepare("
        EXEC CriarPedidoCompra
        @CodigosPat = $patFormatado,
        @User = $user,
        @Operacao = '67',
        @Status = '42';
    ");
    $stmt2->execute();
        

    //cria orçamento
    $sql = "EXEC [dbo].[CRIA_ORC_SUB_CLI]
                @ContratoAnt = :ContratoAnt,
                @ContratoNov = :ContratoNov,
                @CodCli  = :CodCli,
                @Pats = $patFormatado,
                @User = :user,
                @Operacao = '63',
                @Status = 'B2';";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':user' => $user
    ]);

    //echo "Substituição realizada com sucesso.";
} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage() . "vai acessorios: " . $patFormatado;
}

echo "<script>alert('Dados alterados com sucesso.'); window.location.href = '../views/subsRazSocial.php';</script>";
exit;
