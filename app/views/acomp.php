<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');
include '../../config/database.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>DATABIT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../public/assets/CSS/acomp.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Acompanhamento</h2>
            <button type="button" onclick="window.location.href= 'menu.php'" class="btn-sair">← Voltar</button>
        </div>
        <table id="substituicoes">
            <thead>
                <tr>
                    <th class="titulo-col-tab" onclick="ordenarTabela(0)">PAT Saída <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(1)">PAT Entrada <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(2)">Pedido Compra <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(3)">Orçamento Ext <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(4)">Orçamento Subs<i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(5)">Status Compra <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(6)">Operador <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(7)">Contrato <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(8)">Concluído? <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(9)">Tipo <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(10)">Acessorios? <i class="fa fa-sort" aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(11)">Ação <i class="fa fa-sort" aria-hidden="true"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "SELECT [PAT_ENTRADA]
                                ,[PAT_SAIDA]
                                ,[PEDIDO_COMPRA]
                                ,[ORCAMENTO]
                                ,[STATUS_COMPRA]
                                ,[OPCAD]
                                ,[COD_CONTRATO]
                                ,CASE WHEN [CONCLUIDO_S_N] = 'S' THEN 'SIM' ELSE 'NÃO' END AS CONCLUIDO_S_N
                                ,CASE WHEN [ACESSORIOS_S_N] = 'S' THEN 'SIM' ELSE 'NÃO' END AS ACESSORIOS_S_N
                                ,[TIPO_OP]
                                ,[TB02002_CODPRE]
                                ,TB02030_ORCAMENTO OrcPed

                            FROM [dbo].[SUBS_CONTRATO]
                            LEFT JOIN TB02002 ON TB02002_CODPRE = PEDIDO_COMPRA
                            LEFT JOIN TB02030 ON TB02030_CODIGO = PEDIDO_COMPRA
                            WHERE CONCLUIDO_S_N = 'N'";
                    $stmt = $pdo->query($sql);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['PAT_SAIDA']}</td>";
                        echo "<td>{$row['PAT_ENTRADA']}</td>";
                        echo "<td>{$row['PEDIDO_COMPRA']}</td>";
                        echo "<td>{$row['ORCAMENTO']}</td>";
                        echo "<td>{$row['OrcPed']}</td>";
                        echo "<td>{$row['STATUS_COMPRA']}</td>";
                        echo "<td>{$row['OPCAD']}</td>";
                        echo "<td>{$row['COD_CONTRATO']}</td>";
                        echo "<td>{$row['TIPO_OP']}</td>";
                        echo "<td>{$row['CONCLUIDO_S_N']}</td>";
                        echo "<td>{$row['ACESSORIOS_S_N']}</td>";

                        if ($row['CONCLUIDO_S_N'] == 'NÃO' /* && $row['TB02002_CODPRE'] != NULL */) {
                            echo "<td>
                                <form method='POST' action='../models/concluir.php'>
                                    <input type='hidden' name='PAT_SAIDA' value='{$row['PAT_SAIDA']}'>
                                    <input type='hidden' name='PAT_ENTRADA' value='{$row['PAT_ENTRADA']}'>
                                    <input type='hidden' name='PEDIDO_COMPRA' value='{$row['PEDIDO_COMPRA']}'>
                                    <input type='hidden' name='ORCAMENTO' value='{$row['ORCAMENTO']}'>
                                    <input type='hidden' name='STATUS_COMPRA' value='{$row['STATUS_COMPRA']}'>
                                    <input type='hidden' name='OPCAD' value='{$row['OPCAD']}'>
                                    <input type='hidden' name='COD_CONTRATO' value='{$row['COD_CONTRATO']}'>
                                    <input type='hidden' name='CONCLUIDO_S_N' value='{$row['CONCLUIDO_S_N']}'>
                                    <input type='hidden' name='TIPO_OP' value='{$row['TIPO_OP']}'>
                                    <input type='hidden' name='ACESSORIOS_S_N' value='{$row['ACESSORIOS_S_N']}'>
                                    <button type='submit' class='btn-nova'>Concluir</button>
                                </form>
                              </td>";
                        } else {
                            echo "<td></td>";
                        }

                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='10'>Erro ao conectar ao banco: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="../../public/assets/JS/script.js"></script>
</body>

</html>
