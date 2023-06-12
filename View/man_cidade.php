<?php
  require_once("../Controllers/CidadeController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
  <?php include("head.php"); ?>
  <body>
    <div class="container">
      <h3>Manutenção de Cidade</h3>
      <?php
        try
        {
          $CidadeController     = new CidadeController();
          $formManutencaoCidade = $CidadeController->ControladorCidade->montarFormManutencaoCidade();
          
          echo $formManutencaoCidade;
        }
        catch (Exception $e)
        {
          $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
          header("Location: ../erro.php?dsOrigem=cidade&dsMensagem=" . urlencode($error_message));
          exit;
        }
      ?>
      <p><a href="sel_cidade.php">Listagem de Cidade</a></p>
    </div>
  </body>
</html>

