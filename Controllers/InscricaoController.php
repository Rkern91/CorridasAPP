<?php
  require_once("../Model/Inscricao.php");

  class InscricaoController
  {
    /**
     * Model de inscrição.
     * @var Inscricao
     */
    private Inscricao $Inscricao;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->Inscricao = new Inscricao($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemInscricoes(): array
    {
      return $this->Inscricao->obterListagemInscricoes();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterDadosEventoInscricao(): array
    {
      return $this->Inscricao->obterDadosEventoInscricao();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterModalidadesEvento(): array
    {
      return $this->Inscricao->obterModalidadesEvento();
    }
  }
