<?php
session_start();
header('Content-type: text/html; charset=ISO-8895-1');

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/login.php");
    exit;
}

/* pega os patrimonios selecionados */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Verifica se alguma célula foi selecionada
  if (isset($_POST['selecionado']) && is_array($_POST['selecionado'])) {
      // Recupera os valores das células selecionadas
      $selecionados = $_POST['selecionado'];

      // Coloca os valores dentro de aspas simples e os separa por vírgula
      $patrimonioAcessorios = "'".implode("','", array_map('htmlspecialchars', $selecionados))."'";
  }
}

$itemAdicionado = isset($_POST['itemAdicionado']) ? $_POST['itemAdicionado'] : $_SESSION['itemAdicionado'];
$itemRetirado = isset($_POST['itemRetirado']) ? $_POST['itemRetirado'] : $_SESSION['itemRetirado'];
$contrato = isset($_POST['contrato']) ? $_POST['contrato'] : $_SESSION['contrato'];

if(isset($patrimonioAcessorios)){
  $valueItemAdd ="value = '$itemAdicionado' "; 
  $valueItemRet ="value = '$itemRetirado' ";
  $valueContrato ="value = '$contrato' ";
  $textareaAcessorios ="<textarea name='pats' id='pats'>$patrimonioAcessorios</textarea>";
} else{
  $valueItemAdd =""; 
  $valueItemRet ="";
  $valueContrato ="";
  $textareaAcessorios ="";
}

echo $patrimonioAcessorios, $itemAdicionado, $itemRetirado, $contrato;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../../public/assets/CSS/style.css">
  <title>DATABIT</title>
</head>
<body>

  <div class="container">
    <h2>Substituição de Item no Contrato</h2>
    <div class="form-group">
      <input type="text" id="contrato" placeholder="Número do Contrato" oninput="atualizarEstado()" required <?=$valueContrato?>/>
      
      <div class="row">
        <input type="text" id="itemRetirado" placeholder="Item Retirado" disabled oninput="atualizarEstado()" required <?=$valueItemRet?>/>
        <input type="text" id="itemAdicionado" placeholder="Item Adicionado" disabled oninput="atualizarEstado()" required <?=$valueItemAdd?>/>
      </div>

      <!-- Checkbox -->
      <div class="check-container">
        <input type="checkbox" class="check" id="chkOpcao">
        <label for="chkOpcao">Extravio?</label>
        <input type="checkbox" class="check" id="chkOpcao2">
        <label for="chkOpcao2">Incluir acessorios?</label>
      </div>
      <?= $textareaAcessorios?>
      <button id="btnAlterar" disabled onclick="abrirModalVerificacao()">Alterar</button>
      <button onclick="window.location='menu.php'">Voltar</button>
    </div>
  </div>

  <!-- Modal original -->
  <div class="modal" id="modalConfirmacao">
    <div class="modal-content">
      <p>Deseja realmente trocar os itens?</p>
      <div class="buttons">
        <button onclick="confirmarAlteracao()">Confirmar</button>
        <button onclick="fecharModal()">Cancelar</button>
      </div>
    </div>
  </div>

  <!-- Novo modal (quando checkbox marcado) -->
  <div class="modal" id="modalAlternativo">
    <div class="modal-content">
      <p>Deseja realmente trocar os itens extraviados?</p>
      <div class="buttons">
        <button onclick="confirmarAlteracaoAlternativa()">Confirmar</button>
        <button onclick="fecharModal()">Cancelar</button>
      </div>
    </div>
  </div>
  <script src="../../public/assets/JS/script.js"></script>
</body>
</html>
