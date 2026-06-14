<?php
  require_once("../Model/FormEvento.php");

  class EventoController
  {
    /**
     * Model de evento.
     * @var FormEvento
     */
    private FormEvento $FormEvento;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->FormEvento = new FormEvento($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemEventos(): array
    {
      return $this->FormEvento->obterListagemEventos();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterDadosEvento(): array
    {
      return $this->FormEvento->obterDadosEvento();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterCidades(): array
    {
      return $this->FormEvento->obterCidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterModalidades(): array
    {
      return $this->FormEvento->obterModalidades();
    }
  }
