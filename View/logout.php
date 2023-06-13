<?php
  session_start();
  
  if (isset($_REQUEST["cd_pessoa"]) && $_REQUEST["cd_pessoa"] == $_SESSION["cd_pessoa"])
  {
    session_destroy();
    header("Location: login.php?id_operacao=deslogar");
    exit;
  }