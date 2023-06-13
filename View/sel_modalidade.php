<?php
  require_once("../Controllers/ModalidadeController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
  <?php include("head.php"); ?>
  <body>
    <?php
      try
      {
        $ModalidadeController   = new ModalidadeController();
        $formListagemModalidade = $ModalidadeController->ControladorModalidade->montarFormListagemModalidade();
        
        echo $formListagemModalidade;
      }
      catch (Exception $e)
      {
        $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
        header("Location: erro.php?dsOrigem=modalidade&dsMensagem=" . urlencode($error_message));
        exit;
      }
    ?>
  </body>
  <?php include("footer.html");?>
</html>