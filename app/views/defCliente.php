<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

header('Content-type: text/html; charset=ISO-8895-1');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selecionados'])) {
    $_SESSION['pats_selecionados'] = $_POST['selecionados'];
    /* header("Location: processaSelecionados.php");
    exit; */
}

// Verifica se há pats selecionados em sessão
$patArray = isset($_SESSION['pats_selecionados']) ? $_SESSION['pats_selecionados'] : [];

// Transforma o array em string: (009677, 023650, ...)
$patFormatado = '';
if (!empty($patArray)) {
    $patFormatado =  implode(', ', $patArray) ;
}

// Mantém o array original como JSON, para reuso em JavaScript ou reenvio oculto
$patJson = htmlspecialchars(json_encode($patArray), ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../public/assets/CSS/style.css">
    <title>DATABIT</title>
</head>

<body>

    <div class="container">
        <h2>Defina o contrato novo e o cliente</h2>
        <div class="form-group">
            <textarea name="patSelect" id="patSelec" rows="4"
                style="resize: vertical; width: 100%; max-height: 120px; overflow-y: auto;" readonly
                required><?= $patFormatado ?></textarea>

            <!-- Campo oculto com array original em JSON -->
            <input type="hidden" id="patArray" name="patArray" value="<?= $patJson ?>" />

            <div class="row">
                <input type="text" id="contrato" placeholder="Novo contrato" oninput="atualizarEstado()" required />
                <input type="text" id="cliente" placeholder="Novo Cliente" oninput="atualizarEstado()" required />
            </div>
            <button id="btnAlterar" disabled onclick="abrirModal()">Alterar</button>
            <button id="btnVoltar" onclick="window.location='selecEquips.php'">Voltar</button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modalConfirmacao">
        <div class="modal-content">
            <p>Deseja realmente alterar os itens?</p>
            <div class="buttons">
                <form method="POST" action="processarAlteracao.php">
                    <!-- Envia o array original novamente -->
                    <input type="hidden" name="pats[]" id="patsHidden" />
                    <input type="hidden" name="contrato" id="contratoHidden" />
                    <input type="hidden" name="cliente" id="clienteHidden" />
                    <button type="submit">Confirmar</button>
                </form>
                <button onclick="fecharModal()">Cancelar</button>
            </div>
        </div>
    </div>
    <script src="../../public/assets/JS/defCliente.js"></script>
</body>

</html>