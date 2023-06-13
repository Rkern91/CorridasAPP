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
        echo "<script>
                  alert('Usuário ou senha incorretos');
              </script>";
      }
      else
      {
        $_SESSION["id_tipo_usuario"]  = $this->Usuario->getCdIdTipo();
        $_SESSION["cd_pessoa"]        = $this->Usuario->getCdPessoa();
        
        echo "<script>
                alert('Login realizado com sucesso!');
                
              </script>";
        
        $dsMsgSucess = "Login relizado com sucesso!";
        header("Location: index.php?dsOrigem=login&dsMensagem=" . urlencode($dsMsgSucess));
      }
    }
  }