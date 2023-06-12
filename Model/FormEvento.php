<?php
  require_once("ConexaoBanco.php");
  require_once("../helpers.inc.php");

  class FormEvento
  {
    /**
     * Classe de Conexao ao Banco de Dados
     * @var ConexaoBanco
     */
    private ConexaoBanco $ConexaoBanco;

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
      $this->ConexaoBanco              = new ConexaoBanco();
      $this->arrRequest                = $arrRequest;
      $this->arrRequest["dt_completa"] = "{$arrRequest["dt_evento"]} {$arrRequest["hr_evento"]}:00";
    }

    /**
     * Retorna o link com botoes para incluir ou remover modalidades que serao
     * adicionadas ao evento quando o usuario submeter o form.
     * @return string
     */
    protected function addBtnAlterarModalidades(): string
    {
      return "<a title=\"Adicionar Modalidade\" id=\"add\" onclick=\"alterarModalidadesSelecionadas('add')\">(+)</a> | " .
             "<a title=\"Remover Modalidade\"   id=\"rem\" onclick=\"alterarModalidadesSelecionadas('rem')\">(-)</a>";
    }
    
    protected function adicionarModalidadesEvento()
    {
      foreach (explode(",", $this->arrRequest["arr_cd_modalidades"]) as $cdModalidade)
      {
        $sqlInsertModalidade = "INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES ('{$cdModalidade}', '{$this->arrRequest["cd_evento"]}')";
        $this->ConexaoBanco->runQueryes($sqlInsertModalidade, "insert");
        
        if (!$retornoModalidade)
        {
          $error_message = pg_last_error($retornoModalidade);
          header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_INSERT . "&dsOrigem=evento&dsMensagem=" . urlencode($error_message));
          exit;
        }
      }
    }

    /**
     * Atualiza o registro selecionado.
     * @return void
     */
    public function atualizarAcao()
    {
      $sqlCidade = "UPDATE cidade
                       SET nm_cidade = '{$this->arrRequest["nm_cidade"]}',
                           cd_uf     = '{$this->arrRequest["cd_uf"]}'
                     WHERE cd_cidade = '{$this->arrRequest["cd_cidade"]}'";
      
      $this->ConexaoBanco->runQueryes($sqlCidade, "update");
    }
    
    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      //Se não existem dependencias, remove o registro
      $this->removerDependenciasEvento();
      
      $sql = "DELETE FROM evento WHERE cd_evento = '{$this->arrRequest["cd_evento"]}'";
      
      if (!$this->ConexaoBanco->runQueryes($sql, "delete"))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * Insere um novo registro de cidade.
     * @return void
     */
    public function inserirAcao()
    {
      $sqlEvento =<<<SQL
        INSERT INTO evento (nm_evento, dt_evento, cd_cidade)
             VALUES ('{$this->arrRequest["nm_evento"]}', '{$this->arrRequest["dt_completa"]}', '{$this->arrRequest["cd_cidade"]}')
          RETURNING cd_evento
SQL;

      $this->ConexaoBanco->runQueryes($sqlEvento);
      $this->removerDependenciasEvento();
      $this->adicionarModalidadesEvento();
      
    }
    
    /**
     * Monta a tela de listagem.
     * @return string
     */
    public function montarFormListagemEvento(): string
    {
    
    }
    
    /**
     * Monta o formulário da tela para edição
     * ou novo registro do processo.
     *
     * @return string
     * @throws Exception
     */
    public function montarFormManutencaoEvento() : string
    {
      $dsCampoHidden   = "";
      $dsCampoNome     = "";
      $dsCampoData     = "";
      $dsCampoHora     = "";
      $dsCampoQtModal  = "";
      $dsCampoCdsModal = "";
      $dsCampoKmModal  = "";
      $dsCampoAcao     = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"insert\" checked>Inserir</label>";
      
      if (isset($this->arrRequest["cd_evento"]))
      {
        $arrDadosEvento  = $this->obterDadosEvento();
        $dsCampoData     = $arrDadosEvento["dt_evento"];
        $dsCampoHora     = $arrDadosEvento["hr_evento"];
        $dsCampoNome     = $arrDadosEvento["nm_evento"];
        $dsCampoQtModal  = trim($arrDadosEvento["qt_modalidade"],      "{}");
        $dsCampoCdsModal = trim($arrDadosEvento["arr_cd_modalidades"], "{}");
        $dsCampoKmModal  = trim($arrDadosEvento["arr_km_distancia"],   "{}");
        $dsCampoHidden   = "<input type=hidden name=cd_evento value={$arrDadosEvento["cd_evento"]}>";
        $dsCampoAcao     = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"update\" checked>Alterar</label>
                            <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"delete\">Excluir</label>";
      }
      
      return <<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          {$dsCampoHidden}
          <input type="hidden" name="tabela"             id="id_tabela"          value="evento">
          <input type="hidden" name="arr_cd_modalidades" id="arr_cd_modalidades" value="{$dsCampoCdsModal}">
          <input type="hidden" name="qt_modalidades"     id="qt_modalidades"     value="{$dsCampoQtModal}">
          <table>
            <tr>
              <th>Nome</th>
              <td colspan="3" style="text-align: left"><input type="text" name="nm_evento" id="nm_evento" size="40" minlength="2" value="{$dsCampoNome}" oninput="validateInput(this)"></td></tr>
            <tr>
              <th>Cidade</th>
              <td colspan="3" style="text-align: left"><select name="cd_cidade" id="cd_cidade">{$this->obterOpCidades()}</select></td>
            </tr>
            <tr>
              <th>Data</th>
              <td style="text-align: left"><input type="date" name="dt_evento" id="dt_evento" value="{$dsCampoData}"></td>
              <th>Hora</th>
              <td><input type="time" name="hr_evento" id="hr_evento" value="{$dsCampoHora}"></td>
            </tr>
            <tr>
              <th>Modalidades</th>
              <td colspan="3" style="text-align: left">
                <select id="op_id_modalidades">{$this->obterOpModalidadesEvento()}</select>{$this->addBtnAlterarModalidades()}<input type="text" id="dsModals" value="$dsCampoKmModal" readonly>
              </td>
            </tr>
            <tr>
              <th>Ação:</th>
              <td colspan="3">
                {$dsCampoAcao}
              </td>
             </tr>
            <tr>
              <td colspan="4" style="text-align: center">
                <input type="submit" name=btn_submit id="btn_submit" value="Confirmar">
              </td>
            </tr>
          </table>
        </form>
HTML;
    }
    
    /**
     * Retorna dados sobre o evento conforme
     * codigo informado por parametro.
     *
     * @return int|mixed
     * @throws Exception
     */
    protected function obterDadosEvento()
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
         WHERE e.cd_evento = '{$this->arrRequest["cd_evento"]}'
         GROUP BY e.cd_evento, e.nm_evento, e.dt_evento, c.cd_cidade
         ORDER BY e.cd_evento, e.nm_evento;
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlEvento))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      return $this->ConexaoBanco->getLastQueryResults()[0];
    }
    
    /**
     * Monta o array de opcoes de cidades
     *
     * @return string
     * @throws Exception
     */
    protected function obterOpCidades() : string
    {
      $sqlCidades =<<<SQL
        SELECT c.cd_cidade                        AS value,
               c.nm_cidade || ' / ' || u.ds_sigla AS description
          FROM cidade c
          JOIN uf     u ON u.cd_uf = c.cd_uf
         ORDER BY c.nm_cidade
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlCidades))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrOptionsCidades = [];
      
      // Loop para concatenar as opções de cidades em uma variável e
      // setar a cidade selecionada no array caso esteja ocorrendo uma edição
      foreach ($this->ConexaoBanco->getLastQueryResults() as $cidade)
      {
        $idSelected = "";
        
        if (hasValue($this->arrRequest["cd_cidade"]) && ($this->arrRequest["cd_cidade"] == $cidade["value"]))
          $idSelected = "selected";
        
        $arrOptionsCidades[] = "<option value=\"{$cidade["value"]}\" {$idSelected}>{$cidade["description"]}</option>";
      }
      
      setFirstEmpty($arrOptionsCidades);
      return implode($arrOptionsCidades);
    }
    
    /**
     * Obtem e retorna uma lista de modalidades em forma de array
     * para popular o campo SELECT da tela.
     * @return string
     */
    protected function obterOpModalidadesEvento(): string
    {
      $sqlModalidades = <<<SQL
        SELECT m.cd_modalidade                              AS value,
               m.vl_km_distancia || ' / ' || m.ds_descricao AS description
          FROM modalidade m
         ORDER BY m.vl_km_distancia
SQL;
      
      $arrRetorno      = $this->ConexaoBanco->runQueryes($sqlModalidades);
      $arrOptionsModal = [];
      
      // Loop para concatenar as opções em uma variável
      foreach ($arrRetorno as $modal)
        $arrOptionsModal[] = "<option value=\"{$modal["value"]}\">{$modal["description"]}</option>";
      
      return implode($arrOptionsModal);
    }
    
    /**
     * Remove qualquer ligação ao evento da tabela modalidade_evento.
     *
     * @return void
     * @throws Exception
     */
    protected function removerDependenciasEvento()
    {
      $sql = "DELETE FROM modalidade_evento WHERE cd_evento = '{$this->arrRequest["cd_evento"]}'";
      
      if (!$this->ConexaoBanco->runQueryes($sql, "delete"))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
  }