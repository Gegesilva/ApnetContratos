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
    <h2>Extravio (saída do equipamento)</h2>
    <div class="form-group">
      <div class="row">
        <input type="text" id="itemRetirado" placeholder="Item Retirado" required
          <?= $valueItemRet ?> <?= $dissabled ?> />


      </div>
      <button id="btnAlterar" type="button" onclick="abrirModal('modalConfirmacao')">Alterar</button>
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
  <script src="../../public/assets/JS/novExtrav.js"></script>
</body>

</html>