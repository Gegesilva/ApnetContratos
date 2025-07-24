<?php
function insereTabSubs($pdo, $itemAntigo, $itemNovo, $contrato, $user) {
    try {
        $stmt = $pdo->prepare("INSERT INTO [dbo].[SUBS_CONTRATO]
                                                        ([PAT_ENTRADA]
                                                        ,[PAT_SAIDA]
                                                        ,[PEDIDO_COMPRA]
                                                        ,[ORCAMENTO]
                                                        ,[STATUS_COMPRA]
                                                        ,[OPCAD]
                                                        ,[COD_CONTRATO]
                                                        ,[CONCLUIDO_A_N]
                                                        ,[TIPO_OP])
                                                    VALUES
                                                        (:itemNovo
                                                        ,:itemAntigo
                                                        ,(SELECT TOP 1 TB02030_CODIGO FROM TB02030 WHERE TB02030_CONTRATO = :contrato2 ORDER BY TB02030_DTCAD DESC)
                                                        ,NULL
                                                        ,NULL
                                                        ,:user
                                                        ,:contrato
                                                        ,'N'
                                                        ,'SubsItemContrato')");
                                                        $stmt->execute([
                                                            ':itemAntigo' => $itemAntigo,
                                                            ':itemNovo' => $itemNovo,
                                                            ':contrato' => $contrato,
                                                            ':contrato2' => $contrato,
                                                            ':user' => $user
                                                        ]);

        if ($stmt->rowCount()) {
            return "Alteração feita com sucesso!";
        } else {
            return "Nenhum item foi alterado.";
        }

    } catch (PDOException $e) {
        return "Erro ao alterar: " . $e->getMessage();
    }
}