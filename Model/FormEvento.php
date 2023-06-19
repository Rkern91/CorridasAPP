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
      $this->ConexaoBanco = new ConexaoBanco();
      $this->arrRequest   = $arrRequest;
      
      if (isset($arrRequest["dt_evento"]))
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
    
    /**
     * Adiciona a ligação das modalidades com o evento criado.
     * @throws Exception
     */
    protected function adicionarModalidadesEvento()
    {
      $cdEventoNovo = $this->arrRequest["cd_evento"] ?? $this->ConexaoBanco->getLastQueryId();
      
      foreach (explode(",", $this->arrRequest["arr_cd_modalidades"]) as $cdModalidade)
      {
        $sqlInsertModalidade = "INSERT INTO modalidade_evento (cd_modalidade, cd_evento)
                                     VALUES ('{$cdModalidade}', '{$cdEventoNovo}')
                                  RETURNING cd_evento";
        
        if (!$this->ConexaoBanco->runQueryes($sqlInsertModalidade, $this->arrRequest["f_action"]))
          throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
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
                           SET nm_evento = '{$this->arrRequest["nm_evento"]}',
                               dt_evento = '{$this->arrRequest["dt_completa"]}',
                               cd_cidade = '{$this->arrRequest["cd_cidade"]}'
                         WHERE cd_evento = '{$this->arrRequest["cd_evento"]}'
                     RETURNING cd_evento";
      
      if (!$this->ConexaoBanco->runQueryes($sqlEvento, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $this->removerDependenciasEvento();
      $this->adicionarModalidadesEvento();
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
      
      if (!$this->ConexaoBanco->runQueryes($sql, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * Insere um novo registro de cidade.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $sqlEvento =<<<SQL
        INSERT INTO evento (nm_evento, dt_evento, cd_cidade)
             VALUES ('{$this->arrRequest["nm_evento"]}', '{$this->arrRequest["dt_completa"]}', '{$this->arrRequest["cd_cidade"]}')
          RETURNING cd_evento
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlEvento, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $this->adicionarModalidadesEvento();
    }
    
    /**
     * Monta a tela de listagem.
     *
     * @return string
     * @throws Exception
     */
    public function montarFormListagemEvento(): string
    {
      $dsCampoHidden  = "";
      $dsLinksRetorno = "";
      $Eventos        = $this->obtemDadosEventoListagem();
      $dsTRows        = "";
      
      if (!empty($Eventos))
      {
        foreach ($Eventos as $evento)
        {
          $dsLinkEditar   = "<a href=\"man_evento.php?cd_evento={$evento["cd_evento"]}\">Editar</a>";
          $dsLinksRetorno = "<p><a href=\"man_evento.php\">Adicionar Evento</a> | <a href=\"index.php\">Voltar ao Início</a></p>";
          
          if (isset($_SESSION["id_tipo_usuario"]) && $_SESSION["id_tipo_usuario"] == 2)
          {
            $dsLinkEditar   = "<a href=\"man_inscricao.php?cd_evento={$evento["cd_evento"]}&cd_pessoa={$_SESSION["cd_pessoa"]}\">Inscrever-se!</a>";
            $dsLinksRetorno =  "<p><a href=\"index.php\">Voltar ao Início</a></p>";
          }
          
          $dsTipModalidade = "";
          
          //Se existir modalidade atrelada ao evento, cria uma especie de tip ao sobrepor o mouse na coluna
          if (isset($evento["ds_descricacao"]))
          {
            $itensModalidade = "";
            $dsModalidades   = explode(",", str_replace("\"", "", trim($evento["ds_descricacao"], "{}")));
            
            foreach ($dsModalidades as $modal)
              $itensModalidade .= "$modal\n";
            
            $dsTipModalidade = "$itensModalidade";
          }
          
          $dsTRows .=<<<HTML
            <tr>
              <td style="text-align: center">{$evento["cd_evento"]}</td>
              <td>{$evento["nm_evento"]}</td>
              <td style="text-align: center">{$evento["dt_evento"]}</td>
              <td>{$evento["nm_cidade"]}</td>
              <td style="text-align: center" title="{$dsTipModalidade}">{$evento["ds_modalidades"]}</td>
              <td style="text-align: center">{$dsLinkEditar}</td>
            </tr>
HTML;
        }
      }
      else
      {
        return <<<HTML
          <input type=hidden id=ds_operacao value=cadastrar>
          <input type=hidden id=ds_origem   value=evento>";
HTML;
      }
      
      //Define a operacao executada ao chamar a tela e cria um alerta
      if (isset($_REQUEST["id_operacao"]))
        $dsCampoHidden = "<input type=\"hidden\" id=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">" .
                         "<input type=\"hidden\" id=\"ds_origem\"   value=\"evento\">";
      
      return <<<HTML
        <div class=container>
          <h3>Listagem de Eventos</h3>
          {$dsCampoHidden}
          <table>
            <tr>
              <th>Cód.</th>
              <th>Evento</th>
              <th>Data</th>
              <th>Cidade</th>
              <th>Modalidades (KMs)</th>
              <th>-</th>
            </tr>
            {$dsTRows}
          </table>
          {$dsLinksRetorno}
        </div>
HTML;
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
      $dsCampoAcao     = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"inserir\" checked>Inserir</label>";
      
      if (isset($this->arrRequest["cd_evento"]))
      {
        $arrDadosEvento                = $this->obterDadosEvento();
        $this->arrRequest["cd_cidade"] = $arrDadosEvento["cd_cidade"];
        $dsCampoData                   = $arrDadosEvento["dt_evento"];
        $dsCampoHora                   = $arrDadosEvento["hr_evento"];
        $dsCampoNome                   = $arrDadosEvento["nm_evento"];
        $dsCampoQtModal                = trim($arrDadosEvento["qt_modalidade"],      "{}");
        $dsCampoCdsModal               = trim($arrDadosEvento["arr_cd_modalidades"], "{}");
        $dsCampoKmModal                = trim($arrDadosEvento["arr_km_distancia"],   "{}");
        $dsCampoHidden                 = "<input type=hidden name=cd_evento value={$arrDadosEvento["cd_evento"]}>";
        $dsCampoAcao                   = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"atualizar\" checked>Alterar</label>
                                          <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"deletar\">Excluir</label>";
      }
      
      return <<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          {$dsCampoHidden}
          <input type="hidden" name="tabela"             id="id_tabela"          value="evento">
          <input type="hidden" name="tela"               id="id_tela"            value="manutencao">
          <input type="hidden" name="arr_cd_modalidades" id="arr_cd_modalidades" value="{$dsCampoCdsModal}">
          <input type="hidden" name="qt_modalidades"     id="qt_modalidades"     value="{$dsCampoQtModal}">
          <table>
            <tr>
              <th>Nome</th>
              <td colspan="3" style="text-align: left"><input type="text" name="nm_evento" id="nm_evento" size="40" minlength="2" value="{$dsCampoNome}" oninput="validateInput(this)"></td>
            </tr>
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
     * Retorna os dados de eventos para a listagem de eventos.
     *
     * @return array
     * @throws Exception
     */
    protected function obtemDadosEventoListagem(): array
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
      
      if (!$this->ConexaoBanco->runQueryes($sqlEvento))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      return $this->ConexaoBanco->getLastQueryResults();
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
        
        if (isset($this->arrRequest["cd_cidade"]) && ($this->arrRequest["cd_cidade"] == $cidade["value"]))
          $idSelected = "selected";
        
        $arrOptionsCidades[] = "<option value=\"{$cidade["value"]}\" {$idSelected}>{$cidade["description"]}</option>";
      }
      
      setFirstEmpty($arrOptionsCidades);
      return implode($arrOptionsCidades);
    }
    
    /**
     * Obtem e retorna uma lista de modalidades em forma de array
     * para popular o campo SELECT da tela.
     *
     * @param string $idTela
     * @return string
     * @throws Exception
     */
    protected function obterOpModalidadesEvento(string $idTela = "manutencao"): string
    {
      $sqlJoinWhere = "";
      
      if ($idTela == "extrato")
      {
        $sqlJoinWhere = "JOIN modalidade_evento me ON me.cd_modalidade = m.cd_modalidade
                         WHERE me.cd_evento = '{$this->arrRequest["cd_evento"]}'";
      }
      
      $sqlModalidades = <<<SQL
        SELECT m.cd_modalidade                              AS value,
               m.vl_km_distancia || ' / ' || m.ds_descricao AS description
          FROM modalidade m
          {$sqlJoinWhere}
         ORDER BY m.vl_km_distancia
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlModalidades))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrRetorno      = $this->ConexaoBanco->getLastQueryResults();
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
      //TODO: Ao remover evento deve considerar inscrições já realizadas nas modalidades do evento
      $arrPendencias = [
        "inscricao",
        "modalidade_evento"
      ];
      
      foreach ($arrPendencias as $dsTablePendencia)
      {
        $sqlPendenciasCidade =<<<SQL
        DELETE FROM {$dsTablePendencia}
         WHERE cd_evento = '{$this->arrRequest["cd_evento"]}'
SQL;
        
        if (!$this->ConexaoBanco->runQueryes($sqlPendenciasCidade, $this->arrRequest["f_action"]))
          throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      }
    }
  }