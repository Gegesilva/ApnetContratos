function confirmarAlteracao() {
  const contrato = document.getElementById("contrato").value;
  const contratoAnt = document.getElementById("contratoAnt").value;
  const cliente = document.getElementById("clienteInp").value;
  const pats = document.getElementById("patArray").value;

  // pega o estado do checkbox
  const check = document.getElementById("chkAcess");
  const checkPar = document.getElementById("chkAcessPar");
  const checkContExist = document.getElementById("chkAcessContExist");
  const vaiAcessorios = check.checked ? "S" : "N";
  const vaiSituacaoContratoAnt = checkPar.checked ? "A" : "I";
  const vaiContratoExistente = checkContExist.checked ? "S" : "N";

  // Preencher os inputs ocultos dentro do modal
 /*  document.getElementById("contratoHidden").value = contrato;
  document.getElementById("contratoAntHidden").value = contratoAnt;
  document.getElementById("clienteHidden").value = cliente;
  document.getElementById("patsHidden").value = pats;
  document.getElementById("vaiAcessorios").value = vaiAcessorios; */

  // Criar um formulário e enviá-lo por POST
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "../models/subsCliente.php";

  const addHidden = (name, value) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    form.appendChild(input);
  };

  addHidden("contrato", contrato);
  addHidden("contratoAnt", contratoAnt);
  addHidden("cliente", cliente);
  addHidden("patArray", pats);
  addHidden("Acessorios", vaiAcessorios); 
  addHidden("SituacaoContratoAnt", vaiSituacaoContratoAnt);
  addHidden("ContratoExistente", vaiContratoExistente);

 /*  console.log("Dados a enviar:", {
    vaiSituacaoContratoAnt
  }); */

  document.body.appendChild(form);
  form.submit();
}



// Abrir e fechar modal
function abrirModal() {
  const modal = document.getElementById('modalConfirmacao');
  modal.style.display = 'flex';
}


function fecharModal() {
  document.getElementById('modalConfirmacao').style.display = 'none';
}

function atualizarEstado() {
  const contrato = document.getElementById("contrato");
  const cliente = document.getElementById("clienteInp");
  const btnAlterar = document.getElementById("btnAlterar");

  const contratoOK = contrato.value.trim().length >= 3;
  const clienteOK = cliente.value.trim().length == 8;

  btnAlterar.disabled = !(contratoOK && clienteOK);

}


//selecionar todos os checkboxes
let todosSelecionados = false;

function selecionarTodos() {
  const checkboxes = document.querySelectorAll('.chk-item');
  todosSelecionados = !todosSelecionados;
  checkboxes.forEach(cb => cb.checked = todosSelecionados);
}



// Ordenação das Colunas
function ordenarTabela(colIndex) {
  const table = document.querySelector("table");
  const tbody = table.tBodies[0];
  const rows = Array.from(tbody.querySelectorAll("tr"));

  const asc = !table.dataset.sortAsc || table.dataset.sortAsc === "false";
  table.dataset.sortAsc = asc;

  rows.sort((a, b) => {
    const cellA = a.children[colIndex].innerText.trim();
    const cellB = b.children[colIndex].innerText.trim();

    // Remove "R$", espaços e pontos dos milhares, substitui vírgula por ponto
    const normalize = str =>
      parseFloat(
        str.replace(/R\$\s?/g, "") // remove R$ e espaço
        .replace(/\./g, "") // remove pontos dos milhares
        .replace(",", ".") // troca vírgula decimal por ponto
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

/* desailitar campo codigo no novo cliente quando o contrato existente for selecionado */
function atualizarCampos() {
  const chkAcessContExist = document.getElementById("chkAcessContExist");
  const clienteInp = document.getElementById("clienteInp");

  if (chkAcessContExist.checked) {
    clienteInp.style.display = 'none';
    clienteInp.value = "N";
  } else {
    clienteInp.style.display = 'block';
  }
  

}