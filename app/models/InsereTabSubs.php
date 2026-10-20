<?php
header('Content-type: text/html; charset=ISO-8859-1');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];


//echo "<script>console.log('Debug Objects: " . $patrimonioAcessorios . "' );</script>";

function insereTabSubs($pdo, $itemAntigo, $itemNovo, $contrato, $user, $tipo, $acessorios, $patsRaz, $contratoAnt, $codCli, $Orcamento, $novPed, $lancarErro = false)
{
    try {
        $tipoInsert = isset($contratoAnt) ? 'SubsRazSocial' : 'SubsItemContrato';
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
                                        ,[DTCAD]
                                        ,PATS_RAZ
                                        ,COD_CONTRATO_ANT
                                        ,CODCLI)
                                    VALUES
                                        (:itemNovo
                                        ,:itemAntigo
                                        ,:Pedido
                                        ,:Orcamento
                                        ,NULL
                                        ,:user
                                        ,:contrato
                                        ,'N'
                                        ,:tipoInsert
                                        ,:acessorios
                                        ,GETDATE()
                                        ,:patsRaz
                                        ,:contratoAnt
                                        ,:codcli)");
            $stmt->execute([
                ':tipoInsert' => $tipoInsert,
                ':itemAntigo' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':contrato' => $contrato,
                ':user' => $user,
                ':acessorios' => $acessorios,
                ':patsRaz' => $patsRaz,
                ':contratoAnt' => $contratoAnt,
                ':codcli' => $codCli,
                ':Orcamento' => $Orcamento,
                ':Pedido' => $novPed
            ]);
        } else if ($tipo == 'ES') {
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
                                        ,:Pedido
                                        ,:Orcamento
                                        ,NULL
                                        ,:user
                                        ,:contrato
                                        ,'N'
                                        ,'Extravio (saída)'
                                        ,:acessorios
                                        ,GETDATE())");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemNovo' => '-',
                ':contrato' => $contrato,
                ':user' => $user,
                ':acessorios' => '-',
                ':Pedido' => $novPed,
                ':Orcamento' => $Orcamento
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
                                        ,:Pedido
                                        ,:Orcamento
                                        ,NULL
                                        ,:user
                                        ,:contrato
                                        ,'N'
                                        ,'Extravio'
                                        ,:acessorios
                                        ,GETDATE())");
            $stmt->execute([
                ':itemAntigo' => $itemAntigo,
                ':itemNovo' => $itemNovo,
                ':contrato' => $contrato,
                ':user' => $user,
                ':acessorios' => $acessorios,
                ':Pedido' => $novPed,
                ':Orcamento' => $Orcamento
            ]);
        }

        if ($stmt->rowCount()) {
            return "Alteração feita com sucesso! na função";
        } else {
            if ($lancarErro) {
                throw new RuntimeException('Nenhum registro foi inserido em SUBS_CONTRATO.');
            }
            return "Nenhum item foi alterado.";
        }

    } catch (Throwable $e) {
        if ($lancarErro) {
            throw $e;
        }
        return "Erro ao alterar: " . $e->getMessage();
    }
}


function insereAcessorios($pdo, $itemAntigo, $itemNovo, $contrato, $user, $acessoriosSelecionados)
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
                                        :contrato,	--[CONTRATO]
                                        Nov.TB02054_PRODUTO,	--[PRODUTO]
                                        Nov.TB02054_NUMSERIE,	--[NUMSERIE]
                                        TB02114_ACESSORIO,	--[CODACESSORIO]
                                        TB02114_QTPROD,	--[QTPROD]
                                        'App Subs Contrato',	--[OBS]
                                        TB02114_NUMSERIEACESSORIO	--[NUMSERIEACESSORIO]
                                    FROM TB02114
                                    LEFT JOIN TB02054 Ant ON Ant.TB02054_PAT = :itemAntigo
                                    LEFT JOIN TB02054 Nov ON Nov.TB02054_PAT = :itemNovo 
                                    WHERE TB02114_ACESSORIO IN ($acessoriosSelecionados) AND
                                     TB02114_CODIGO = :contrato2

");
        $stmt->execute([
            ':itemAntigo' => $itemAntigo,
            'itemNovo' => $itemNovo,
            ':contrato' => $contrato,
            ':contrato2' => $contrato,
        ]);

        if ($stmt->rowCount()) {
            // return "Alteração feita com sucesso! na função";
        } else {
            //return "Nenhum item foi alterado.";
        }

    } catch (PDOException $e) {
        return "Erro ao alterar: " . $e->getMessage();
    }
}



function atualizaEvento($pdo, $itemAntigo)
{
    try {

        $stmt = $pdo->prepare("UPDATE SUBS_CONTRATO 
                                SET CONCLUIDO_S_N = 'S'
                                WHERE PAT_SAIDA = :itemAntigo
                                    AND PAT_ENTRADA = '-'");
        $stmt->execute([
            ':itemAntigo' => $itemAntigo
        ]);

        if ($stmt->rowCount()) {
            // return "Alteração feita com sucesso! na função";
        } else {
            //return "Nenhum item foi alterado.";
        }

    } catch (PDOException $e) {
        return "Erro ao alterar: " . $e->getMessage();
    }
}

