<?php
session_start();

include_once "InsereTabSubs";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

$user = $_SESSION['usuario'];

header('Content-type: text/html; charset=ISO-8895-1');


include '../../config/database.php';

$itemAdicionado = $_POST['PAT_ENTRADA'];
$itemRetirado = $_POST['PAT_SAIDA'];
$contrato = $_POST['COD_CONTRATO'];
$ContratoAnt = $_POST['COD_CONTRATO_ANT'];
$acessorios = strtoupper($_POST['ACESSORIOS_S_N']);


/* if (!$itemAdicionado || !$itemRetirado || !$contrato) {
    echo "Dados incompletos.";
    exit;
} */

try {
    if ($ContratoAnt != '' && $ContratoAnt != null) {
        // Atualiza o contrato antigo para concluído
        $sql = "UPDATE SUBS_CONTRATO
                SET CONCLUIDO_S_N = 'S'
                WHERE COD_CONTRATO_ANT = :contratoAnt ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':contratoAnt' => $ContratoAnt
        ]);
    } else {
        $sql = "EXEC [dbo].[TROCAR_ITEM_CONTRATO] 
                        @Contrato = :contrato 
                        ,@ItemRetirado = :itemRetirado
                        ,@ItemAdicionado = :itemAdicionado
                        ,@User = :user
                        ,@Acessorios = :acessorios;
                        
            UPDATE SUBS_CONTRATO
            SET CONCLUIDO_S_N = 'S'
            WHERE PAT_ENTRADA = :itemAdicionadoUp AND PAT_SAIDA = :itemRetiradoUp";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':itemAdicionado' => $itemAdicionado,
            ':itemAdicionadoUp' => $itemAdicionado,
            ':itemRetirado' => $itemRetirado,
            ':itemRetiradoUp' => $itemRetirado,
            ':contrato' => $contrato,
            ':user' => $user,
            ':acessorios' => $acessorios
        ]);
    }



    echo "Substituição concluída com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

header("Location: ../views/acomp.php");
exit;
