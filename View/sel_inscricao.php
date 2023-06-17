<?php
  require_once("../Controllers/InscricaoController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<?php require_once("head.php"); ?>
<body>
<div class="container">
  <?php
    try
    {
      $InscricaoController     = new InscricaoController();
      $formManutencaoInscricao = $InscricaoController->ControladorInscricao->montarFormListagemCadastro();
      
      echo $formManutencaoInscricao;
    }
    catch (Exception $e)
    {
      $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
      header("Location: erro.php?dsOrigem=inscricao&dsMensagem=" . urlencode($error_message));
      exit;
    }
  ?>
  
</div>
</body>
<?php include("footer.html");?>
</html>

