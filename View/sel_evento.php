<?php
  require_once("../Controllers/EventoController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<?php include("head.php"); ?>
<body>
<?php
  try
  {
    $EventoController   = new EventoController();
    $formListagemEvento = $EventoController->ControladorEvento->montarFormListagemEvento();
    
    echo $formListagemEvento;
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=evento&dsMensagem=" . urlencode($error_message));
    exit;
  }
?>
</body>
<?php include("footer.html");?>
</html>