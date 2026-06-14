<?php
  require_once("../Model/Usuario.php");
  require_once("../helpers.inc.php");

  class LoginController
  {
    /**
     * @var Usuario
     */
    protected Usuario $Usuario;
    
    /**
     * @throws Exception
     */
    public function __construct()
    {
      $this->Usuario = new Usuario($_REQUEST["ds_email"], $_REQUEST["ds_senha"]);
    }
    
    /**
     * Executa o login do usuario. Em caso de sucesso, popula a sessão.
     * @return bool true se autenticou, false caso contrário.
     * @throws Exception
     */
    public function realizarLoginUsuario(): bool
    {
      if (!$this->Usuario->realizarLogin())
        return false;

      $_SESSION["id_tipo_usuario"] = $this->Usuario->getCdIdTipo();
      $_SESSION["cd_pessoa"]       = $this->Usuario->getCdPessoa();

      return true;
    }
  }