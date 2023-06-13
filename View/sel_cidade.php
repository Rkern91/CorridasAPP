<?php
  require_once("../Controllers/CidadeController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
  <?php include("head.php"); ?>
  <body>
    <?php
      try
      {
        $CidadeController   = new CidadeController();
        $formListagemCidade = $CidadeController->ControladorCidade->montarFormListagemCidade();
        
        echo $formListagemCidade;
      }
      catch (Exception $e)
      {
        $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
        header("Location: erro.php?dsOrigem=cidade&dsMensagem=" . urlencode($error_message));
        exit;
      }
    ?>
  </body>
  <?php include("footer.html");?>
</html>