function confirmarAlteracao() {
  const contrato = document.getElementById("contrato").value;
  const contratoAnt = document.getElementById("contratoAnt").value;
  const cliente = document.getElementById("clienteInp").value;
  const pats = document.getElementById("patArray").value;
  const obsPedido = document.getElementById("obsPedido").value;

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
  addHidden("obsPedido", obsPedido);
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
  const btnAlterar = document.getElementById("btnAlterar");
  const chkAcessContExist = document.getElementById("chkAcessContExist");

  const clienteInp = document.getElementById("clienteInp");
  const contratoNov = document.getElementById("contrato");

  if (chkAcessContExist.checked) {
    clienteInp.style.display = 'none';
    clienteInp.value = "";
    btnAlterar.disabled = false; 
  } else {
    clienteInp.style.display = 'block';
  }
  
  if (contratoNov.value.trim().length >= 3) {
    btnAlterar.disabled = false;
  }

  const contratoOK = contrato.value.trim().length >= 3;
  const clienteOK = clienteInp.value.trim().length == 8;

  btnAlterar.disabled = !(contratoOK && (clienteOK || chkAcessContExist.checked));

}


//selecionar todos os checkboxes
let todosSelecionados = false;

function selecionarTodos() {
  const checkboxes = document.querySelectorAll('.chk-item');
  todosSelecionados = !todosSelecionados;
  checkboxes.forEach(cb => cb.checked = todosSelecionados);
}



// Ordenação das colunas
function ordenarTabela(colIndex) {
  const table = document.getElementById("substituicoes");
  const tbody = table?.tBodies[0];

  if (!tbody) return;

  const colunaAnterior = Number(table.dataset.sortColumn);
  const asc = colunaAnterior !== colIndex || table.dataset.sortDirection !== "asc";

  table.dataset.sortColumn = String(colIndex);
  table.dataset.sortDirection = asc ? "asc" : "desc";

  const collator = new Intl.Collator("pt-BR", {
    numeric: true,
    sensitivity: "base"
  });

  const converterNumero = (valor) => {
    const numeroBr = valor.replace(/R\$\s?/g, "").replace(/\s/g, "");

    if (!/^-?(?:\d{1,3}(?:\.\d{3})+|\d+)(?:,\d+)?$/.test(numeroBr)) {
      return null;
    }

    return Number(numeroBr.replace(/\./g, "").replace(",", "."));
  };

  const rows = Array.from(tbody.querySelectorAll("tr")).map((row, index) => ({ row, index }));

  rows.sort((itemA, itemB) => {
    const cellA = itemA.row.children[colIndex];
    const cellB = itemB.row.children[colIndex];
    const valorA = (cellA?.dataset.sortValue ?? cellA?.innerText ?? "").trim();
    const valorB = (cellB?.dataset.sortValue ?? cellB?.innerText ?? "").trim();

    if (valorA === "" && valorB === "") return itemA.index - itemB.index;
    if (valorA === "") return 1;
    if (valorB === "") return -1;

    const numeroA = converterNumero(valorA);
    const numeroB = converterNumero(valorB);
    const comparacao = numeroA !== null && numeroB !== null
      ? numeroA - numeroB
      : collator.compare(valorA, valorB);

    return comparacao === 0 ? itemA.index - itemB.index : (asc ? comparacao : -comparacao);
  });

  tbody.replaceChildren(...rows.map(({ row }) => row));
}

/* desailitar campo codigo no novo cliente quando o contrato existente for selecionado */
/* function atualizarCampos() {
  const chkAcessContExist = document.getElementById("chkAcessContExist");
  const clienteInp = document.getElementById("clienteInp");
  const btnAlterar = document.getElementById("btnAlterar");
  const contratoNov = document.getElementById("contrato");

  if (chkAcessContExist.checked) {
    clienteInp.style.display = 'none';
    clienteInp.value = "N";
    btnAlterar.disabled = false; 
  } else {
    clienteInp.style.display = 'block';
    clienteInp.value = "";
  }
  
  if (contratoNov.value.trim().length >= 3) {
    btnAlterar.disabled = false;
  }

} */
