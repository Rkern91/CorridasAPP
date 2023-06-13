<?php
  session_start();
  
  if (!isset($_SESSION["cd_pessoa"])) {
    header("location: ../View/login.php");
  }