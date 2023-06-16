<?php
  $dsMsg         = "";
  $dsLinkRetorno = "";

  const ID_ERRO_CONEXAO_BANCO = 1;
  const ID_ERRO_CONFIG_BANCO  = 2;
  const ID_ERRO_INSERT        = 3;
  const ID_ERRO_UPDATE        = 4;
  const ID_ERRO_DELETE        = 5;

  if (isset($_REQUEST["id_erro"]))
  {
    switch ($_REQUEST["id_erro"])
    {
      case ID_ERRO_CONEXAO_BANCO:
        $dsMsg = "Não foi possivel conectar ao Banco de Dados!";
        break;
      case ID_ERRO_CONFIG_BANCO:
        $dsMsg = "Não foi possivel obter o arquivo de configuracao do Banco de Dados!";
        break;
      case ID_ERRO_INSERT:
        $dsMsg = "Não foi possivel INSERIR o registro!";
        break;
      case ID_ERRO_UPDATE:
        $dsMsg = "Não foi possivel ATUALIZAR o registro!";
        break;
      case ID_ERRO_DELETE:
        $dsMsg = "Não foi possivel EXCLUIR o registro!";
        break;
    }
  }
  
  $dsLinkInicio = "<a href='index.php'>Voltar ao Inicio</a>";
  
  switch ($_REQUEST["dsOrigem"])
  {
    case "cidade":
      $dsLinkRetorno = "<a href=\"sel_cidade.php\">Voltar p/ Listagem de Cidades</a> | " . $dsLinkInicio;
    break;
    case "evento":
      $dsLinkRetorno = "<a href=\"sel_evento.php\">Voltar p/ Listagem de Eventos</a> | " . $dsLinkInicio;
    break;
    case "modalidade":
      $dsLinkRetorno = "<a href=\"sel_modalidade.php\">Voltar p/ Listagem de Modalidades</a> | " . $dsLinkInicio;
    break;
    case "cadastroUsuario":
      $dsLinkRetorno = $dsLinkInicio;
      
      if (!isset($_SESSION["cd_pessoa"]))
        $dsLinkRetorno = "<a href=\"man_cadastro_usuario.php\">Cadastre-se</a> | <a href=\"login.php\">Voltar ao Inicio</a>";
    break;
    case "login":
    case "extratoUsuario":
      $dsLinkRetorno = $dsLinkInicio;
    break;
  }
  
  $dsLinkRetorno = "<p>" . $dsLinkRetorno . "</p>";
  
  if (isset($_REQUEST["dsMensagem"]))
    $dsMsg = urldecode($_REQUEST["dsMensagem"]);
  
  $divTable = "<div class=\"container\">
                 <h3>Ops!</h3>
                 <table class=\"erro\">
                   <tr>
                     <th>STATUS</th>
                     <td><b>{$dsMsg}</b></td>
                   </tr>
                 </table>
                 {$dsLinkRetorno}
               </div>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <title>ERRO</title>
  </head>
  <body>
    <?php echo $divTable ?>
  </body>
</html>
