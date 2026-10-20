
// Controla estado dos botões
function atualizarEstado() {
    const contrato = document.getElementById("contrato");
    const btnAlterar = document.getElementById("btnAlterar");

    const contratoOK = contrato.value.trim().length >= 3;

    btnAlterar.disabled = !contratoOK;
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
