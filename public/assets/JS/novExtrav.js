document.addEventListener('DOMContentLoaded', () => {
  // localiza elementos
  const itemRetirado = document.getElementById('itemRetirado');
  const btnAlterar = document.getElementById('btnAlterar');

  // se não existir sai
  if (!itemRetirado || !btnAlterar) return;

  // habilita/desabilita o botão Alterar
  function validarRetirado() {
    const preenchido = itemRetirado.value && itemRetirado.value.trim().length > 2;
    btnAlterar.disabled = !preenchido;
  }

  // Atualiza ao digitar
  itemRetirado.addEventListener('input', validarRetirado);

  // Atualiza ao carregar a página
  validarRetirado();
});




// Abre modal pelo ID
function abrirModal(idModal) {
  document.querySelectorAll(".modal").forEach(m => m.style.display = "none");
  document.getElementById(idModal).style.display = "flex";
}

// Fecha todos os modais
function fecharModal() {
  document.querySelectorAll(".modal").forEach(m => m.style.display = "none");
}

// Confirmação
function confirmarAlteracao() {
  //const contrato = document.getElementById("contrato").value;
  const itemRetirado = document.getElementById("itemRetirado").value;

  fetch("../models/extravioSaida.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `itemRetirado=${encodeURIComponent(itemRetirado)}`
    })
    .then(response => response.text())
    .then(data => {
      fecharModal();
      alert(data);
      window.location.href = "novExtrav.php"; // recarrega a página
    })
    .catch(error => {
      alert("Erro ao enviar os dados!");
      console.error("Erro:", error);
    });
}


