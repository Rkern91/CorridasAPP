<?php
  require_once("../Controllers/CadastroUsuarioController.php");

  try
  {
    $CadastroUsuarioController = new CadastroUsuarioController();
    $arrUsuario                = $CadastroUsuarioController->ControladorCadastroUsuario->obterExtratoUsuario();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=extratoUsuario&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $nrTelefone = padronizaFone($arrUsuario["nr_telefone"] ?? "", "sys", "pt_BR");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CorridasAPP</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="container">
    <h3>Dados do Usuário</h3>
    <table>
      <tr>
        <th>Nome</th>
        <td><?= $arrUsuario["nm_pessoa"] ?? "" ?></td>
      </tr>
      <tr>
        <th>Dt. Nascimento</th>
        <td><?= $arrUsuario["dt_nascimento"] ?? "" ?></td>
      </tr>
      <tr>
        <th>Tipo</th>
        <td><?= $arrUsuario["tipo_usuario"] ?? "" ?></td>
      </tr>
      <tr>
        <th>Cidade</th>
        <td><?= $arrUsuario["nm_cidade"] ?? "" ?></td>
      </tr>
      <tr>
        <th>Telefone</th>
        <td><?= $nrTelefone ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?= $arrUsuario["ds_email"] ?? "" ?></td>
      </tr>
    </table>
    <a href="index.php">Voltar ao Início</a>
  </div>
</body>
<?php include("footer.html");?>
</html>
