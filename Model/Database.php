<?php
  require_once(__DIR__ . "/../init.php");

  /**
   * Camada intermediadora única de acesso ao banco de dados (PostgreSQL).
   *
   * Centraliza a conexão e executa toda consulta/comando via prepared
   * statements (parâmetros $1, $2...), evitando SQL Injection. Expõe o
   * último ID retornado por RETURNING e oferece suporte a transações.
   */
  class Database
  {
    const ID_ERRO_CONEXAO_BANCO = 1;
    const ID_ERRO_CONFIG_BANCO  = 2;
    const ID_ERRO_INSERT        = 3;
    const ID_ERRO_UPDATE        = 4;
    const ID_ERRO_DELETE        = 5;
    const ID_ERRO_CONSULTA      = 6;

    /**
     * Mapeia a ação do formulário ao código de erro correspondente,
     * usado para direcionar à tela de erros.
     * @var array
     */
    public static array $opIdErrosBd = [
      "inserir"   => self::ID_ERRO_INSERT,
      "atualizar" => self::ID_ERRO_UPDATE,
      "deletar"   => self::ID_ERRO_DELETE
    ];

    /**
     * Código do último registro retornado por uma cláusula RETURNING.
     * @var int
     */
    protected int $lastInsertId = 0;

    /**
     * Objeto de conexão ao banco de dados.
     * @var resource|\PgSql\Connection
     */
    protected $objConn;

    /**
     * Abre a conexão lendo a configuração centralizada.
     * @throws Exception
     */
    public function __construct()
    {
      $arrConfig = require(__DIR__ . "/../config/database.php");
      $this->conectar($arrConfig);
    }

    /**
     * Encerra a conexão ao destruir o objeto.
     */
    public function __destruct()
    {
      if ($this->objConn)
        pg_close($this->objConn);
    }

    /**
     * Estabelece a conexão a partir dos parâmetros de configuração.
     * @throws Exception
     */
    protected function conectar(array $arrConfig): void
    {
      $dsParametros = sprintf(
        "host=%s port=%d dbname=%s user=%s password=%s",
        $arrConfig["host"],
        $arrConfig["port"],
        $arrConfig["dbname"],
        $arrConfig["user"],
        $arrConfig["password"]
      );

      $this->objConn = @pg_connect($dsParametros);

      if (!$this->objConn)
        throw new Exception("Erro ao conectar com o Banco de Dados!", self::ID_ERRO_CONEXAO_BANCO);
    }

    /**
     * Executa uma consulta (SELECT) parametrizada e retorna as linhas.
     *
     * @param string $sqlConsulta SQL com placeholders $1, $2...
     * @param array  $arrParams   Valores que substituem os placeholders.
     * @return array Linhas associativas (vazio quando não há resultados).
     * @throws Exception
     */
    public function select(string $sqlConsulta, array $arrParams = []): array
    {
      $dsResultado = $this->executarSql($sqlConsulta, $arrParams, self::ID_ERRO_CONSULTA);
      $arrLinhas   = pg_fetch_all($dsResultado, PGSQL_ASSOC);

      return $arrLinhas ?: [];
    }

    /**
     * Executa um comando de escrita (INSERT/UPDATE/DELETE) parametrizado.
     * Quando o comando usa RETURNING, o valor é guardado em lastInsertId().
     *
     * @param string $sqlComando SQL com placeholders $1, $2...
     * @param array  $arrParams  Valores que substituem os placeholders.
     * @return int Quantidade de linhas afetadas.
     * @throws Exception
     */
    public function execute(string $sqlComando, array $arrParams = []): int
    {
      $dsResultado = $this->executarSql($sqlComando, $arrParams, self::ID_ERRO_INSERT);

      if (pg_num_fields($dsResultado) > 0 && pg_num_rows($dsResultado) > 0)
        $this->lastInsertId = (int) pg_fetch_result($dsResultado, 0, 0);

      return pg_affected_rows($dsResultado);
    }

    /**
     * Retorna o código gerado pela última cláusula RETURNING.
     * @return int
     */
    public function lastInsertId(): int
    {
      return $this->lastInsertId;
    }

    /**
     * Inicia uma transação.
     * @throws Exception
     */
    public function begin(): void
    {
      $this->executarComando("BEGIN");
    }

    /**
     * Confirma a transação corrente.
     * @throws Exception
     */
    public function commit(): void
    {
      $this->executarComando("COMMIT");
    }

    /**
     * Desfaz a transação corrente.
     * @throws Exception
     */
    public function rollback(): void
    {
      $this->executarComando("ROLLBACK");
    }

    /**
     * Executa um SQL parametrizado e devolve o resultado bruto,
     * lançando exceção em caso de falha.
     *
     * @param string $sql
     * @param array  $arrParams
     * @param int    $idErro
     * @return resource|\PgSql\Result
     * @throws Exception
     */
    protected function executarSql(string $sql, array $arrParams, int $idErro)
    {
      $dsResultado = pg_query_params($this->objConn, $sql, $arrParams);

      if (!$dsResultado)
        throw new Exception(pg_last_error($this->objConn), $idErro);

      return $dsResultado;
    }

    /**
     * Executa um comando simples sem parâmetros (controle de transação).
     * @throws Exception
     */
    protected function executarComando(string $sql): void
    {
      if (!pg_query($this->objConn, $sql))
        throw new Exception(pg_last_error($this->objConn), self::ID_ERRO_CONEXAO_BANCO);
    }
  }
