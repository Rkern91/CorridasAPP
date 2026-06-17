<?php
  require_once("../Model/Evento.php");

  class EventoController
  {
    /**
     * Model de evento.
     * @var Evento
     */
    private Evento $Evento;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->Evento = new Evento($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemEventos(): array
    {
      return $this->Evento->obterListagemEventos();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterDadosEvento(): array
    {
      return $this->Evento->obterDadosEvento();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterCidades(): array
    {
      return $this->Evento->obterCidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterModalidades(): array
    {
      return $this->Evento->obterModalidades();
    }
  }
