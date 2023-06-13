<?php
  require_once("../Model/FormUsuario.php");
  
  class CadastroUsuarioController
  {
    /**
     * @var FormUsuario $ControladorCadastroUsuario
     */
    public FormUsuario $ControladorCadastroUsuario;
    
    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      try
      {
        $this->ControladorCadastroUsuario = new FormUsuario($_REQUEST);
      }
      catch (Exception $e)
      {
        $this->tratarErros($e->getMessage(), $e->getCode());
      }
    }
    
    private function tratarErros($dsErro, $codErro)
    {
      //TODO: Fazer algo com o erro.
    }
  }