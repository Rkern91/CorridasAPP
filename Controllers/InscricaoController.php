<?php
  require_once("../Model/FormInscricao.php");

  class InscricaoController
  {
    /**
     * Model de inscrição.
     * @var FormInscricao
     */
    private FormInscricao $FormInscricao;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->FormInscricao = new FormInscricao($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemInscricoes(): array
    {
      return $this->FormInscricao->obterListagemInscricoes();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterDadosEventoInscricao(): array
    {
      return $this->FormInscricao->obterDadosEventoInscricao();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterModalidadesEvento(): array
    {
      return $this->FormInscricao->obterModalidadesEvento();
    }
  }
