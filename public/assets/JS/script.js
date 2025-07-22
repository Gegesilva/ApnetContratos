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