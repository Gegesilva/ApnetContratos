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


$contrato = isset($_POST['contrato']) ? $_POST['contrato'] : $_SESSION['contrato'];

$_SESSION['contrato'] = $contrato;

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
        <form action="defCliente.php" method="POST">
            <div class="header">
                <h2 class="titulo">Acessorios <?= $nome ?></h2>
                <div>
                    <button type="submit" class="btn-selec">SELECIONAR</button>
                    <button type="button" onclick="window.location.href= 'subsMaq.php'" class="btn-sair">←
                        Voltar</button>

                </div>
            </div>

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

                        $sql = "SELECT 
                                    TB02114_ACESSORIO codAcess,
                                    TB01010_NOME NomeAcess,
                                    TB02114_CODIGO Contrato,
                                    TB02114_NUMSERIE numSerieMaq,
                                    TB02114_QTPROD qtde
                                FROM TB02114
                                LEFT JOIN TB01010 ON TB01010_CODIGO = TB02114_ACESSORIO
                                WHERE TB02114_NUMSERIE = 'SPE05DZVE'";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><input class='chk-item' type='checkbox' name='selecionados[]' value='{$row['Pat']}'></td>";
                            echo "<td>{$row['codAcess']}</td>";
                            echo "<td>{$row['NomeAcess']}</td>";
                            echo "<td>{$row['Contrato']}</td>";
                            echo "<td>{$row['numSerieMaq']}</td>";
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


        </form>
    </div>
    <script src="../../public/assets/JS/defCliente.js"></script>
</body>

</html>