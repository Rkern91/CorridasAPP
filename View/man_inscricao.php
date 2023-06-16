<?php
  require_once("../Controllers/InscricaoController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<?php require_once("head.php"); ?>
<body>
<div class="container">
  <h3>Inscrição</h3>
  <?php
    try
    {
      $InscricaoController     = new InscricaoController();
      $formManutencaoInscricao = $InscricaoController->ControladorInscricao->montarFormCadastroEvento();
      
      echo $formManutencaoInscricao;
    }
    catch (Exception $e)
    {
      $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
      header("Location: erro.php?dsOrigem=inscricao&dsMensagem=" . urlencode($error_message));
      exit;
    }
  ?>
  <p><a href="sel_evento.php">Listagem de Eventos</a> | <a href="index.php">Voltar ao Início</a></p>
</div>
</body>
<?php include("footer.html");?>
</html>

