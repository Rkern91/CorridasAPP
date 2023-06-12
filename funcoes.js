/**
 * Remove caracteres invalidos.
 * @param inputField
 */
function validateInput(inputField)
{
  var nmTabela = document.getElementById('id_tabela').value.toString();

  // Express?o regular para encontrar caracteres inv?lidos (n?o letras e espa?os)
  var dsRegex      = nmTabela == 'cidade' ? /[^a-zA-Z\/\s\-]/g : /[^a-zA-Z0-9\/\s\-]/g;
  var inputValue   = inputField.value.toUpperCase();
  inputField.value = inputValue.replace(dsRegex, "");
}

/**
 * Adiciona ou remove a modalidade selecionada ao campo de texto,
 * apenas para dar um retorno visual da acao executada na tela ao usuario.
 * @param dsTexto
 * @param idOp
 */
function alterarTextoModalidadesSelecionadas(dsTexto, idOp)
{
  var objTextoModals = document.getElementById('dsModals');
  var arrDsModal     = [];
  var dsModal        = '';
  
  if (objTextoModals.value != '')
    arrDsModal = objTextoModals.value.split(',');
  
  switch (idOp)
  {
    case 'add':
      //Adiciona o texto ao array das modalidades selecionadas
      if (Array.isArray(arrDsModal) && !arrDsModal.includes(dsTexto))
      {
        if (arrDsModal.length > 0)
        {
          //Quando nao for mais a primeira modalidade inserida,
          //Itera sobre o array obtendo os valores existentes
          arrDsModal.forEach(value => {
            if (dsModal == '')
              dsModal = value
            else
              dsModal = dsModal + ',' + value;
          })
          
          //Por fim adiciona o novo valor no final desse array
          dsModal = dsModal + ',' + dsTexto;
        }
        else
          dsModal = dsTexto;
        
        objTextoModals.value = dsModal;
      }
    break;
    case 'rem':
      //Remove o texto do array as modalidades selecionadas
      if (Array.isArray(arrDsModal) && arrDsModal.length > 0 && arrDsModal.includes(dsTexto))
      {
        //Remove a atual opcao selecionada do array
        var opIdRemover = arrDsModal.indexOf(dsTexto);
        arrDsModal.splice(opIdRemover, 1);
        
        arrDsModal.forEach(value => {
          if (dsModal == '')
            dsModal = value;
          else
            dsModal = dsModal + ',' + value;
        })
        
        //Define o valor final novamente no campo
        objTextoModals.value = dsModal;
      }
    break;
  }
}

/**
 * Adiciona ou remove a modalidade selecionada ao array de modalidades,
 * que sera submetido junto ao form e inserido na relacao do evento.
 * @param idOp
 */
function alterarModalidadesSelecionadas(idOp)
{
  //Obtem os objetos manipulados na funcao
  var objModal      = document.getElementById('op_id_modalidades');
  var objArrModal   = document.getElementById('arr_cd_modalidades');
  var qtModalidades = document.getElementById('qt_modalidades');
  
  //definicao de valores
  var dsTexto         = objModal.options[objModal.selectedIndex].text.split(' ')[0] + 'km';
  var qtModalAtual    = qtModalidades.value;
  var dsSelectedModal = '';
  var arrOptModal     = [];
  
  if (objArrModal.value != '')
    arrOptModal = objArrModal.value.split(',');
  
  switch (idOp)
  {
    case 'add':
      //Adiciona o codigo das modalidades selecionadas em um array e submete junto ao form
      if (Array.isArray(arrOptModal) && !arrOptModal.includes(objModal.value))
      {
        if (arrOptModal.length > 0)
        {
          //Quando nao for mais a primeira modalidade inserida,
          //Itera sobre o array obtendo os valores existentes
          arrOptModal.forEach(value => {
            if (dsSelectedModal == '')
              dsSelectedModal = value
            else
              dsSelectedModal = dsSelectedModal + ',' + value;
          })
          
          //Por fim adiciona o novo valor no final desse array
          dsSelectedModal = dsSelectedModal + ',' + objModal.value;
        }
        else
          dsSelectedModal = objModal.value;

        qtModalAtual++;
        qtModalidades.value = qtModalAtual;
        objArrModal.value   = dsSelectedModal;
        
        alterarTextoModalidadesSelecionadas(dsTexto, idOp);
      }
    break;
    case 'rem':
      //Remove do array as modalidades selecionadas e nao submete junto ao form
      if (Array.isArray(arrOptModal) && arrOptModal.length > 0 && arrOptModal.includes(objModal.value))
      {
        var opIdRemover = arrOptModal.indexOf(objModal.value);
        arrOptModal.splice(opIdRemover, 1);
        
        arrOptModal.forEach(value => {
          if (dsSelectedModal == '')
            dsSelectedModal = value;
          else
            dsSelectedModal = dsSelectedModal + ',' + value;
        })
    
        if (qtModalAtual > 0)
          qtModalAtual--;
        
        qtModalidades.value = qtModalAtual;
        objArrModal.value   = dsSelectedModal;
        
        alterarTextoModalidadesSelecionadas(dsTexto, idOp);
      }
    break;
  }
}

