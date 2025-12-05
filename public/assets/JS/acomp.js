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