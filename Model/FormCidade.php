<?php
  require_once("Database.php");
  require_once("../helpers.inc.php");

  class FormCidade
  {
    /**
     * Camada de acesso ao Banco de Dados
     * @var Database
     */
    private Database $Database;

    /**
     * @var array
     */
    private array $arrRequest;

    /**
     * Construtor de Classe
     * @param $arrRequest
     */
    public function __construct($arrRequest)
    {
      $this->Database   = new Database();
      $this->arrRequest = $arrRequest;
    }

    /**
     * Insere um novo registro de cidade.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $sqlCidade = "INSERT INTO cidade (nm_cidade, cd_uf)
                         VALUES ($1, $2)
                      RETURNING cd_cidade";

      $this->Database->execute($sqlCidade, [
        $this->arrRequest["nm_cidade"],
        $this->arrRequest["cd_uf"]
      ]);
    }

    /**
     * Atualiza o registro selecionado.
     *
     * @return void
     * @throws Exception
     */
    public function atualizarAcao()
    {
      $sqlCidade = "UPDATE cidade
                       SET nm_cidade = $1,
                           cd_uf     = $2
                     WHERE cd_cidade = $3
                 RETURNING cd_cidade";

      $this->Database->execute($sqlCidade, [
        $this->arrRequest["nm_cidade"],
        $this->arrRequest["cd_uf"],
        $this->arrRequest["cd_cidade"]
      ]);
    }

    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      //Se não existem pendencias, entra e remove a cidade
      if (!$this->validarExistenciaPendenciasCidade())
        $this->Database->execute("DELETE FROM cidade WHERE cd_cidade = $1", [$this->arrRequest["cd_cidade"]]);
    }

    /**
     * Retorna a lista de cidades para a tela de listagem.
     *
     * @return array
     * @throws Exception
     */
    public function obterListagemCidades(): array
    {
      $sqlCidades =<<<SQL
        SELECT c.cd_cidade,
               c.nm_cidade || ' / ' || u.ds_sigla AS nm_cidade
          FROM cidade c
          JOIN uf     u ON u.cd_uf = c.cd_uf
         ORDER BY c.nm_cidade
SQL;

      return $this->Database->select($sqlCidades);
    }

    /**
     * Retorna os dados de uma cidade (para edição).
     *
     * @return array
     * @throws Exception
     */
    public function obterCidade(): array
    {
      $sqlCidade =<<<SQL
        SELECT c.cd_cidade,
               c.nm_cidade,
               u.cd_uf
          FROM cidade c
          JOIN uf     u ON u.cd_uf = c.cd_uf
         WHERE c.cd_cidade = $1
SQL;

      $arrCidade = $this->Database->select($sqlCidade, [$this->arrRequest["cd_cidade"]]);

      return $arrCidade[0] ?? [];
    }

    /**
     * Retorna a lista de estados (UF) para popular o campo de seleção.
     *
     * @return array Lista de [value, description].
     * @throws Exception
     */
    public function obterEstados(): array
    {
      $sqlUf =<<<SQL
        SELECT u.cd_uf                         AS value,
               u.ds_uf || ' / ' || u.ds_sigla AS description
          FROM uf u
         ORDER BY u.ds_uf
SQL;

      return $this->Database->select($sqlUf);
    }

    /**
     * Verifica se a cidade atual está ligada a algum evento ou pessoa
     * e bloqueia a exclusão.
     *
     * @return boolean
     * @throws Exception
     */
    protected function validarExistenciaPendenciasCidade() : bool
    {
      $arrPendencias = [
        "pessoa",
        "evento"
      ];

      foreach ($arrPendencias as $dsTablePendencia)
      {
        $sqlPendenciasCidade =<<<SQL
        SELECT COUNT(*) AS qt_eventos
          FROM {$dsTablePendencia} tp
         WHERE tp.cd_cidade = $1
SQL;

        $arrPendenciasCidade = $this->Database->select($sqlPendenciasCidade, [$this->arrRequest["cd_cidade"]]);

        if ($arrPendenciasCidade[0]["qt_eventos"] > 0)
          throw new Exception("A cidade selecionada está ligada a um(a) ou mais {$dsTablePendencia}(s)!");
      }

      return false;
    }
  }
