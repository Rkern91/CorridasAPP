<?php
  require_once("../Controllers/ModalidadeController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<?php require_once("head.php"); ?>
<body>
<div class="container">
  <h3>Manutenção de Modalidade</h3>
  <?php
    try
    {
      $ModalidadeController     = new ModalidadeController();
      $formManutencaoModalidade = $ModalidadeController->ControladorModalidade->montarFormManutencaoModalidade();
      
      echo $formManutencaoModalidade;
    }
    catch (Exception $e)
    {
      $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
      header("Location: erro.php?dsOrigem=modalidade&dsMensagem=" . urlencode($error_message));
      exit;
    }
  ?>
  <p><a href="sel_modalidade.php">Listagem de Modalidades</a></p>
</div>
</body>
<?php include("footer.html");?>
</html>

