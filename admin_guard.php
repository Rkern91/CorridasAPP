<?php
  // Protege telas administrativas: exige usuário autenticado E perfil admin (cd_id_tipo = 1).
  // Deve ser incluído no topo da View, antes de qualquer saída.
  require_once(__DIR__ . "/auth_guard.php");

  if (($_SESSION["id_tipo_usuario"] ?? null) != 1)
  {
    $dsMensagem = "Acesso negado: área restrita a administradores.";
    header("Location: ../View/erro.php?dsOrigem=login&dsMensagem=" . urlencode($dsMensagem));
    exit;
  }
