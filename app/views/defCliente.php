<?php
session_start();

if (isset($_POST['contrato']) && $_POST['contrato'] !== '') {
    $contrato = $_POST['contrato'];
    $_SESSION['contrato'] = $_POST['contrato']; // salva na sessão para futuros usos
} elseif (isset($_SESSION['contrato']) && $_SESSION['contrato'] !== '') {
    $contrato = $_SESSION['contrato'];
} else {
    $contrato = null;
}

$ContratoAnt = $contrato ? $contrato : '';

// Teste de debug
/* var_dump($_POST, $_SESSION, $contrato);
exit; */

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

header('Content-type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selecionados']) && is_array($_POST['selecionados']) && !empty($_POST['selecionados'])) {
    $_SESSION['pats_selecionados'] = $_POST['selecionados'];
    header("Location: defCliente.php");
    exit; 
}

// Verifica se há pats selecionados em sessão
$patArray = isset($_SESSION['pats_selecionados']) ? $_SESSION['pats_selecionados'] : array();

// Transforma o array em string: (009677, 023650, ...)
$patFormatado = '';
if (!empty($patArray)) {
    $patFormatado = implode(', ', $patArray);
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
        <textarea name="patSelect" id="patSelec" rows="4" disabled
            style="resize: vertical; width: 100%; max-height: 120px; overflow-y: auto;" readonly
            required><?= $patFormatado ?>
        </textarea>
        <textarea name="obsPedido" id="obsPedido" rows="4" placeholder="Obs do pedido"
            style="resize: vertical; width: 100%; max-height: 120px; overflow-y: auto;"></textarea>

        <!-- Campo oculto com array original em JSON -->
        <input type="hidden" id="patArray" name="patArray" value="<?= $patJson ?>" />

        <div class="row">
            <input type="text" maxlength="12" name="contratoNov" id="contrato" placeholder="Código do novo contrato"
                oninput="atualizarEstado()" required />
            <input type="text" maxlength="8" minlength="8" name="clienteInp" id="clienteInp" placeholder="Código do novo cliente"
                oninput="atualizarEstado()" required />
            <input type="hidden" name="contratoAnt" id="contratoAnt" value="<?= $ContratoAnt ?>">
        </div>
        <!-- Checkbox -->
      <div class="check-container">
        <input type="checkbox" class="check" id="chkAcess">
        <label for="chkAcess">Incluir acessorios?</label>

        <input type="checkbox" class="check" id="chkAcessPar">
        <label for="chkAcessPar">Parcial?</label>

        <input type="checkbox" class="check" id="chkAcessContExist" onchange="atualizarEstado()">
        <label for="chkAcessContExist">Contrato existente?</label>
      </div>
        <button id="btnAlterar" disabled onclick="abrirModal()">Alterar</button>
        <button id="btnVoltar" type="button" onclick="window.location.href= 'selecEquips.php'">Voltar</button>
    </div>
</div>

<!-- Modal -->
<div class="modal" id="modalConfirmacao">
    <div class="modal-content">
        <p>Deseja realmente substituir o cliente?</p>
        <div class="buttons">
            <!-- Inputs ocultos para envio via JS -->
            <!-- <input type="hidden" name="pats" id="patsHidden" />
            <input type="hidden" name="contrato" id="contratoHidden" />
            <input type="hidden" name="contratoAnt" id="contratoAntHidden" />
            <input type="hidden" name="cliente" id="clienteHidden" />
            <input type="hidden" name="Acessorios" id="vaiAcessorios" /> -->
            <!-- <input type="hidden" name="SituacaoContratoAnt" id="vaiSituacaoContratoAnt" /> -->

            <!-- Botão chama a função JS -->
            <button onclick="confirmarAlteracao()">Confirmar</button>
            <button onclick="fecharModal()">Cancelar</button>
        </div>
    </div>
</div>

<script src="../../public/assets/JS/defCliente.js"></script>
</body>
</html>
