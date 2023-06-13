<?php
  require_once("../Controllers/TelaUserLoginController.php");
  
  $dsCampoHidden = "";
  
  //Define a operacao executada ao chamar a tela e cria um alerta
  if (isset($_REQUEST["id_operacao"]))
    $dsCampoHidden .= "<input type=\"hidden\" id=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">";
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <?php include("head.php") ?>
  <body>
    <div class="container">
      <?php
        new TelaUserLoginController();
        echo $dsCampoHidden;
      ?>
    </div>
  </body>
  <?php include("footer.html");?>
</html>
