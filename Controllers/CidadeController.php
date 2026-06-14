<?php
  require_once("../Model/FormCidade.php");

  class CidadeController
  {
    /**
     * Model de cidade.
     * @var FormCidade
     */
    private FormCidade $FormCidade;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->FormCidade = new FormCidade($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemCidades(): array
    {
      return $this->FormCidade->obterListagemCidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterCidade(): array
    {
      return $this->FormCidade->obterCidade();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterEstados(): array
    {
      return $this->FormCidade->obterEstados();
    }
  }
