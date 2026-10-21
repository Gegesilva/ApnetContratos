<?php
session_start();

if (!isset($_SESSION['usuario'])) {
  // Redireciona para login, se não estiver logado
  header("Location: ../../public/login.php");
  exit;
}

header('Content-type: text/html; charset=UTF-8');

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
    <h2>Substituição de Razão Social</h2>
    <form action="selecEquips.php" method="post">
      <div class="form-group">

        <input type="text" name="contrato" id="contrato" placeholder="Número do Contrato" oninput="atualizarEstado()"
          required />

        <button id="btnAlterar" disabled onclick="enviaContrato()">Alterar</button>
    </form>
    <button id="btnAlterar" onclick="window.location='menu.php'">Voltar</button>
  </div>
  </div>

  <!-- Modal -->
  <!--  <div class="modal" id="modalConfirmacao">
    <div class="modal-content">
      <p>Deseja realmente alterar os itens?</p>
      <div class="buttons">
        <button onclick="confirmarAlteracao()">Confirmar</button>
        <button onclick="fecharModal()">Cancelar</button>
      </div>
    </div>
  </div> -->
  <script src="../../public/assets/JS/selec.js"></script>
</body>

</html>
