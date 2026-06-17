<?php
  require_once("Database.php");
  require_once("../helpers.inc.php");

  class Modalidade
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

      if (isset($this->arrRequest["f_action"]) && $this->arrRequest["f_action"] != "deletar")
      {
        $this->arrRequest["dt_largada"]   = "{$arrRequest["dt_largada_modalidade"]} {$arrRequest["hr_largada_modalidade"]}:00";
        $this->arrRequest["vl_inscricao"] = padronizaMoeda(preg_replace("/[^0-9\s,]/", "", $arrRequest["vl_valor"]), 2, "pt_BR", "sys");
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
      $sqlModalidade = "UPDATE modalidade
                           SET ds_descricao    = $1,
                               vl_valor        = $2,
                               vl_km_distancia = $3,
                               dt_largada      = $4
                         WHERE cd_modalidade   = $5
                     RETURNING cd_modalidade";

      $this->Database->execute($sqlModalidade, [
        $this->arrRequest["ds_descricao"],
        $this->arrRequest["vl_inscricao"],
        $this->arrRequest["vl_km_distancia"],
        $this->arrRequest["dt_largada"],
        $this->arrRequest["cd_modalidade"]
      ]);
    }

    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      //Se não existem dependencias, remove o registro
      if (!$this->validarExistenciaPendenciasModalidade())
        $this->Database->execute("DELETE FROM modalidade WHERE cd_modalidade = $1", [$this->arrRequest["cd_modalidade"]]);
    }

    /**
     * Insere um novo registro de modalidade.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $sqlModalidade =<<<SQL
        INSERT INTO modalidade (ds_descricao, dt_largada, vl_km_distancia, vl_valor)
             VALUES ($1, $2, $3, $4)
          RETURNING cd_modalidade
SQL;

      $this->Database->execute($sqlModalidade, [
        $this->arrRequest["ds_descricao"],
        $this->arrRequest["dt_largada"],
        $this->arrRequest["vl_km_distancia"],
        $this->arrRequest["vl_inscricao"]
      ]);
    }

    /**
     * Retorna a lista de modalidades para a tela de listagem.
     *
     * @return array
     * @throws Exception
     */
    public function obterListagemModalidades(): array
    {
      $sqlModalidades =<<<SQL
        SELECT m.cd_modalidade,
               m.ds_descricao,
               m.vl_valor,
               m.vl_km_distancia || 'KM'                 AS vl_km_distancia,
               TO_CHAR(m.dt_largada, 'DD/MM/YYYY HH:MI') AS dt_largada_modalidade
          FROM modalidade m
         ORDER BY m.ds_descricao
SQL;

      return $this->Database->select($sqlModalidades);
    }

    /**
     * Retorna os dados de uma modalidade (para edição).
     *
     * @return array
     * @throws Exception
     */
    public function obterModalidade(): array
    {
      $sqlModalidade =<<<SQL
        SELECT m.cd_modalidade,
               m.ds_descricao,
               m.vl_valor,
               m.vl_km_distancia                   AS vl_km_distancia,
               TO_CHAR(m.dt_largada, 'YYYY-MM-DD') AS dt_largada_modalidade,
               TO_CHAR(m.dt_largada, 'HH:MI')      AS hr_largada_modalidade
          FROM modalidade m
         WHERE m.cd_modalidade = $1
SQL;

      $arrModalidade = $this->Database->select($sqlModalidade, [$this->arrRequest["cd_modalidade"]]);

      return $arrModalidade[0] ?? [];
    }

    /**
     * Verifica se a modalidade atual está ligada a algum evento
     * e bloqueia a exclusão.
     *
     * @return boolean
     * @throws Exception
     */
    protected function validarExistenciaPendenciasModalidade() : bool
    {
      $sqlPendenciasModal =<<<SQL
        SELECT COUNT(*) AS qt_evento
          FROM modalidade_evento e
         WHERE e.cd_modalidade = $1
SQL;

      $arrPendenciasModal = $this->Database->select($sqlPendenciasModal, [$this->arrRequest["cd_modalidade"]]);

      if ($arrPendenciasModal[0]["qt_evento"] > 0)
        throw new Exception("A modalidade selecionada está ligada a uma ou mais eventos!");

      return false;
    }
  }
