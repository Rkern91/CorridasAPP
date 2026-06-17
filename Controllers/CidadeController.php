<?php
  require_once("../Model/Cidade.php");

  class CidadeController
  {
    /**
     * Model de cidade.
     * @var Cidade
     */
    private Cidade $Cidade;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->Cidade = new Cidade($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemCidades(): array
    {
      return $this->Cidade->obterListagemCidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterCidade(): array
    {
      return $this->Cidade->obterCidade();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterEstados(): array
    {
      return $this->Cidade->obterEstados();
    }
  }
