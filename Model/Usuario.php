<?php
  require_once("ConexaoBanco.php");
  require_once("../helpers.inc.php");
  
  class Usuario
  {
    /**
     * Classe de Conexao ao Banco de Dados
     * @var ConexaoBanco
     */
    private ConexaoBanco $ConexaoBanco;
    
    private string $nm_pessoa;
    private string $ds_email;
    private string $ds_senha;
    private int $cd_id_tipo;
    
    public function __construct($ds_email, $ds_senha)
    {
      $this->setDsEmail($ds_email);
      $this->setDsSenha($ds_senha);
      
      $this->ConexaoBanco = new ConexaoBanco();
    }
    
    /**
     * @return string
     */
    public function getNmPessoa(): string
    {
      return $this->nm_pessoa;
    }
    
    /**
     * @param string $nm_pessoa
     */
    private function setNmPessoa(string $nm_pessoa): void
    {
      $this->nm_pessoa = $nm_pessoa;
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
     * Realiza login com os dados submetidos no formulário
     * @return bool
     * @throws Exception
     */
    public function realizarLogin(): bool
    {
      $sqlLoginUsuario =<<<SQL
        SELECT ds_email, cd_id_tipo FROM pessoa WHERE ds_email = '{$this->getDsEmail()}'
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlLoginUsuario))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrLoginUsuario = $this->ConexaoBanco->getLastQueryResults()[0];

      if (hasValue($arrLoginUsuario["ds_email"]))
      {
        $this->setCdIdTipo($arrLoginUsuario["cd_id_tipo"]);
        return true;
      }
      
      return false;
    }
  }