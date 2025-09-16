<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');

function insereTabSubs($pdo, $itemAntigo, $itemNovo, $contrato, $user, $tipo, $acessorios)
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
                                                ,[TIPO_OP]
                                                ,[ACESSORIOS_S_N]
                                                ,[DTCAD])
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
                                                ,'SubsItemContrato'
                                                ,:acessorios
                                                ,GETDATE())");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemAntigo2' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':contrato' => $contrato,
                ':contrato2' => $contrato,
                ':contrato3' => $contrato,
                ':user' => $user,
                ':acessorios' => $acessorios
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
                                                ,[TIPO_OP]
                                                ,[ACESSORIOS_S_N]
                                                ,[DTCAD])
                                            VALUES
                                                (:itemNovo
                                                ,:itemAntigo
                                                ,(SELECT TOP 1 
                                                        TB02030_CODIGO FROM TB02030 
                                                    WHERE TB02030_CONTRATO = :contrato2
                                                    AND EXISTS(SELECT * FROM TB02112 WHERE TB02112_CODIGO = :contrato3 AND TB02112_PAT = :itemAntigo2) ORDER BY TB02030_DTCAD DESC)
                                                ,(SELECT TOP 1
                                                        TB02019_CODIGO
                                                    FROM TB02019 
                                                    LEFT JOIN TB02018 ON TB02018_CODIGO = TB02019_CODIGO
                                                    LEFT JOIN TB02054 ON TB02054_NUMSERIE = TB02019_NUMSERIE
                                                    WHERE TB02018_CONTRATO = :contrato4 AND TB02054_PAT = :itemAntigo3
                                                    ORDER BY TB02019_DTCAD DESC)
                                                ,NULL
                                                ,:user
                                                ,:contrato
                                                ,'N'
                                                ,'SubsItemContratoExtravio'
                                                ,:acessorios
                                                ,GETDATE())");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemAntigo2' => $itemAntigo,
                ':itemAntigo3' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':contrato' => $contrato,
                ':contrato2' => $contrato,
                ':contrato3' => $contrato,
                ':contrato4' => $contrato,
                ':user' => $user,
                ':acessorios' => $acessorios
            ]);

        }

        if ($stmt->rowCount()) {
            return "Alteração feita com sucesso! na função";
        } else {
            return "Nenhum item foi alterado.";
        }

    } catch (PDOException $e) {
        return "Erro ao alterar: " . $e->getMessage();
    }
}


function insereAcessorios($pdo, $itemAntigo, $itemNovo, $contrato, $user, $acessorios)
{
    try {

            $stmt = $pdo->prepare("INSERT INTO [dbo].[SUBS_CONTRATO_ACESSORIOS]
                                                            ([DTCAD]
                                                            ,[OPCAD]
                                                            ,[DTALT]
                                                            ,[OPALT]
                                                            ,[CONTRATO]
                                                            ,[PRODUTO]
                                                            ,[NUMSERIE]
                                                            ,[CODACESSORIO]
                                                            ,[QTPROD]
                                                            ,[OBS]
                                                            ,[NUMSERIEACESSORIO])
                                                        SELECT DISTINCT
                                                                GETDATE(),	--[DTCAD]
                                                                '$user',	--[OPCAD]
                                                                NULL,	--[DTALT]
                                                                NULL,	--[OPALT]
                                                                TB02114_CODIGO,	--[CONTRATO]
                                                                TB02054_PRODUTO,	--[PRODUTO]
                                                                TB02114_NUMSERIE,	--[NUMSERIE]
                                                                TB02114_ACESSORIO,	--[CODACESSORIO]
                                                                TB02114_QTPROD,	--[QTPROD]
                                                                'App Subs Contrato',	--[OBS]
                                                                TB02114_NUMSERIEACESSORIO	--[NUMSERIEACESSORIO]
                                                            FROM TB02114
                                                            LEFT JOIN TB01010 ON TB01010_CODIGO = TB02114_ACESSORIO
                                                            LEFT JOIN TB02054 ON TB02054_NUMSERIE = TB02114_NUMSERIE
                                                            WHERE TB02054_PAT = :pat
                                                            AND TB02114_CODIGO = :contrato
");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemAntigo2' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':contrato' => $contrato,
                ':contrato2' => $contrato,
                ':contrato3' => $contrato,
                ':user' => $user,
                ':acessorios' => $acessorios
            ]);

        if ($stmt->rowCount()) {
            return "Alteração feita com sucesso! na função";
        } else {
            return "Nenhum item foi alterado.";
        }

    } catch (PDOException $e) {
        return "Erro ao alterar: " . $e->getMessage();
    }
}

