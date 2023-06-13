<?php
  session_start();
  
  if (!isset($_SESSION["ds_email_usuario"])) {
    header("location: ../View/login.php");
  }