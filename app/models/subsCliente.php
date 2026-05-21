<?php
session_start();
include 'InsereTabSubs.php';

ini_set('max_input_vars', 3000);
error_reporting(0);
ini_set('display_errors', '0');

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
$SituacaoContratoAnt = $_POST['SituacaoContratoAnt'];
$ContratoExistente = $_POST['ContratoExistente'];
$obsPedido = $_POST['obsPedido'];



if ($ContratoExistente === "N") {
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

} else if ($ContratoExistente === "S") {
    // Pega o cliente do contrato novo para usar na substituição
    $sqlCheck = "SELECT TB02111_CODCLI Cliente FROM TB02111 WHERE TB02111_CODIGO = :ContratoNov";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':ContratoNov' => $ContratoNov]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $CodCli = $result['Cliente'];
}

if (empty($ContratoAnt) || empty($ContratoNov) || empty($CodCli) || empty($patFormatado) || empty($user)) {
    echo "DEBUG => ContratoAnt: '$ContratoAnt', ContratoNov: '$ContratoNov', CodCli: '$CodCli', Pats: $patFormatado, User: '$user'\n";
    echo "Dados incompletos.";
    exit;
}

try {


    //cria pedido para cliente anterior
    $stmt2 = $pdo->prepare("
        EXEC CriarPedidoCompra
        @CodigosPat = $patFormatado,
        @User = '$user',
        @Operacao = '67',
        @Status = '42',
        @Obs = '$obsPedido';
    ");
    $stmt2->execute();
    $result = $stmt2->fetch(PDO::FETCH_ASSOC);
    $NovPed = $result['NovPed'] ?? null;


   /*  echo 'NovPed: ' . $NovPed;
    echo 'ObsPedido: ' . $obsPedido; */

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
        ':ContratoAnt' => $ContratoAnt,//
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':user' => $user
    ]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $novoOrcamento = $result[0]['NovoOrcamento'] ?? null;


    $sql = "EXEC [dbo].[SUBS_CILENTE_CONTRATO]
                @ContratoAnt = :ContratoAnt,
                @ContratoNov = :ContratoNov,
                @CodCli  = :CodCli,
                @Pats = $patFormatado,
                @User = :user,
                @Acessorios = :Acessorios,
                @SituacaoContratoAnt = :SituacaoContratoAnt,
                @ContratoExistente = :ContratoExistente;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':Acessorios' => $vaiAcessorios,
        ':user' => $user,
        ':SituacaoContratoAnt' => $SituacaoContratoAnt,
        ':ContratoExistente' => $ContratoExistente
    ]);


    //echo "Substituição realizada com sucesso.";
} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}

$respIns = insereTabSubs($pdo, NULL, NULL, $ContratoNov, $user, 'S', $vaiAcessorios, $patFormatado, $ContratoAnt, $CodCli, null, $NovPed);
error_log('[subsitemcontrato] resp insereTabSubs = ' . $respIns);
//echo ' | RESP_SUBS=' . $respIns;  // temporário pra você ver no alert/resposta

//echo "<script>alert('Dados alterados com sucesso.". $patFormatado . "');</script>";
echo "<script>alert('Dados alterados com sucesso.'); window.location.href = '../views/subsRazSocial.php';</script>";
exit;



//echo 'ContratoNov: ' . $ContratoNov, 'ContratoAnt: ' . $ContratoAnt, 'CodCli: ' . $CodCli, 'Pats: ' . $patFormatado, 'User: ' . $user;
