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

  <?php
    try
    {
      $CadastroUsuarioController     = new CadastroUsuarioController();
      $formManutencaoCadastroUsuario = $CadastroUsuarioController->ControladorCadastroUsuario->montarExtratoDadosUsuario();
      
      echo $formManutencaoCadastroUsuario;
    }
    catch (Exception $e)
    {
      $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
      header("Location: erro.php?dsOrigem=extratoUsuario&dsMensagem=" . urlencode($error_message));
      exit;
    }
  ?>
</body>
<?php include("footer.html");?>
</html>

