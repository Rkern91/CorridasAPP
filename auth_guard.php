<?php
  // Protege telas internas: exige usuário autenticado.
  // Deve ser incluído no topo da View, antes de qualquer saída.
  require_once(__DIR__ . "/session.php");

  if (!isset($_SESSION["cd_pessoa"]))
  {
    header("Location: ../View/login.php");
    exit;
  }
