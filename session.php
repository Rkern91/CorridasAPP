<?php
  // Inicia a sessão de forma idempotente (seguro chamar mais de uma vez).
  if (session_status() === PHP_SESSION_NONE)
    session_start();
