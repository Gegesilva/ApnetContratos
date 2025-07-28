 // Atualiza o array antes de enviar (convertendo JSON para múltiplos inputs hidden)
 function confirmarAlteracao() {
    const pats = JSON.parse(document.getElementById('patArray').value);
    const patsHidden = document.getElementById('patsHidden');
    const contrato = document.getElementById('contrato').value;
    const cliente = document.getElementById('cliente').value;

    // Cria um input para cada valor do array
    patsHidden.name = 'pats[]';
    patsHidden.value = pats.join(',');

    document.getElementById('contratoHidden').value = contrato;
    document.getElementById('clienteHidden').value = cliente;

    // Submete o formulário automaticamente
    patsHidden.form.submit();
  }

  function fecharModal() {
    document.getElementById('modalConfirmacao').style.display = 'none';
  }

  function abrirModal() {
    document.getElementById('modalConfirmacao').style.display = 'block';
  }

  function atualizarEstado() {
    const contrato = document.getElementById('contrato').value.trim();
    const cliente = document.getElementById('cliente').value.trim();
    document.getElementById('btnAlterar').disabled = !(contrato && cliente);
  }


  // Controla estado dos botões
function atualizarEstado() {
  const contrato = document.getElementById("contrato");
  const cliente = document.getElementById("cliente");
  const btnAlterar = document.getElementById("btnAlterar");

  const contratoOK = contrato.value.trim().length >= 3;
  const clienteOK = cliente.value.trim().length >= 3;

  const todosPreenchidos = contratoOK && clienteOK;

  btnAlterar.disabled = !todosPreenchidos;
}