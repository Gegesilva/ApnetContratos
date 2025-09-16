
// Controla estado dos botões
function atualizarEstado() {
    const contrato = document.getElementById("contrato");
    const btnAlterar = document.getElementById("btnAlterar");

    const contratoOK = contrato.value.trim().length >= 3;

    btnAlterar.disabled = !contratoOK;
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
