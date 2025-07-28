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

  function abrirModal() {
    document.getElementById("modalConfirmacao").style.display = "flex";
  }

  function fecharModal() {
    document.getElementById("modalConfirmacao").style.display = "none";
  }

  function confirmarAlteracao() {
    // Captura os valores dos inputs
    const contrato = document.getElementById("contrato").value;
    const itemRetirado = document.getElementById("itemRetirado").value;
    const itemAdicionado = document.getElementById("itemAdicionado").value;
  
    // Envia via fetch para um arquivo PHP
    fetch("../models/SubsItemContrato.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `contrato=${encodeURIComponent(contrato)}&itemRetirado=${encodeURIComponent(itemRetirado)}&itemAdicionado=${encodeURIComponent(itemAdicionado)}`
    })
    .then(response => response.text())
    .then(data => {
      fecharModal();
      alert(data); // ou exiba como quiser
      document.getElementById("contrato").value = "";
      document.getElementById("itemRetirado").value = "";
      document.getElementById("itemAdicionado").value = "";
      atualizarEstado(); // sua função
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
      window.location.reload(); // recarrega a página após o sucesso
    })
    .catch(error => {
      console.log("Erro ao concluir: " + error.message);
    });

    return false; // impede o submit tradicional
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
