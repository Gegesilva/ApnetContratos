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
    <link rel="stylesheet" href="../../public/assets/CSS/acomp.css">
</head>

<body>

    <body>
        <div class="container">
            <div class="header">
                <h2>Acompanhamento</h2>
                <a href="menu.php" class="btn-sair">VOLTAR</a>
            </div>
            <table id="substituicoes">
                <thead>
                    <tr>
                        <th>PAT Entrada</th>
                        <th>PAT Saída</th>
                        <th>Pedido Compra</th>
                        <th>Orçamento</th>
                        <th>Status Compra</th>
                        <th>Operador</th>
                        <th>Contrato</th>
                        <th>Concluído?</th>
                        <th>Tipo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    try {
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = "SELECT [PAT_ENTRADA], [PAT_SAIDA], [PEDIDO_COMPRA], [ORCAMENTO], [STATUS_COMPRA], 
                   [OPCAD], [COD_CONTRATO], [CONCLUIDO_A_N], [TIPO_OP]
            FROM [dbo].[SUBS_CONTRATO]";
                        $stmt = $pdo->query($sql);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>{$row['PAT_ENTRADA']}</td>";
                            echo "<td>{$row['PAT_SAIDA']}</td>";
                            echo "<td>{$row['PEDIDO_COMPRA']}</td>";
                            echo "<td>{$row['ORCAMENTO']}</td>";
                            echo "<td>{$row['STATUS_COMPRA']}</td>";
                            echo "<td>{$row['OPCAD']}</td>";
                            echo "<td>{$row['COD_CONTRATO']}</td>";
                            echo "<td>{$row['CONCLUIDO_A_N']}</td>";
                            echo "<td>{$row['TIPO_OP']}</td>";

                            // Verifica se o campo PEDIDO_COMPRA está preenchido
                            if (!empty($row['PEDIDO_COMPRA'])) {
                                echo "<td>
                    <form method='POST' action='concluir_substituicao.php'>
                        <input type='hidden' name='PAT_ENTRADA' value='{$row['PAT_ENTRADA']}'>
                        <input type='hidden' name='PAT_SAIDA' value='{$row['PAT_SAIDA']}'>
                        <input type='hidden' name='PEDIDO_COMPRA' value='{$row['PEDIDO_COMPRA']}'>
                        <input type='hidden' name='ORCAMENTO' value='{$row['ORCAMENTO']}'>
                        <input type='hidden' name='STATUS_COMPRA' value='{$row['STATUS_COMPRA']}'>
                        <input type='hidden' name='OPCAD' value='{$row['OPCAD']}'>
                        <input type='hidden' name='COD_CONTRATO' value='{$row['COD_CONTRATO']}'>
                        <input type='hidden' name='CONCLUIDO_A_N' value='{$row['CONCLUIDO_A_N']}'>
                        <input type='hidden' name='TIPO_OP' value='{$row['TIPO_OP']}'>
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

        <script>
            fetch('buscar_substituicoes.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#substituicoes tbody');
                    data.forEach(linha => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
            <td data-label="PAT Entrada">${linha.PAT_ENTRADA}</td>
            <td data-label="PAT Saída">${linha.PAT_SAIDA}</td>
            <td data-label="Pedido Compra">${linha.PEDIDO_COMPRA ?? ''}</td>
            <td data-label="Orçamento">${linha.ORCAMENTO ?? ''}</td>
            <td data-label="Status Compra">${linha.STATUS_COMPRA ?? ''}</td>
            <td data-label="Operador">${linha.OPCAD}</td>
            <td data-label="Contrato">${linha.COD_CONTRATO}</td>
            <td data-label="Concluído?">${linha.CONCLUIDO_A_N}</td>
            <td data-label="Tipo">${linha.TIPO_OP}</td>
            <td data-label="Ação">
              ${linha.PEDIDO_COMPRA ? `
                <form method="POST" action="concluir_substituicao.php">
                  <input type="hidden" name="PAT_ENTRADA" value="${linha.PAT_ENTRADA}">
                  <input type="hidden" name="PAT_SAIDA" value="${linha.PAT_SAIDA}">
                  <input type="hidden" name="PEDIDO_COMPRA" value="${linha.PEDIDO_COMPRA}">
                  <input type="hidden" name="ORCAMENTO" value="${linha.ORCAMENTO}">
                  <input type="hidden" name="STATUS_COMPRA" value="${linha.STATUS_COMPRA}">
                  <input type="hidden" name="OPCAD" value="${linha.OPCAD}">
                  <input type="hidden" name="COD_CONTRATO" value="${linha.COD_CONTRATO}">
                  <input type="hidden" name="CONCLUIDO_A_N" value="${linha.CONCLUIDO_A_N}">
                  <input type="hidden" name="TIPO_OP" value="${linha.TIPO_OP}">
                  <button type="submit" class="concluir">Concluir</button>
                </form>
              ` : ''}
            </td>
          `;
                        tbody.appendChild(tr);
                    });
                });
        </script>


    </body>

</html>