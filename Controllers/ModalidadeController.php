<?php
  require_once("../Model/Modalidade.php");

  class ModalidadeController
  {
    /**
     * Model de modalidade.
     * @var Modalidade
     */
    private Modalidade $Modalidade;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->Modalidade = new Modalidade($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemModalidades(): array
    {
      return $this->Modalidade->obterListagemModalidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterModalidade(): array
    {
      return $this->Modalidade->obterModalidade();
    }
  }
