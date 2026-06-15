<?php
  require_once("../Model/FormModalidade.php");

  class ModalidadeController
  {
    /**
     * Model de modalidade.
     * @var FormModalidade
     */
    private FormModalidade $FormModalidade;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->FormModalidade = new FormModalidade($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemModalidades(): array
    {
      return $this->FormModalidade->obterListagemModalidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterModalidade(): array
    {
      return $this->FormModalidade->obterModalidade();
    }
  }