/**
 * Ajusta a formatação de valores para o padrão da moeda brasileira.
 * @param objCampo
 */
function ajustarFormatoValores(objCampo)
{
  var formatter  = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
  objCampo.value = formatter.format(objCampo.value);
}

/**
 * Alternativa para ZERAR/LIMPAR os campos hidden ao clicar
 * no botao RESET do form.
 */
function limparCamposHidden()
{
  document.getElementById('arr_cd_modalidades').value = '';
  document.getElementById('qt_modalidades').value     = 0;
}

/**
 * Testa se o valor não é vazio. Não serve para objetos ou arrays.
 * @param vl
 * @returns {boolean}
 */
function hasValue(vl)
{
  var idTrue = true;
  
  if (typeof vl === 'string')
    idTrue = vl.length > 0;
  
  if (typeof vl === 'number')
    idTrue = !isNaN(vl);
  
  return (
    idTrue &&
    vl !== '' &&
    vl !== null &&
    vl !== false &&
    typeof vl !== 'undefined'
  );
}

/**
 * Itera sobre o objeto de campos da tela e valida
 * se os campos foram preenchidos para inserção.
 * @returns {boolean}
 */
function validarCampos()
{
  var idTela    = document.getElementById('id_tabela').value;
  var dsMsg     = '';
  var arrFields = '';
  
  switch (idTela)
  {
    case 'evento':
      arrFields = {
        'nm_evento': 'Evento',
        'cd_cidade': 'Cidade',
        'dt_evento': 'Data',
        'hr_evento': 'Hora',
        'arr_cd_modalidades': 'Modalidade'
      };
    break
    case 'modalidade':
      arrFields = {
        'ds_descricao': 'Modalidade',
        'vl_valor': 'Valor (R$)',
        'vl_km_distancia': 'Distância (KM)',
        'dt_largada_modalidade': 'Data',
        'hr_largada_modalidade': 'Hora'
      };
    break;
    case 'cidade':
      arrFields = {
        'nm_cidade': 'Cidade',
        'cd_uf': 'Estado'
      };
    break;
    case 'login':
      arrFields = {
        'ds_email': 'Email',
        'ds_senha': 'Senha'
      };
  }
  
  for (var key in arrFields)
  {
    if (arrFields.hasOwnProperty(key))
    {
      var objCampo = document.getElementById(key.toString());
      
      if (!hasValue(objCampo.value))
      {
        dsMsg = 'Por favor, preencha o campo ' + arrFields[key] + '!';
        objCampo.focus();
        break;
      }
    }
  }
  
  if (dsMsg != '')
  {
    alert(dsMsg);
    return false;
  }
  else
    return true;
}

/**
 * Verifica a acao retornada no formulario e exibe um alerta
 * de confirmacao.
 */
function verificarAcaoForm()
{
  const idOperacao = document.getElementsByClassName('ds_operacao');
  
  if (idOperacao.length > 0)
  {
    const dsOrigem = document.getElementsByClassName('ds_origem');
    var   dsLink   = '';
    var   dsTexto  = '';
    
    switch (idOperacao[0].value)
    {
      case 'delete':
        alert('Registro REMOVIDO com sucesso!');
      break;
      case 'update':
        alert('Registro ATUALIZADO com sucesso!');
      break;
      case 'insert':
        alert('Registro INSERIDO com sucesso!');
      break;
      case 'login':
        alert('Login realizado com sucesso!');
      break;
      case 'cadastrar':
        switch (dsOrigem[0].value)
        {
          case 'cidade':
            dsLink  = 'man_cidade.php';
            dsTexto = 'Nenhuma cidade cadastrada ou todas as cidades foram removidos! Deseja cadastrar nova cidade?';
          break;
          case 'evento':
            dsLink  = 'man_evento.php';
            dsTexto = 'Nenhum evento cadastro ou todos os eventos foram removidos! Deseja cadastrar novo evento?';
          break;
          case 'modalidade':
            dsLink  = 'man_modalidade.php';
            dsTexto = 'Nenhuma modalidade cadastrada ou todas as modalidades foram removidas! Deseja cadastrar nova modalidade?';
          break;
        }
        
        if (confirm(dsTexto))
          window.location.href = dsLink;
        else
          window.location.href = 'View/index.php';
      break;
    }
  }
}

document.addEventListener('DOMContentLoaded', function(){
  verificarAcaoForm();
  
  document.getElementById('form').addEventListener('submit', function(ev){
    ev.preventDefault();
    
    if (validarCampos())
      document.getElementById('form').submit();
  });
});

