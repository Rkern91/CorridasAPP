<?php
  require_once("../auth_guard.php");
  require_once("../Controllers/CadastroUsuarioController.php");

  try
  {
    $CadastroUsuarioController = new CadastroUsuarioController();
    $arrUsuario                = $CadastroUsuarioController->obterExtratoUsuario();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=extratoUsuario&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $nrTelefone   = padronizaFone($arrUsuario["nr_telefone"] ?? "", "sys", "pt_BR");
  $tituloPagina = "Extrato";
  require("header.php");
?>
  <div class="container">
    <h3>Dados do Usuário</h3>
    <table class="ficha">
      <tr>
        <th>Nome</th>
        <td><?= h($arrUsuario["nm_pessoa"] ?? "") ?></td>
      </tr>
      <tr>
        <th>Dt. Nascimento</th>
        <td><?= h($arrUsuario["dt_nascimento"] ?? "") ?></td>
      </tr>
      <tr>
        <th>Tipo</th>
        <td><?= h($arrUsuario["tipo_usuario"] ?? "") ?></td>
      </tr>
      <tr>
        <th>Cidade</th>
        <td><?= h($arrUsuario["nm_cidade"] ?? "") ?></td>
      </tr>
      <tr>
        <th>Telefone</th>
        <td><?= h($nrTelefone) ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?= h($arrUsuario["ds_email"] ?? "") ?></td>
      </tr>
    </table>
  </div>
<?php require("footer.php"); ?>
