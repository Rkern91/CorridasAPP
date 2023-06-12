<?php
  session_start();
  
  if (!isset($_SESSION["ds_email"])) {
    header("location: ../View/login.php");
  }