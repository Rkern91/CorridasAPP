<?php
  require_once("../session.php");

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

  $dsLinkInicio = "<a href='index.php'>Voltar ao Início</a>";

  switch ($_REQUEST["dsOrigem"] ?? "")
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
    case "inscricao":
      $dsLinkRetorno = "<a href=\"sel_inscricao.php\">Voltar p/ Minhas Inscrições</a> | " . $dsLinkInicio;
    break;
    case "cadastroUsuario":
      $dsLinkRetorno = $dsLinkInicio;

      if (!isset($_SESSION["cd_pessoa"]))
        $dsLinkRetorno = "<a href=\"man_cadastro_usuario.php\">Cadastre-se</a> | <a href=\"login.php\">Voltar ao Login</a>";
    break;
    case "login":
    case "extratoUsuario":
      $dsLinkRetorno = $dsLinkInicio;
    break;
  }

  if (isset($_REQUEST["dsMensagem"]))
    $dsMsg = urldecode($_REQUEST["dsMensagem"]);

  $tituloPagina  = "Erro";
  $layoutSidebar = false;
  require("header.php");
?>
  <h3>Ops!</h3>
  <div class="alert alert-danger"><?= h($dsMsg) ?></div>
  <p><?= $dsLinkRetorno ?></p>
<?php require("footer.php"); ?>
