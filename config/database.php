<?php
  /**
   * Configuração centralizada de conexão ao banco de dados (PostgreSQL).
   *
   * As credenciais são lidas do ambiente (variáveis do contêiner Docker
   * ou do arquivo .env, que NÃO é versionado). Os valores padrão abaixo
   * cobrem apenas dados não sensíveis (host/porta/nome), permitindo que a
   * aplicação suba mesmo sem .env; a senha permanece vazia por padrão.
   */

  // Carrega o .env (se existir) para o ambiente, sem sobrescrever
  // variáveis já definidas pelo contêiner.
  $dsCaminhoEnv = __DIR__ . "/../.env";

  if (is_readable($dsCaminhoEnv))
  {
    foreach (file($dsCaminhoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $dsLinha)
    {
      $dsLinha = trim($dsLinha);

      if ($dsLinha === "" || $dsLinha[0] === "#")
        continue;

      [$nmChave, $dsValor] = array_pad(explode("=", $dsLinha, 2), 2, "");
      $nmChave = trim($nmChave);
      $dsValor = trim($dsValor);

      if ($nmChave !== "" && getenv($nmChave) === false)
        putenv("{$nmChave}={$dsValor}");
    }
  }

  return [
    "host"     => getenv("DB_HOST") ?: "bdpostgres",
    "port"     => getenv("DB_PORT") ?: "5432",
    "dbname"   => getenv("DB_NAME") ?: "runningdb",
    "user"     => getenv("DB_USER") ?: "runningdb",
    "password" => getenv("DB_PWD")  ?: ""
  ];
