<?php
  require_once("../Controllers/EventoController.php");
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<!--  --><?php //require_once("../head.php"); ?>
  <body>
    <div class="container">
      <h3>Manutenção de Evento</h3>
      <?php
        try
        {
          $EventoController     = new EventoController();
          $formManutencaoEvento = $EventoController->ControladorEvento->montarFormManutencaoEvento();
          
          echo $formManutencaoEvento;
        }
        catch (Exception $e)
        {
          $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
          header("Location: erro.php?dsOrigem=evento&dsMensagem=" . urlencode($error_message));
          exit;
        }
      ?>
      <p><a href="sel_evento.php">Listagem de Eventos</a></p>
    </div>
  </body>
</html>

