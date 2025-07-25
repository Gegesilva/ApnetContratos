<?php
include '../../config/database.php'; 

$itemAdicionado = $_POST['PAT_ENTRADA'];
$itemRetirado   = $_POST['PAT_SAIDA'];
$pedidoCompra  = $_POST['PEDIDO_COMPRA'];
$contrato  = $_POST['COD_CONTRATO'];

if (!$entrada || !$saida || !$pedido) {
    echo "Dados incompletos.";
    exit;
}

try {
    /* $sql = "UPDATE SUBS_CONTRATO
            SET CONCLUIDO_S_N = 'S'
            WHERE PAT_ENTRADA = :entrada AND PAT_SAIDA = :saida"; */

    $sql = "EXEC @RC = [dbo].[TROCAR_ITEM_CONTRATO] 
                        @Contrato
                        ,@ItemRetirado
                        ,@ItemAdicionado
                        ,@User";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':itemAdicionado' => $itemAdicionado,
        ':itemRetirado'   => $itemRetirado,
    ]);

    echo "Substituição concluída com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

header("Location: ../views/acomp.php");
exit;
