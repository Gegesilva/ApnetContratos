<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');

function insereTabSubs($pdo, $itemAntigo, $itemNovo, $contrato, $user, $tipo)
{
    try {

        if ($tipo == 'S') {
            $stmt = $pdo->prepare("INSERT INTO [dbo].[SUBS_CONTRATO]
                                                        ([PAT_ENTRADA]
                                                        ,[PAT_SAIDA]
                                                        ,[PEDIDO_COMPRA]
                                                        ,[ORCAMENTO]
                                                        ,[STATUS_COMPRA]
                                                        ,[OPCAD]
                                                        ,[COD_CONTRATO]
                                                        ,[CONCLUIDO_S_N]
                                                        ,[TIPO_OP])
                                                    VALUES
                                                        (:itemNovo
                                                        ,:itemAntigo
                                                        ,(SELECT TOP 1 
                                                                TB02030_CODIGO FROM TB02030 
                                                          WHERE TB02030_CONTRATO = :contrato2
                                                            AND EXISTS(SELECT * FROM TB02112 WHERE TB02112_CODIGO = :contrato3 AND TB02112_PAT = :itemAntigo2) ORDER BY TB02030_DTCAD DESC)
                                                        ,NULL
                                                        ,NULL
                                                        ,:user
                                                        ,:contrato
                                                        ,'N'
                                                        ,'SubsItemContrato')");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemAntigo2' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':contrato' => $contrato,
                ':contrato2' => $contrato,
                ':contrato3' => $contrato,
                ':user' => $user
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO [dbo].[SUBS_CONTRATO]
                                                        ([PAT_ENTRADA]
                                                        ,[PAT_SAIDA]
                                                        ,[PEDIDO_COMPRA]
                                                        ,[ORCAMENTO]
                                                        ,[STATUS_COMPRA]
                                                        ,[OPCAD]
                                                        ,[COD_CONTRATO]
                                                        ,[CONCLUIDO_S_N]
                                                        ,[TIPO_OP])
                                                    VALUES
                                                        (:itemNovo
                                                        ,:itemAntigo
                                                        ,(SELECT TOP 1 
                                                                TB02030_CODIGO FROM TB02030 
                                                          WHERE TB02030_CONTRATO = :contrato2
                                                            AND EXISTS(SELECT * FROM TB02112 WHERE TB02112_CODIGO = :contrato3 AND TB02112_PAT = :itemAntigo2) ORDER BY TB02030_DTCAD DESC)
                                                        ,(SELECT 
                                                                STRING_AGG(TB02019_CODIGO, ',')
                                                            FROM TB02019 
                                                            LEFT JOIN TB02054 ON TB02054_NUMSERIE = TB02019_NUMSERIE
                                                            WHERE TB02054_PAT IN (:itemNovo3,:itemAntigo3))
                                                        ,NULL
                                                        ,:user
                                                        ,:contrato
                                                        ,'N'
                                                        ,'SubsItemContratoExtravio')");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemAntigo2' => $itemAntigo,
                ':itemAntigo3' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':itemNovo2' => $itemNovo,
                ':itemNovo3' => $itemNovo,
                ':contrato' => $contrato,
                ':user' => $user
            ]);

        }

        if ($stmt->rowCount()) {
            return "Alteração feita com sucesso!";
        } else {
            return "Nenhum item foi alterado.";
        }

    } catch (PDOException $e) {
        return "Erro ao alterar: " . $e->getMessage();
    }
}

/* sub query para pegar utlitmo orçamento
    (SELECT 
        STRING_AGG(TB02019_CODIGO, ',')
    FROM TB02019 
    LEFT JOIN TB02054 ON TB02054_NUMSERIE = TB02019_NUMSERIE
    WHERE TB02054_PAT IN (:itemNovo2,:itemAntigo2))

*/