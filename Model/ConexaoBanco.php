<?php
  require_once("../init.php");
  
  class ConexaoBanco
  {
    const ID_ERRO_CONEXAO_BANCO = 1;
    const ID_ERRO_CONFIG_BANCO  = 2;
    const ID_ERRO_INSERT        = 3;
    const ID_ERRO_UPDATE        = 4;
    const ID_ERRO_DELETE        = 5;
    
    public static array $opIdErrosBd = [
      "inserir" => self::ID_ERRO_INSERT,
      "update"  => self::ID_ERRO_UPDATE,
      "delete"  => self::ID_ERRO_DELETE
    ];
    
    /**
     * Código do último registro inserido em uma
     * operação de update ou insert.
     * @var int
     */
    protected $lastQueryId;
    
    /**
     * Armazena o resultado da consulta
     * @var array
     */
    protected array $lastQueryResults;
    
    /**
     * Armazena erros ocorridos em um SQL executado pelo processo
     * de exclusão ou inserção.
     * @var string
     */
    protected string $lastQueryError;
    
    /**
     * Objeto de Conexao ao Banco de Dados
     * @var $objConn
     */
    protected $objConn;
    
    /**
     * Estabelece a conexão com o Banco de Dados
     * e retorna o objeto de conexão para futuras operações.
     *
     * @throws Exception
     */
    public function __construct()
    {
      try
      {
        $this->obtemConn();
      }
      catch (Exception $e)
      {
        throw new Exception("Erro ao conectar com o Banco de Dados!", self::ID_ERRO_CONEXAO_BANCO);
      }
    }
    
    public function __destruct()
    {
      $this->encerrarConn();
    }
    
    /**
     * Define a conexão com o Banco de Dados.
     */
    protected function obtemConn()
    {
      $connParams    = sprintf("host=%s port=%d dbname=%s user=%s password=%s", HOST, PORT, DBNAME, USER, PWD);
      $this->objConn = pg_connect($connParams);
    }
    
    /**
     * Encerra conexao ao Banco de Dados
     * @return void
     */
    protected function encerrarConn()
    {
      pg_close($this->objConn);
    }
    
    /**
     * Executa os SQLs gerados em funções
     * durante o processo, devolvendo o resultado de consultas.
     *
     * @param        $sqlDados
     * @param string $dsOp
     * @return bool
     */
    public function runQueryes($sqlDados, string $dsOp = "selecionar"): bool
    {
      $dsRetorno = pg_query($this->objConn, $sqlDados);
      
      //Se ocorreu erro ao executar o SQL, seta o atributo com a descrição do erro.
      if (!$dsRetorno)
      {
        $this->setLastQueryError(pg_last_error($this->objConn));
        return false;
      }
      else
      {
        if ($dsOp == "selecionar")
          $this->setLastQueryResults(pg_fetch_all($dsRetorno));
        
        if ($dsOp == "inserir" || $dsOp == "atualizar")
          $this->setLastQueryId(pg_fetch_row($dsRetorno)[0]);
      }

      return true;
    }
    
    /**
     * Retorna a descrição do último erro ocorrido.
     * @return string
     */
    public function getLastQueryError(): string
    {
      return $this->lastQueryError;
    }
    
    /**
     * Define a descrição do último erro ocorrido.
     * @param string $lastQueryError
     */
    protected function setLastQueryError(string $lastQueryError): void
    {
      $this->lastQueryError = $lastQueryError;
    }
    
    /**
     * @return int
     */
    public function getLastQueryId(): int
    {
      return $this->lastQueryId;
    }
    
    /**
     * @param int $lastQueryId
     */
    protected function setLastQueryId(int $lastQueryId): void
    {
      $this->lastQueryId = $lastQueryId;
    }
    
    /**
     * Retorna o resulta da consulta armazenado.
     * @return array
     */
    public function getLastQueryResults(): array
    {
      return $this->lastQueryResults;
    }
    
    /**
     * Armazena o resultado da consulta que será usado
     * na classe que instanciou a conexão ao banco.
     *
     * @param $lastQueryResults
     */
    protected function setLastQueryResults($lastQueryResults): void
    {
      if (isset($lastQueryResults) && $lastQueryResults > 0)
        $this->lastQueryResults = $lastQueryResults;
      else
        $this->lastQueryResults = [];
    }
  }