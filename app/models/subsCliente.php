<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
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
$patArray = $_SESSION['pats_selecionados'] ?? [];
$patArray = array_values(array_unique(array_filter(array_map(
    static function ($pat) {
        return is_scalar($pat) ? trim((string) $pat) : '';
    },
    is_array($patArray) ? $patArray : []
))));

// Formata como string para uso no SQL
$patFormatado = implode(',', $patArray);
$patsRazRegistro = $patFormatado === '' ? '' : "'{$patFormatado}'";

header('Content-type: text/html; charset=UTF-8');
include '../../config/database.php';

$ContratoAnt = trim((string) ($_POST['contratoAnt'] ?? ''));
$ContratoNov = trim((string) ($_POST['contrato'] ?? ''));
$CodCli = trim((string) ($_POST['cliente'] ?? ''));
$vaiAcessorios = trim((string) ($_POST['Acessorios'] ?? 'N'));
$SituacaoContratoAnt = trim((string) ($_POST['SituacaoContratoAnt'] ?? ''));
$ContratoExistente = trim((string) ($_POST['ContratoExistente'] ?? ''));
$obsInformada = trim((string) ($_POST['obsPedido'] ?? ''));

if ($obsInformada === '') {
    $obsPedido = 'Substituição de contrato - sem observação adicional';
} else {
    $obsPedido = $obsInformada;
}
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

} elseif ($ContratoExistente === "S") {
    // Pega o cliente do contrato novo para usar na substituição
    $sqlCheck = "SELECT TB02111_CODCLI Cliente FROM TB02111 WHERE TB02111_CODIGO = :ContratoNov";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':ContratoNov' => $ContratoNov]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $CodCli = $result['Cliente'];

    if(empty($CodCli)) {
        echo "<script>
        alert('Contrato não encontrado.');
        window.location.href = '../views/subsRazSocial.php';
    </script>";
        exit;
    }

} else {
    echo "<script>alert('Opção de contrato inválida.'); window.location.href = '../views/subsRazSocial.php';</script>";
    exit;
}

if (empty($ContratoAnt) || empty($ContratoNov) || empty($CodCli) || empty($patFormatado) || empty($user)) {
    echo "Dados incompletos.";
    exit;
}

/**
 * Localiza uma coluna retornada por uma procedure mesmo quando o driver SQL
 * Server expõe rowsets intermediários.
 */
function obterRetornoProcedure(PDOStatement $stmt, string $coluna)
{
    do {
        if ($stmt->columnCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (array_key_exists($coluna, $row)) {
                    return $row[$coluna];
                }
            }
        }
    } while ($stmt->nextRowset());

    return null;
}

try {
    $pdo->beginTransaction();

    //cria pedido para cliente anterior
    $stmt2 = $pdo->prepare("
        EXEC [dbo].[CriarPedidoCompra]
        @CodigosPat = :CodigosPat,
        @User = :UsuarioPedido,
        @Operacao = '67',
        @Status = '42',
        @Obs = :ObservacaoPedido,
        @Acessorios = :AcessoriosPedido;
    ");
    $stmt2->execute([
        ':CodigosPat' => $patFormatado,
        ':UsuarioPedido' => $user,
        ':ObservacaoPedido' => $obsPedido,
        ':AcessoriosPedido' => $vaiAcessorios,
    ]);
    $NovPed = obterRetornoProcedure($stmt2, 'NovPed');
    $stmt2->closeCursor();

    if (empty($NovPed)) {
        throw new RuntimeException('CriarPedidoCompra não retornou o código do pedido.');
    }

    // Impede que uma falha silenciosa na TB02031 gere substituição parcial.
    $stmtValidaPedido = $pdo->prepare("
        SELECT
            (SELECT COUNT(*) FROM [dbo].[TB02030] WHERE TB02030_CODIGO = :PedidoCabecalho) AS Cabecalhos,
            (SELECT COUNT(*) FROM [dbo].[TB02031] WHERE TB02031_CODIGO = :PedidoItens) AS Itens
    ");
    $stmtValidaPedido->execute([
        ':PedidoCabecalho' => $NovPed,
        ':PedidoItens' => $NovPed,
    ]);
    $pedidoCriado = $stmtValidaPedido->fetch(PDO::FETCH_ASSOC);
    $stmtValidaPedido->closeCursor();

    if ((int) ($pedidoCriado['Cabecalhos'] ?? 0) !== 1 || (int) ($pedidoCriado['Itens'] ?? 0) < 1) {
        throw new RuntimeException("O pedido {$NovPed} foi gerado sem itens na TB02031.");
    }

    //cria orçamento
    $sql = "EXEC [dbo].[CRIA_ORC_SUB_CLI]
                @ContratoAnt = :ContratoAnt,
                @ContratoNov = :ContratoNov,
                @CodCli  = :CodCli,
                @Pats = :PatsOrcamento,
                @User = :UsuarioOrcamento,
                @Operacao = '63',
                @Status = 'B2';";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,//
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':PatsOrcamento' => $patFormatado,
        ':UsuarioOrcamento' => $user,
    ]);
    $novoOrcamento = obterRetornoProcedure($stmt, 'NovoOrcamento');
    $stmt->closeCursor();

    if (empty($novoOrcamento)) {
        throw new RuntimeException('CRIA_ORC_SUB_CLI não retornou o código do orçamento.');
    }

    $sql = "EXEC [dbo].[SUBS_CILENTE_CONTRATO]
                @ContratoAnt = :ContratoAnt,
                @ContratoNov = :ContratoNov,
                @CodCli  = :CodCli,
                @Pats = :PatsSubstituicao,
                @User = :UsuarioSubstituicao,
                @Acessorios = :Acessorios,
                @SituacaoContratoAnt = :SituacaoContratoAnt,
                @ContratoExistente = :ContratoExistente;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ContratoAnt' => $ContratoAnt,
        ':ContratoNov' => $ContratoNov,
        ':CodCli' => $CodCli,
        ':PatsSubstituicao' => $patFormatado,
        ':Acessorios' => $vaiAcessorios,
        ':UsuarioSubstituicao' => $user,
        ':SituacaoContratoAnt' => $SituacaoContratoAnt,
        ':ContratoExistente' => $ContratoExistente
    ]);
    $stmt->closeCursor();

    $respIns = insereTabSubs(
        $pdo,
        null,
        null,
        $ContratoNov,
        $user,
        'S',
        $vaiAcessorios,
        $patsRazRegistro,
        $ContratoAnt,
        $CodCli,
        null,
        $NovPed,
        true
    );
    error_log('[subsRazSocial] ' . $respIns . ' Pedido=' . $NovPed);

    $pdo->commit();

    echo "<script>alert('Dados alterados com sucesso. Pedido: {$NovPed}'); window.location.href = '../views/subsRazSocial.php';</script>";
    exit;
} catch (Throwable $e) {
    try {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    } catch (Throwable $rollbackError) {
        error_log('[subsRazSocial] Falha adicional no rollback: ' . $rollbackError->getMessage());
    }

    $mensagem = json_encode(
        $e->getMessage(),
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    );
    echo "<script>alert({$mensagem}); window.location.href = '../views/subsRazSocial.php';</script>";
    exit;
}
