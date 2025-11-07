function atualizarEstado() {
  const itemRetirado = document.getElementById("itemRetirado");
  const itemAdicionado = document.getElementById("itemAdicionado");
  const btnAlterar = document.getElementById("btnAlterar");

  const itemRetiradoOK = itemRetirado.value.trim().length >= 3;

  // Habilita/desabilita campos de item
  itemAdicionado.disabled = !itemRetiradoOK;

  // Habilita botão Alterar somente se todos estiverem preenchidos
  const todosPreenchidos = itemRetiradoOK &&
    itemAdicionado.value.trim().length > 0;

  btnAlterar.disabled = !todosPreenchidos;
}

 //alternancia entre os checkboxes
 const chkOpcao = document.getElementById("chkOpcao");
 const chkOpcao2 = document.getElementById("chkOpcao2");
 const labelOpcao = document.getElementById("lbExtravio");

 chkOpcao.addEventListener("change", () => {
    if (chkOpcao.checked) {
      chkOpcao2.checked = false;
    }
 });

  chkOpcao2.addEventListener("change", () => {
    if (chkOpcao2.checked) {
      chkOpcao.checked = false;
      chkOpcao.disabled = true;
      labelOpcao.textContent = "Selecione na última etapa.";
      labelOpcao.style.color = "red";
      labelOpcao.style.fontSize = "10px";
    } else {
      chkOpcao.disabled = false;
      labelOpcao.textContent = "Extravio?";
      labelOpcao.style.color = "black";
      labelOpcao.style.fontSize = "14px";
    }
  })

// Função para decidir qual modal abrir
function abrirModalVerificacao() {
  const check = document.getElementById("chkOpcao").checked;
  const check2 = document.getElementById("chkOpcao2").checked;

  if (check) {
    abrirModal("modalAlternativo");
  } else if (check2) {
    // chama somente quando o segundo checkbox está marcado
    selecaoAcessorios();
  } else {
    abrirModal("modalConfirmacao");
  }
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
  //const contrato = document.getElementById("contrato").value;
  const itemRetirado = document.getElementById("itemRetirado").value;
  const itemAdicionado = document.getElementById("itemAdicionado").value;
  const elemento = document.getElementById("acessoriosSelecionados");
  const acessoriosSelecionados = elemento ? elemento.value : "";

  fetch("../models/SubsItemContrato.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `itemRetirado=${encodeURIComponent(itemRetirado)}&itemAdicionado=${encodeURIComponent(itemAdicionado)}&acessoriosSelecionados=${encodeURIComponent(acessoriosSelecionados)}`
    })
    .then(response => response.text())
    .then(data => {
      fecharModal();
      alert(data);
      window.location.href = "subsMaq.php"; // recarrega a página
      atualizarEstado();
    })
    .catch(error => {
      alert("Erro ao enviar os dados!");
      console.error("Erro:", error);
    });
}

// Confirmação alternativa (quando checkbox marcado)
function confirmarAlteracaoAlternativa() {
  //const contrato = document.getElementById("contrato").value;
  const itemRetirado = document.getElementById("itemRetirado").value;
  const itemAdicionado = document.getElementById("itemAdicionado").value;
  const elemento = document.getElementById("acessoriosSelecionados");
  const acessoriosSelecionados = elemento ? elemento.value : "";

  fetch("../models/extravio.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `itemRetirado=${encodeURIComponent(itemRetirado)}&itemAdicionado=${encodeURIComponent(itemAdicionado)}&acessoriosSelecionados=${encodeURIComponent(acessoriosSelecionados)}`
    })
    .then(response => response.text())
    .then(data => {
      fecharModal();
      alert(data);
      window.location.href = "subsMaq.php"; // recarrega a página
      atualizarEstado();
    })
    .catch(error => {
      alert("Erro ao enviar os dados!");
      console.error("Erro:", error);
    });
}

// Check inclui acessorios marcado
function selecaoAcessorios() {
  //const contrato = document.getElementById("contrato").value;
  const itemRetirado = document.getElementById("itemRetirado").value;
  const itemAdicionado = document.getElementById("itemAdicionado").value;

  // cria um form temporário para enviar por POST e abrir a tela
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "../views/acessorios.php";
  form.style.display = "none";

  const addField = (name, value) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    form.appendChild(input);
  };

  //addField("contrato", contrato);
  addField("itemRetirado", itemRetirado);
  addField("itemAdicionado", itemAdicionado);

  document.body.appendChild(form);
  form.submit(); // navega para a tela de acessórios com os parâmetros
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