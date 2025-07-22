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
      <input type="text" id="contrato" placeholder="Número do Contrato" oninput="atualizarEstado()" />
      <div class="row">
        <input type="text" id="itemRetirado" placeholder="Item Retirado" disabled oninput="atualizarEstado()" />
        <input type="text" id="itemAdicionado" placeholder="Item Adicionado" disabled oninput="atualizarEstado()" />
      </div>
      <button id="btnAlterar" disabled onclick="abrirModal()">Alterar</button>
      <button id="btnAlterar" onclick="window.location='../../public/login.php'">Voltar</button>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="modalConfirmacao">
    <div class="modal-content">
      <p>Deseja realmente alterar os itens?</p>
      <div class="buttons">
        <button onclick="confirmarAlteracao()">Confirmar</button>
        <button onclick="fecharModal()">Cancelar</button>
      </div>
    </div>
  </div>
  <script src="../../public/assets/JS/script.js"></script>
</body>
</html>
