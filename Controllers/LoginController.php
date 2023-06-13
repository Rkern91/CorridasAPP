<?php
  require_once("../Model/Usuario.php");
  require_once("../helpers.inc.php");
  session_start();
  
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
     * Executa o login do usuario e direciona para a tela de entrada.
     * @throws Exception
     */
    public function realizarLoginUsuario()
    {
      if (!$this->Usuario->realizarLogin())
      {
        session_destroy();
        echo "<h1>Usuário ou senha incorretos.</h1>";
      }
      else
      {
        $_SESSION["id_tipo_usuario"]  = $this->Usuario->getCdIdTipo();
        $_SESSION["ds_email_usuario"] = $this->Usuario->getDsEmail();
        
        $dsMsgSucess = "Login relizado com sucesso!";
        header("Location: index.php?dsOrigem=login&dsMensagem=" . urlencode($dsMsgSucess));
      }
    }
  }