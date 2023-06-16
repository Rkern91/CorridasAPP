<?php
  require_once("../Model/FormInscricao.php");
  
  class InscricaoController
  {
    /**
     * @var FormInscricao $ControladorInscricao
     */
    public FormInscricao $ControladorInscricao;
    
    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      try
      {
        $this->ControladorInscricao = new FormInscricao($_REQUEST);
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