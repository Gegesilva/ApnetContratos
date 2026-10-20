function ordenarTabela(colIndex) {
  const table = document.getElementById("substituicoes");
  const tbody = table?.tBodies[0];

  if (!tbody) {
    return;
  }

  const colunaAnterior = Number(table.dataset.sortColumn);
  const direcaoAnterior = table.dataset.sortDirection;
  const asc = colunaAnterior !== colIndex || direcaoAnterior !== "asc";

  table.dataset.sortColumn = String(colIndex);
  table.dataset.sortDirection = asc ? "asc" : "desc";

  const collator = new Intl.Collator("pt-BR", {
    numeric: true,
    sensitivity: "base"
  });

  const obterValor = (row) => {
    const cell = row.children[colIndex];
    return (cell?.dataset.sortValue ?? cell?.innerText ?? "").trim();
  };

  const converterData = (valor) => {
    const partes = valor.match(/^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{2}):(\d{2}))?$/);

    if (!partes) {
      return null;
    }

    return Date.UTC(
      Number(partes[3]),
      Number(partes[2]) - 1,
      Number(partes[1]),
      Number(partes[4] || 0),
      Number(partes[5] || 0)
    );
  };

  const converterNumero = (valor) => {
    const numeroBr = valor
      .replace(/R\$\s?/g, "")
      .replace(/\s/g, "");

    if (!/^-?(?:\d{1,3}(?:\.\d{3})+|\d+)(?:,\d+)?$/.test(numeroBr)) {
      return null;
    }

    return Number(numeroBr.replace(/\./g, "").replace(",", "."));
  };

  const rows = Array.from(tbody.querySelectorAll("tr")).map((row, index) => ({ row, index }));

  rows.sort((itemA, itemB) => {
    const valorA = obterValor(itemA.row);
    const valorB = obterValor(itemB.row);

    // Células vazias permanecem no final nas duas direções.
    if (valorA === "" && valorB === "") return itemA.index - itemB.index;
    if (valorA === "") return 1;
    if (valorB === "") return -1;

    const dataA = converterData(valorA);
    const dataB = converterData(valorB);
    const numeroA = converterNumero(valorA);
    const numeroB = converterNumero(valorB);
    let comparacao;

    if (dataA !== null && dataB !== null) {
      comparacao = dataA - dataB;
    } else if (numeroA !== null && numeroB !== null) {
      comparacao = numeroA - numeroB;
    } else {
      comparacao = collator.compare(valorA, valorB);
    }

    if (comparacao === 0) {
      return itemA.index - itemB.index;
    }

    return asc ? comparacao : -comparacao;
  });

  tbody.replaceChildren(...rows.map(({ row }) => row));
}
