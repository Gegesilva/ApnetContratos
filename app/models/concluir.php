<?php
include '../../config/database.php'; 

$entrada = $_POST['PAT_ENTRADA'] ?? null;
$saida   = $_POST['PAT_SAIDA'] ?? null;
$pedido  = $_POST['PEDIDO_COMPRA'] ?? null;

if (!$entrada || !$saida || !$pedido) {
    echo "Dados incompletos.";
    exit;
}

try {
    $sql = "UPDATE SUBS_CONTRATO
            SET CONCLUIDO_A_N = 'S'
            WHERE PAT_ENTRADA = :entrada AND PAT_SAIDA = :saida";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':entrada' => $entrada,
        ':saida'   => $saida
    ]);

    echo "Substituição concluída com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

header("Location: ../views/acomp.php");
exit;
