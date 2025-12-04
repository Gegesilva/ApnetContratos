<?php
session_start();
include '../../config/database.php';
header('Content-type: text/html; charset=ISO-8895-1');
ini_set('max_input_vars', 3000);
error_reporting(0);
ini_set('display_errors', '0');

if (!isset($_SESSION['usuario'])) {
  header("Location: ../../public/login.php");
  exit;
}

/* pega os patrimonios selecionados */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Verifica se alguma célula foi selecionada
  if (isset($_POST['selecionados']) && is_array($_POST['selecionados'])) {
    // Recupera os valores das células selecionadas
    $selecionados = $_POST['selecionados'];

    // Coloca os valores dentro de aspas simples e os separa por vírgula
    $patrimonioAcessorios = "'" . implode("','", array_map('htmlspecialchars', $selecionados)) . "'";
  }
}

$itemRetiradoAcomp = $_POST['PAT_SAIDA'];


$itemAdicionado = isset($_POST['itemAdicionado']) ? $_POST['itemAdicionado'] : $_SESSION['itemAdicionado'];
$itemRetirado = isset($_POST['itemRetirado']) ? $_POST['itemRetirado'] : $_SESSION['itemRetirado'];
$contrato = isset($_POST['contrato']) ? $_POST['contrato'] : $_SESSION['contrato'];



if (isset($patrimonioAcessorios)) {
  /* pega nome dos acessorios */
  $sqlNome = "SELECT 
              STRING_AGG(TB01010_NOME, CHAR(13) + CHAR(10)) Nome
              FROM TB01010
              WHERE TB01010_CODIGO IN ($patrimonioAcessorios)";
  $stmtNome = $pdo->query($sqlNome);
  $nomesAcessorios = $stmtNome->fetchAll(PDO::FETCH_ASSOC)[0]['Nome'];


  $valueItemAdd = "value = '$itemAdicionado' ";
  $valueItemRet = "value = '$itemRetirado' ";
  $valueContrato = "value = '$contrato' ";
  $textareaAcessorios = "<textarea style='display: none;' name='acessoriosSelecionados' id='acessoriosSelecionados' disabled>$patrimonioAcessorios</textarea>";
  $textareaAcessoriosNome = "<textarea disabled>$nomesAcessorios</textarea>";
  $check = "hidden";
  $labelAcessorios = " ";
  $dissabled = "disabled";
} else {
  $valueItemAdd = "";
  $valueItemRet = "";
  $valueContrato = "";
  $textareaAcessorios = "";
  $check = "checkbox";
  $labelAcessorios = "<label for='chkOpcao2'>Incluir acessorios?</label>";
  $dissabled = "";
}

if ($itemRetiradoAcomp) {
  $valueItemRet = "value = '$itemRetiradoAcomp'";
}

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
    <h2>Substituição de Item no Contrato</h2>
    <div class="form-group">
      <!-- <input type="text" id="contrato" placeholder="Número do Contrato" oninput="atualizarEstado()" required
        <?= $valueContrato ?> <?= $dissabled ?> /> -->

      <div class="row">
        <input type="text" id="itemRetirado" placeholder="Item Retirado" oninput="atualizarEstado()" required
          <?= $valueItemRet ?> <?= $dissabled ?> />
        <input type="text" id="itemAdicionado" placeholder="Item Adicionado" disabled oninput="atualizarEstado()"
          required <?= $valueItemAdd ?> <?= $dissabled ?> />
      </div>

      <!-- Checkbox -->
      <div class="check-container">
        <input type="checkbox" class="check" id="chkOpcao">
        <label for="chkOpcao" id="lbExtravio">Extravio?</label>
        <input type="<?= $check ?>" class="check" id="chkOpcao2">
        <?= $labelAcessorios ?>
      </div>
      <!-- Textarea para mostrar os patrimonios selecionados -->
      <div class='form-group'>
        <?= $textareaAcessorios ?>
        <?= $textareaAcessoriosNome ?>
      </div>
      <button id="btnAlterar" onclick="abrirModalVerificacao()">Alterar</button>
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
  <script>
    atualizarEstado();
  </script>

</body>

</html>