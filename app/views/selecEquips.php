<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');
include '../../config/database.php';


$contrato = $_POST['contrato'];
if (empty($contrato)) {
    echo "<script>alert('Número do contrato não pode ser vazio.');</script>";
    header("Location: subsRazSocial.php");
    exit;
}
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
            <h2>Equipamentos</h2>
            <div>
                <input type="hidden" name="contrato" value="<?= htmlspecialchars($contrato) ?>">
                <button type="submit" class="btn-sair">SELECIONAR</button>
                <a href="subsRazSocial.php" class="btn-sair">VOLTAR</a>
            </div>
        </div>

        <table id="substituicoes">
            <thead>
                <tr>
                    <th onclick="selecionarTodos()" style="cursor: pointer;">✔</th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(0)">Patrimônio <i class="fa fa-sort"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(1)">Cod Produto <i class="fa fa-sort"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(2)">Serie <i class="fa fa-sort"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(3)">Descrição <i class="fa fa-sort"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "SELECT 
                                TB02112_PAT Pat,
                                TB02112_PRODUTO Produto,
                                TB02112_NUMSERIE Serie,
                                TB01010_NOME DescProd
                            FROM TB02112 
                            LEFT JOIN TB01010 ON TB01010_CODIGO = TB02112_PRODUTO
                            WHERE TB02112_SITUACAO = 'A'
                            AND TB02112_CODIGO = :contrato";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':contrato' => $contrato]);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td><input class='chk-item' type='checkbox' name='selecionados[]' value='{$row['Pat']}'></td>";
                        echo "<td>{$row['Pat']}</td>";
                        echo "<td>{$row['Produto']}</td>";
                        echo "<td>{$row['Serie']}</td>";
                        echo "<td>{$row['DescProd']}</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='5'>Erro: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
</div>
<script src="../../public/assets/JS/defCliente.js"></script>
</body>

</html>
