function confirmarAlteracao() {
  const pats = JSON.parse(document.getElementById('patArray').value);
  const contrato = document.getElementById('contrato').value.trim();
  const contratoAnt = document.getElementById('contratoAnt').value.trim();
  const cliente = document.getElementById('clienteInp').value.trim();

  // Preenche os campos escondidos do formulário no modal
  document.getElementById('patsHidden').value = JSON.stringify(pats);
  document.getElementById('contratoHidden').value = contrato;
  document.getElementById('contratoAntHidden').value = contratoAnt;
  document.getElementById('clienteHidden').value = cliente;

  // Fecha o modal (opcional se você quiser ver ele sumir ao confirmar)
  // document.getElementById('modalConfirmacao').style.display = 'none';
  // Formulário será enviado automaticamente pelo botão "submit"
}

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