<?php
  require_once("../Model/FormUsuario.php");
  session_start();

  class CadastroUsuarioController
  {
    /**
     * Model de usuário.
     * @var FormUsuario
     */
    private FormUsuario $FormUsuario;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->FormUsuario = new FormUsuario($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterDadosPessoa(): array
    {
      return $this->FormUsuario->obterDadosPessoa();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterExtratoUsuario(): array
    {
      return $this->FormUsuario->obterExtratoUsuario();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterCidades(): array
    {
      return $this->FormUsuario->obterCidades();
    }
  }
