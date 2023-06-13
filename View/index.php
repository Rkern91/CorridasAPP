<?php
  require_once("../Controllers/TelaUserLoginController.php");
  require_once("../helpers.inc.php");
  
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <?php include("head.php") ?>
  <body>
    <div class="container">
      <?php new TelaUserLoginController(); ?>
    </div>
  </body>
  <?php include("footer.html");?>
</html>
