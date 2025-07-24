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
    // Captura os valores dos inputs
    const contrato = document.getElementById("contrato").value;
    const itemRetirado = document.getElementById("itemRetirado").value;
    const itemAdicionado = document.getElementById("itemAdicionado").value;
  
    // Envia via fetch para um arquivo PHP
    fetch("../models/SubsItemContrato.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `contrato=${encodeURIComponent(contrato)}&itemRetirado=${encodeURIComponent(itemRetirado)}&itemAdicionado=${encodeURIComponent(itemAdicionado)}`
    })
    .then(response => response.text())
    .then(data => {
      fecharModal();
      alert(data); // ou exiba como quiser
      document.getElementById("contrato").value = "";
      document.getElementById("itemRetirado").value = "";
      document.getElementById("itemAdicionado").value = "";
      atualizarEstado(); // sua função
    })
    .catch(error => {
      alert("Erro ao enviar os dados!");
      console.error("Erro:", error);
    });
  }
  

  function enviarEAtualizar(form) {
    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData
    })
    .then(response => {
      if (!response.ok) throw new Error("Erro na requisição");
      return response.text();
    })
    .then(() => {
      window.location.reload(); // recarrega a página após o sucesso
    })
    .catch(error => {
      alert("Erro ao concluir: " + error.message);
    });

    return false; // impede o submit tradicional
  }