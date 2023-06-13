<?php
  require_once("../Controllers/CadastroUsuarioController.php");
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
  <h3>Cadastro de Usuário</h3>
  <?php
    try
    {
      $CadastroUsuarioController     = new CadastroUsuarioController();
      $formManutencaoCadastroUsuario = $CadastroUsuarioController->ControladorCadastroUsuario->montarFormManutencaoCadastroUsuario();
      
      echo $formManutencaoCadastroUsuario;
    }
    catch (Exception $e)
    {
      $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
      header("Location: erro.php?dsOrigem=cadastroUsuario&dsMensagem=" . urlencode($error_message));
      exit;
    }
  ?>
  <p><a href="login.php">Voltar p/ Login</a></p>
</div>
</body>
<?php include("footer.html");?>
</html>

