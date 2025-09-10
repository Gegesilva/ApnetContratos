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

let acessorios = 'N';
// Função para decidir qual modal abrir
function abrirModalVerificacao() {
  const check = document.getElementById("chkOpcao").checked;
  const check2 = document.getElementById("chkOpcao2").checked;
  
  if (check) {
      abrirModal("modalAlternativo");
  } else {
      abrirModal("modalConfirmacao");
  }
  acessorios = check2 ? 'S' : 'N';
}

// Abre modal pelo ID
function abrirModal(idModal) {
  document.querySelectorAll(".modal").forEach(m => m.style.display = "none");
  document.getElementById(idModal).style.display = "flex";
}

// Fecha todos os modais
function fecharModal() {
  document.querySelectorAll(".modal").forEach(m => m.style.display = "none");
}

// Confirmação normal (processo original)
function confirmarAlteracao() {
  const contrato = document.getElementById("contrato").value;
  const itemRetirado = document.getElementById("itemRetirado").value;
  const itemAdicionado = document.getElementById("itemAdicionado").value;


  fetch("../models/SubsItemContrato.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `contrato=${encodeURIComponent(contrato)}&itemRetirado=${encodeURIComponent(itemRetirado)}&itemAdicionado=${encodeURIComponent(itemAdicionado)}&acessorios=${encodeURIComponent(acessorios)}`
  })
  .then(response => response.text())
  .then(data => {
    fecharModal();
    alert(data);
    document.getElementById("contrato").value = "";
    document.getElementById("itemRetirado").value = "";
    document.getElementById("itemAdicionado").value = "";
    atualizarEstado();
  })
  .catch(error => {
    alert("Erro ao enviar os dados!");
    console.error("Erro:", error);
  });
}

// Confirmação alternativa (quando checkbox marcado)
function confirmarAlteracaoAlternativa() {
  const contrato = document.getElementById("contrato").value;
  const itemRetirado = document.getElementById("itemRetirado").value;
  const itemAdicionado = document.getElementById("itemAdicionado").value;

  fetch("../models/extravio.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `contrato=${encodeURIComponent(contrato)}&itemRetirado=${encodeURIComponent(itemRetirado)}&itemAdicionado=${encodeURIComponent(itemAdicionado)}&acessorios=${encodeURIComponent(acessorios)}`
  })
  .then(response => response.text())
  .then(data => {
    fecharModal();
    alert(data);
    document.getElementById("contrato").value = "";
    document.getElementById("itemRetirado").value = "";
    document.getElementById("itemAdicionado").value = "";
    atualizarEstado();
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
    window.location.reload();
  })
  .catch(error => {
    console.log("Erro ao concluir: " + error.message);
  });

  return false;
}

// Ordenação de tabela (inalterada)
function ordenarTabela(colIndex) {
  const table = document.querySelector("table");
  const tbody = table.tBodies[0];
  const rows = Array.from(tbody.querySelectorAll("tr"));

  const asc = !table.dataset.sortAsc || table.dataset.sortAsc === "false";
  table.dataset.sortAsc = asc;

  rows.sort((a, b) => {
    const cellA = a.children[colIndex].innerText.trim();
    const cellB = b.children[colIndex].innerText.trim();

    const normalize = str =>
      parseFloat(
        str.replace(/R\$\s?/g, "")
           .replace(/\./g, "")
           .replace(",", ".")
      );

    const numA = normalize(cellA);
    const numB = normalize(cellB);

    if (!isNaN(numA) && !isNaN(numB)) {
      return asc ? numA - numB : numB - numA;
    }

    return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
  });

  tbody.innerHTML = "";
  rows.forEach(row => tbody.appendChild(row));
}
