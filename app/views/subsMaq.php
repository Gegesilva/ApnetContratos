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

  <script>
    function atualizarEstado() {
      const contrato = document.getElementById("contrato");
      const itemRetirado = document.getElementById("itemRetirado");
      const itemAdicionado = document.getElementById("itemAdicionado");
      const btnAlterar = document.getElementById("btnAlterar");

      const contratoOK = contrato.value.trim().length >= 3;

      // Habilita/desabilita campos de item
      itemRetirado.disabled = !contratoOK;
      itemAdicionado.disabled = !contratoOK;

      // Habilita botão Alterar somente se todos estiverem preenchidos
      const todosPreenchidos = contratoOK &&
        itemRetirado.value.trim().length > 0 &&
        itemAdicionado.value.trim().length > 0;

      btnAlterar.disabled = !todosPreenchidos;
    }

    function abrirModal() {
      document.getElementById("modalConfirmacao").style.display = "flex";
    }

    function fecharModal() {
      document.getElementById("modalConfirmacao").style.display = "none";
    }

    function confirmarAlteracao() {
      fecharModal();
      alert("Alterações confirmadas!");
      document.getElementById("contrato").value = "";
      document.getElementById("itemRetirado").value = "";
      document.getElementById("itemAdicionado").value = "";
      atualizarEstado();
    }
  </script>

</body>
</html>
