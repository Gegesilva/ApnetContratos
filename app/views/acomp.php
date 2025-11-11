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
                    <th class="titulo-col-tab" onclick="ordenarTabela(0)">Cliente <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(1)">Contrato <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(2)">Tipo <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(3)">PAT Saída <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(4)">PAT Entrada <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(5)">Data Pedido <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(6)">Pedido Compra <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(7)">Status Compra <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(8)">Orçamento Subs <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(9)">Orçamento Ext <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(10)">Status Orc <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(11)">Operador <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(12)">Concluído? <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(13)">Acessórios? <i class="fa fa-sort"
                            aria-hidden="true"></i></th>
                    <th class="titulo-col-tab" onclick="ordenarTabela(14)">Ação <i class="fa fa-sort"
                            aria-hidden="true"></i></th>

                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "SELECT 
                                TB01008_NOME NOME
                                ,[PAT_ENTRADA]
                                ,[PAT_SAIDA]
                                ,[PEDIDO_COMPRA]
                                ,[ORCAMENTO]
                                ,TB01039_NOME STATUS_COMPRA
                                ,[OPCAD]
                                ,[COD_CONTRATO]
                                ,CASE WHEN [CONCLUIDO_S_N] = 'S' THEN 'SIM' ELSE 'NÃO' END AS CONCLUIDO_S_N
                                ,CASE WHEN [ACESSORIOS_S_N] = 'S' THEN 'SIM' ELSE 'NÃO' END AS ACESSORIOS_S_N
                                ,[TIPO_OP]
                                ,[TB02002_CODPRE]
                                ,TB02030_ORCAMENTO OrcPed
                                ,[COD_CONTRATO_ANT]
                                ,[TB01021_NOME]
                                ,TB02030_NTFISC NOTAPEDIDO
                                ,TB02018_NTFISC NOTAORCAMENTO
                                ,FORMAT(TB02030_DTCAD, 'dd/MM/yyyy') DATAPEDIDO


                            FROM [dbo].[SUBS_CONTRATO]
                            LEFT JOIN TB02002 ON TB02002_CODPRE = PEDIDO_COMPRA
                            LEFT JOIN TB02030 ON TB02030_CODIGO = PEDIDO_COMPRA
                            LEFT JOIN TB01039 ON TB01039_CODIGO = TB02030_STATUS
                            LEFT JOIN TB02018 ON TB02018_CODIGO = ISNULL(ORCAMENTO, TB02030_ORCAMENTO)
                            LEFT JOIN TB01021 ON TB01021_CODIGO = TB02018_STATUS
                            LEFT JOIN TB02111 ON TB02111_CODIGO = COD_CONTRATO
                            LEFT JOIN TB01008 ON TB01008_CODIGO = TB02111_CODCLI

                            WHERE CONCLUIDO_S_N = 'N'";
                    $stmt = $pdo->query($sql);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['NOME']}</td>"; // Cliente
                        echo "<td>{$row['COD_CONTRATO']}</td>"; // Contrato
                        echo "<td>{$row['TIPO_OP']}</td>"; // Tipo
                        echo "<td>{$row['PAT_SAIDA']}</td>"; // PAT Saída
                        echo "<td>{$row['PAT_ENTRADA']}</td>"; // PAT Entrada
                        echo "<td>{$row['DATAPEDIDO']}</td>"; // Data Pedido
                        echo "<td>{$row['PEDIDO_COMPRA']}</td>"; // Pedido Compra
                        echo "<td>{$row['STATUS_COMPRA']}</td>"; // Status Compra
                        echo "<td>{$row['OrcPed']}</td>"; // Orçamento Subs
                        echo "<td>{$row['ORCAMENTO']}</td>"; // Orçamento Ext
                        echo "<td>{$row['TB01021_NOME']}</td>"; // Status Orc
                        echo "<td>{$row['OPCAD']}</td>"; // Operador
                        echo "<td>{$row['CONCLUIDO_S_N']}</td>"; // Concluído?
                        echo "<td>{$row['ACESSORIOS_S_N']}</td>"; // Acessórios?

                        if ($row['CONCLUIDO_S_N'] == 'NÃO' /* && $row['TB02002_CODPRE'] != NULL &&  $row['TB02030_STATUS'] = '33'*/) {
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
                                    <input type='hidden' name='COD_CONTRATO_ANT' value='{$row['COD_CONTRATO_ANT']}'>
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