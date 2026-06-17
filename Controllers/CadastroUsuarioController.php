<?php
  require_once("../Model/Pessoa.php");

  class CadastroUsuarioController
  {
    /**
     * Model de usuário.
     * @var Pessoa
     */
    private Pessoa $Pessoa;

    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      $this->Pessoa = new Pessoa($_REQUEST);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterDadosPessoa(): array
    {
      return $this->Pessoa->obterDadosPessoa();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterExtratoUsuario(): array
    {
      return $this->Pessoa->obterExtratoUsuario();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterCidades(): array
    {
      return $this->Pessoa->obterCidades();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obterListagemUsuarios(): array
    {
      return $this->Pessoa->obterListagemUsuarios();
    }
  }
