<?php
  require_once("../Model/FormModalidade.php");
  
  class ModalidadeController
  {
    /**
     * @var FormModalidade $ControladorModalidade
     */
    public FormModalidade $ControladorModalidade;
    
    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      try
      {
        $this->ControladorModalidade = new FormModalidade($_REQUEST);
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