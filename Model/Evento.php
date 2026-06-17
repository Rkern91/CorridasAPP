<?php
  require_once("Database.php");
  require_once("../helpers.inc.php");

  class Evento
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

      if (isset($arrRequest["dt_evento"]))
        $this->arrRequest["dt_completa"] = "{$arrRequest["dt_evento"]} {$arrRequest["hr_evento"]}:00";
    }

    /**
     * Adiciona a ligação das modalidades com o evento criado.
     * @throws Exception
     */
    protected function adicionarModalidadesEvento()
    {
      $cdEventoNovo = $this->arrRequest["cd_evento"] ?? $this->Database->lastInsertId();

      foreach (explode(",", $this->arrRequest["arr_cd_modalidades"]) as $cdModalidade)
      {
        $sqlInsertModalidade = "INSERT INTO modalidade_evento (cd_modalidade, cd_evento)
                                     VALUES ($1, $2)
                                  RETURNING cd_evento";

        $this->Database->execute($sqlInsertModalidade, [$cdModalidade, $cdEventoNovo]);
      }
    }

    /**
     * Atualiza o registro selecionado.
     *
     * @return void
     * @throws Exception
     */
    public function atualizarAcao()
    {
      $sqlEvento = "UPDATE evento
                           SET nm_evento = $1,
                               dt_evento = $2,
                               cd_cidade = $3
                         WHERE cd_evento = $4
                     RETURNING cd_evento";

      try
      {
        $this->Database->begin();

        $this->Database->execute($sqlEvento, [
          $this->arrRequest["nm_evento"],
          $this->arrRequest["dt_completa"],
          $this->arrRequest["cd_cidade"],
          $this->arrRequest["cd_evento"]
        ]);

        $this->removerDependenciasEvento();
        $this->adicionarModalidadesEvento();

        $this->Database->commit();
      }
      catch (Exception $e)
      {
        $this->Database->rollback();
        throw $e;
      }
    }

    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      try
      {
        $this->Database->begin();

        //Remove as dependencias antes de remover o evento
        $this->removerDependenciasEvento();
        $this->Database->execute("DELETE FROM evento WHERE cd_evento = $1", [$this->arrRequest["cd_evento"]]);

        $this->Database->commit();
      }
      catch (Exception $e)
      {
        $this->Database->rollback();
        throw $e;
      }
    }

    /**
     * Insere um novo registro de evento.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $sqlEvento =<<<SQL
        INSERT INTO evento (nm_evento, dt_evento, cd_cidade)
             VALUES ($1, $2, $3)
          RETURNING cd_evento
SQL;

      try
      {
        $this->Database->begin();

        $this->Database->execute($sqlEvento, [
          $this->arrRequest["nm_evento"],
          $this->arrRequest["dt_completa"],
          $this->arrRequest["cd_cidade"]
        ]);

        $this->adicionarModalidadesEvento();

        $this->Database->commit();
      }
      catch (Exception $e)
      {
        $this->Database->rollback();
        throw $e;
      }
    }

    /**
     * Retorna a lista de eventos (com modalidades agregadas) para a listagem.
     *
     * @return array
     * @throws Exception
     */
    public function obterListagemEventos(): array
    {
      $sqlEvento =<<<SQL
        SELECT e.cd_evento,
               e.nm_evento,
               c.nm_cidade,
               TO_CHAR(e.dt_evento, 'DD/MM/YYYY HH:MI') AS dt_evento,
               ARRAY_AGG(m.ds_descricao)                AS ds_descricacao,
               ARRAY_AGG(m.vl_km_distancia || 'km')     AS ds_modalidades
          FROM modalidade_evento me
          JOIN evento             e ON     e.cd_evento = me.cd_evento
          JOIN modalidade         m ON m.cd_modalidade = me.cd_modalidade
          JOIN cidade             c ON     c.cd_cidade = e.cd_cidade
         GROUP BY e.cd_evento, e.nm_evento, e.dt_evento, c.nm_cidade
         ORDER BY e.cd_evento, e.nm_evento
SQL;

      return $this->Database->select($sqlEvento);
    }

    /**
     * Retorna os dados de um evento (para edição), com as modalidades
     * agregadas em arrays do PostgreSQL.
     *
     * @return array
     * @throws Exception
     */
    public function obterDadosEvento(): array
    {
      $sqlEvento =<<<SQL
        SELECT e.cd_evento,
               e.nm_evento,
               c.cd_cidade,
               TO_CHAR(e.dt_evento, 'YYYY-MM-DD')                   AS dt_evento,
               TO_CHAR(e.dt_evento, 'HH:MI')                        AS hr_evento,
               COALESCE(ARRAY_AGG(m.cd_modalidade),           '{}') AS arr_cd_modalidades,
               COALESCE(ARRAY_AGG(m.vl_km_distancia || 'km'), '{}') AS arr_km_distancia,
               COALESCE(COUNT(m.cd_modalidade),                  0) AS qt_modalidade
          FROM modalidade_evento me
          JOIN evento             e ON     e.cd_evento = me.cd_evento
          JOIN modalidade         m ON m.cd_modalidade = me.cd_modalidade
          JOIN cidade             c ON     c.cd_cidade = e.cd_cidade
         WHERE e.cd_evento = $1
         GROUP BY e.cd_evento, e.nm_evento, e.dt_evento, c.cd_cidade
         ORDER BY e.cd_evento, e.nm_evento;
SQL;

      $arrEvento = $this->Database->select($sqlEvento, [$this->arrRequest["cd_evento"]]);

      return $arrEvento[0] ?? [];
    }

    /**
     * Retorna a lista de cidades para popular o campo de seleção.
     *
     * @return array Lista de [value, description].
     * @throws Exception
     */
    public function obterCidades(): array
    {
      $sqlCidades =<<<SQL
        SELECT c.cd_cidade                        AS value,
               c.nm_cidade || ' / ' || u.ds_sigla AS description
          FROM cidade c
          JOIN uf     u ON u.cd_uf = c.cd_uf
         ORDER BY c.nm_cidade
SQL;

      return $this->Database->select($sqlCidades);
    }

    /**
     * Retorna a lista de modalidades para popular o campo de seleção.
     *
     * @return array Lista de [value, description].
     * @throws Exception
     */
    public function obterModalidades(): array
    {
      $sqlModalidades =<<<SQL
        SELECT m.cd_modalidade                              AS value,
               m.vl_km_distancia || ' / ' || m.ds_descricao AS description
          FROM modalidade m
         ORDER BY m.vl_km_distancia
SQL;

      return $this->Database->select($sqlModalidades);
    }

    /**
     * Remove qualquer ligação ao evento da tabela modalidade_evento.
     *
     * @return void
     * @throws Exception
     */
    protected function removerDependenciasEvento()
    {
      //TODO: Ao remover evento deve considerar inscrições já realizadas nas modalidades do evento
      $arrPendencias = [
        "inscricao",
        "modalidade_evento"
      ];

      foreach ($arrPendencias as $dsTablePendencia)
      {
        $sqlPendenciasCidade =<<<SQL
        DELETE FROM {$dsTablePendencia}
         WHERE cd_evento = $1
SQL;

        $this->Database->execute($sqlPendenciasCidade, [$this->arrRequest["cd_evento"]]);
      }
    }
  }
