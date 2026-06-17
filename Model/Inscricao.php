<?php
  require_once("Database.php");
  require_once("../helpers.inc.php");

  class Inscricao
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

      if (!isset($this->arrRequest["cd_pessoa"]) && isset($_SESSION["cd_pessoa"]))
        $this->arrRequest["cd_pessoa"] = $_SESSION["cd_pessoa"];
    }

    /**
     * Atualiza o registro selecionado.
     *
     * @return void
     * @throws Exception
     */
    public function atualizarAcao()
    {
      $sqlModalidade ="
          UPDATE inscricao
             SET ds_contato    = $1,
                 ds_equipe     = $2,
                 cd_modalidade = $3,
                 cd_pessoa     = $4,
                 cd_evento     = $5
           WHERE cd_inscricao = $6
       RETURNING cd_inscricao";

      $this->Database->execute($sqlModalidade, [
        $this->arrRequest["ds_contato"],
        $this->arrRequest["ds_equipe"],
        $this->arrRequest["cd_modalidade"],
        $this->arrRequest["cd_pessoa"],
        $this->arrRequest["cd_evento"],
        $this->arrRequest["cd_inscricao"]
      ]);
    }

    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      $sql = "DELETE FROM inscricao WHERE cd_inscricao = $1";

      $this->Database->execute($sql, [$this->arrRequest["cd_inscricao"]]);
    }

    /**
     * Insere um novo registro de inscrição.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $dtAtualInscricao = date("Y-m-d H:i:s");

      $sqlModalidade =<<<SQL
             INSERT INTO inscricao (dt_inscricao,
                                    id_status,
                                    ds_contato,
                                    ds_equipe,
                                    cd_modalidade,
                                    cd_pessoa,
                                    cd_evento)
                  VALUES ($1, 0, $2, $3, $4, $5, $6)
               RETURNING cd_inscricao;
SQL;

      $this->Database->execute($sqlModalidade, [
        $dtAtualInscricao,
        $this->arrRequest["ds_contato"],
        $this->arrRequest["ds_equipe"],
        $this->arrRequest["cd_modalidade"],
        $this->arrRequest["cd_pessoa"],
        $this->arrRequest["cd_evento"]
      ]);
    }

    /**
     * Retorna as inscrições da pessoa (com dados do evento e da modalidade)
     * para a tela de listagem.
     *
     * @return array
     * @throws Exception
     */
    public function obterListagemInscricoes(): array
    {
      $sqlInscricoes =<<<SQL
        SELECT e.cd_evento,
               e.nm_evento,
               i.ds_equipe,
               i.ds_contato,
               i.cd_modalidade,
               md.ds_descricao,
               c.nm_cidade || ' / ' || u.ds_sigla       AS nm_cidade,
               TO_CHAR(e.dt_evento, 'HH:MI DD/MM/YYYY') AS dt_evento
          FROM evento          e
          JOIN inscricao  i  ON     i.cd_evento = e.cd_evento
                            AND     i.cd_pessoa = $1
          JOIN modalidade md ON md.cd_modalidade = i.cd_modalidade
          JOIN cidade     c  ON     c.cd_cidade = e.cd_cidade
          JOIN uf         u  ON         u.cd_uf = c.cd_uf
         ORDER BY e.dt_evento
SQL;

      return $this->Database->select($sqlInscricoes, [$this->arrRequest["cd_pessoa"]]);
    }

    /**
     * Obtem os dados de um evento para realização da inscrição,
     * trazendo a inscrição existente da pessoa (se houver).
     *
     * @return array
     * @throws Exception
     */
    public function obterDadosEventoInscricao(): array
    {
      $sqlInscricao =<<<SQL
        SELECT e.cd_evento,
               e.nm_evento,
               i.cd_inscricao,
               i.ds_equipe,
               i.ds_contato,
               i.cd_modalidade,
               c.nm_cidade || ' / ' || u.ds_sigla       AS nm_cidade,
               TO_CHAR(e.dt_evento, 'HH:MI DD/MM/YYYY') AS dt_evento
          FROM evento         e
          LEFT JOIN inscricao i ON i.cd_evento = e.cd_evento
                               AND i.cd_pessoa = $1
          JOIN cidade         c ON c.cd_cidade = e.cd_cidade
          JOIN uf             u ON     u.cd_uf = c.cd_uf
         WHERE e.cd_evento = $2
SQL;

      $arrEvento = $this->Database->select($sqlInscricao, [
        $this->arrRequest["cd_pessoa"],
        $this->arrRequest["cd_evento"]
      ]);

      return $arrEvento[0] ?? [];
    }

    /**
     * Retorna as modalidades de um evento para popular o campo de seleção.
     *
     * @return array Lista de [value, description].
     * @throws Exception
     */
    public function obterModalidadesEvento(): array
    {
      $sqlModalidades = <<<SQL
        SELECT m.cd_modalidade                              AS value,
               m.vl_km_distancia || ' / ' || m.ds_descricao AS description
          FROM modalidade m
          JOIN modalidade_evento me ON me.cd_modalidade = m.cd_modalidade
         WHERE me.cd_evento = $1
         ORDER BY m.vl_km_distancia
SQL;

      return $this->Database->select($sqlModalidades, [$this->arrRequest["cd_evento"]]);
    }
  }
