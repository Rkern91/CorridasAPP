<?php
  require_once("../Model/FormCidade.php");
  
  class CidadeController
  {
    /**
     * @var FormCidade $ControladorCidade
     */
    public FormCidade $ControladorCidade;
    
    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      try
      {
        $this->ControladorCidade = new FormCidade($_REQUEST);
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