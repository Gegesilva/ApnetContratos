<?php
session_start();
ini_set('max_input_vars', 3000);
error_reporting(0);
ini_set('display_errors', '0');

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');
include '../../config/database.php';

$itemRetirado = isset($_POST['itemRetirado']) ? $_POST['itemRetirado'] : $_SESSION['itemRetirado'];
$itemAdicionado = isset($_POST['itemAdicionado']) ? $_POST['itemAdicionado'] : $_SESSION['itemAdicionado'];
$patsaida =  $_POST['patsaida'];
$obsPedido = isset($_POST['obsPedido']) ? $_POST['obsPedido'] : $_SESSION['obsPedido'];

$_SESSION['obsPedido'] = $obsPedido;

echo $patsaida;

$sqlContrato = "SELECT TOP 1 TB02112_CODIGO FROM TB02112 WHERE TB02112_SITUACAO = 'A' AND TB02112_PAT = :patSaida";
$stmtContrato = $pdo->prepare($sqlContrato);
$stmtContrato->execute([
    ':patSaida' => $itemRetirado,
]);

$contrato = $stmtContrato->fetchColumn();

$_SESSION['contrato'] = $contrato;



/* Veirifica se os itens  */
// Verifica se o PAT de saida pertence ao contrato, se o PAT de entrada existe e se essa substituição ja foi iniciada.
/* $verificaSql = "SELECT COUNT(*) AS total
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

/* $total = (INT) $verificaStmt->fetchColumn();

if ($total === 0) {
    echo "<script><alert>Dados incorretos. Confira se os dados informados estão corretos.</alert></script>";
    echo "<script>window.location.href= 'subsMaq.php'</script>";
}
 
/* manda para a pagina onde inicia o processo */

/* if (empty($contrato)) {
    echo "<script>alert('Número do contrato não pode ser vazio.');</script>";
    header("Location: subsRazSocial.php");
    exit;
} */

// Pega nome do contrato anterior
$sqlNome = "SELECT TB01008_NOME FROM TB02111 LEFT JOIN TB01008 ON TB01008_CODIGO = TB02111_CODCLI WHERE TB02111_CODIGO = :Contrato";
$stmtNome = $pdo->prepare($sqlNome);
$stmtNome->execute([':Contrato' => $contrato]);
$nome = $stmtNome->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>DATABIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/CSS/acomp.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container">
        <form action="../views/subsMaq.php" method="POST">
            <div class="header">
                <h2 class="titulo">Acessorios <?= $itemRetirado ?></h2>
                <div>
                    <button type="submit" class="btn-selec">SELECIONAR</button>
                    <button type="button" onclick="window.location.href= 'subsMaq.php'" class="btn-sair2">←
                        Voltar</button>

                </div>
            </div>
            <!-- inputs para retorno  -->
             <input type="hidden" value="<?=$itemAdicionado?>" name="itemAdicionado" class="itemAdicionado">
             <input type="hidden" value="<?=$itemRetirado?>" name="itemRetirado" class="itemRetirado">
             <input type="hidden" value="<?=$contrato?>" name="contrato" class="contrato">
             <input type="hidden" value="<?=$patsaida?>" name="PAT_SAIDA" class="patsaida">
             <input type="hidden" value="<?=$obsPedido?>" name="obsPedido" class="obsPedido">

            <table id="substituicoes">
                <thead>
                    <tr>
                        <th onclick="selecionarTodos()" style="cursor: pointer;">✔</th>
                        <th class="titulo-col-tab" onclick="ordenarTabela(0)">Codigo <i class="fa fa-sort"></i></th>
                        <th class="titulo-col-tab" onclick="ordenarTabela(1)">Nome <i class="fa fa-sort"></i>
                        </th>
                        <th class="titulo-col-tab" onclick="ordenarTabela(2)">Contrato <i class="fa fa-sort"></i></th>
                        <th class="titulo-col-tab" onclick="ordenarTabela(3)">Série da Maquina <i class="fa fa-sort"></i></th>
                        <th class="titulo-col-tab" onclick="ordenarTabela(4)">Quantidade <i class="fa fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = "SELECT DISTINCT
                                    TB02114_ACESSORIO codAcess,
                                    TB01010_NOME NomeAcess,
                                    TB02114_CODIGO Contrato,
                                    TB02114_NUMSERIE NumSerieMaq,
                                    TB02114_QTPROD qtde
                                FROM TB02114
                                LEFT JOIN TB01010 ON TB01010_CODIGO = TB02114_ACESSORIO
                                LEFT JOIN TB02054 ON TB02054_NUMSERIE = TB02114_NUMSERIE
                                WHERE TB02054_PAT = :pat
                                AND TB02114_CODIGO = :contrato";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([':pat' => $itemRetirado,
                                                ':contrato' => $contrato
                        ]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><input class='chk-item' type='checkbox' name='selecionados[]' value='{$row['codAcess']}'></td>";
                            echo "<td>{$row['codAcess']}</td>";
                            echo "<td>{$row['NomeAcess']}</td>";
                            echo "<td>{$row['Contrato']}</td>";
                            echo "<td>{$row['NumSerieMaq']}</td>";
                            echo "<td>{$row['qtde']}</td>";
                            echo "</tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5'>Erro: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="contrato" value="<?= htmlspecialchars($contrato) ?>">
            <input type="hidden" name="obsPedido" value="<?= htmlspecialchars($obsPedido) ?>">

        </form>
    </div>
    <script src="../../public/assets/JS/defCliente.js"></script>
</body>

</html>