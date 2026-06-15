<?php
  require_once("Database.php");
  require_once("../helpers.inc.php");

  class Usuario
  {
    /**
     * Camada de acesso ao Banco de Dados
     * @var Database
     */
    private Database $Database;

    private int $cd_pessoa;
    private string $ds_email;
    private string $ds_senha;
    private int $cd_id_tipo;
    
    public function __construct($ds_email, $ds_senha)
    {
      $this->setDsEmail($ds_email);
      $this->setDsSenha($ds_senha);

      $this->Database = new Database();
    }
    
    /**
     * @return string
     */
    public function getDsEmail(): string
    {
      return $this->ds_email;
    }
    
    /**
     * @param string $ds_email
     */
    private function setDsEmail(string $ds_email): void
    {
      $this->ds_email = $ds_email;
    }
    
    /**
     * @return string
     */
    public function getDsSenha(): string
    {
      return $this->ds_senha;
    }
    
    /**
     * @param string $ds_senha
     */
    private function setDsSenha(string $ds_senha): void
    {
      $this->ds_senha = $ds_senha;
    }
    
    /**
     * @return int
     */
    public function getCdIdTipo(): int
    {
      return $this->cd_id_tipo;
    }
    
    /**
     * @param int $cd_id_tipo
     */
    private function setCdIdTipo(int $cd_id_tipo): void
    {
      $this->cd_id_tipo = $cd_id_tipo;
    }
    
    /**
     * @return int
     */
    public function getCdPessoa(): int
    {
      return $this->cd_pessoa;
    }
    
    /**
     * @param int $cd_pessoa
     */
    public function setCdPessoa(int $cd_pessoa): void
    {
      $this->cd_pessoa = $cd_pessoa;
    }
    
    /**
     * Realiza login com os dados submetidos no formulário
     * @return bool
     * @throws Exception
     */
    public function realizarLogin(): bool
    {
      $sqlLoginUsuario =<<<SQL
        SELECT cd_pessoa, cd_id_tipo, ds_senha
          FROM pessoa
         WHERE ds_email = $1
SQL;

      $arrResultado    = $this->Database->select($sqlLoginUsuario, [$this->getDsEmail()]);
      $arrLoginUsuario = $arrResultado ? current($arrResultado) : [];

      if (!isset($arrLoginUsuario["cd_pessoa"]))
        return false;

      $dsSenhaArmazenada = $arrLoginUsuario["ds_senha"];
      $dsSenhaInformada  = $this->getDsSenha();
      $idAutenticado     = password_verify($dsSenhaInformada, $dsSenhaArmazenada);

      // Senha legada em texto puro: autentica e migra para hash (upgrade transparente).
      if (!$idAutenticado && $dsSenhaArmazenada === $dsSenhaInformada)
      {
        $idAutenticado = true;
        $this->atualizarSenhaHash($arrLoginUsuario["cd_pessoa"], $dsSenhaInformada);
      }

      if (!$idAutenticado)
        return false;

      $this->setCdIdTipo($arrLoginUsuario["cd_id_tipo"]);
      $this->setCdPessoa($arrLoginUsuario["cd_pessoa"]);
      return true;
    }

    /**
     * Regrava a senha de uma pessoa já com hash (migração de senha legada).
     * @throws Exception
     */
    private function atualizarSenhaHash(int $cdPessoa, string $dsSenha): void
    {
      $this->Database->execute(
        "UPDATE pessoa SET ds_senha = $1 WHERE cd_pessoa = $2",
        [password_hash($dsSenha, PASSWORD_DEFAULT), $cdPessoa]
      );
    }
  }