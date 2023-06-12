<?php
  require_once("../Model/FormEvento.php");
  
  class EventoController
  {
    /**
     * @var FormEvento $ControladorEvento
     */
    public FormEvento $ControladorEvento;
    
    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      try
      {
        $this->ControladorEvento = new FormEvento($_REQUEST);
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