<?php
include '../config/database.php'; 

$contrato = $_POST['contrato'] ?? '';
$itemRetirado = $_POST['itemRetirado'] ?? '';
$itemAdicionado = $_POST['itemAdicionado'] ?? '';

// Validação básica
if (!$contrato || !$itemRetirado || !$itemAdicionado) {
    echo "Dados incompletos.";
    exit;
}

// Aqui você faria a alteração no banco, por exemplo:
try {
    $stmt = $pdo->prepare("UPDATE TB_ITENS_CONTRATO SET ITEM = :itemNovo WHERE CONTRATO = :contrato AND ITEM = :itemAntigo");
    $stmt->execute([
        ':itemNovo' => $itemAdicionado,
        ':contrato' => $contrato,
        ':itemAntigo' => $itemRetirado
    ]);

    if ($stmt->rowCount()) {
        echo "Alteração feita com sucesso!";
    } else {
        echo "Nenhum item foi alterado.";
    }

} catch (PDOException $e) {
    echo "Erro ao alterar: " . $e->getMessage();
}
