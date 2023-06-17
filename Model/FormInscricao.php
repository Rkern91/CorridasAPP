<?php
  require_once("ConexaoBanco.php");
  require_once("../helpers.inc.php");
  
  class FormInscricao
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
             SET ds_contato    = '{$this->arrRequest["ds_contato"]}',
                 ds_equipe     = '{$this->arrRequest["ds_equipe"]}',
                 cd_modalidade = '{$this->arrRequest["cd_modalidade"]}',
                 cd_pessoa     = '{$this->arrRequest["cd_pessoa"]}',
                 cd_evento     = '{$this->arrRequest["cd_evento"]}'
           WHERE cd_inscricao = '{$this->arrRequest["cd_inscricao"]}'
       RETURNING cd_inscricao";

      if (!$this->ConexaoBanco->runQueryes($sqlModalidade, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
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
      {
        $sql = "DELETE FROM modalidade WHERE cd_modalidade = '{$this->arrRequest["cd_modalidade"]}'";
        
        if (!$this->ConexaoBanco->runQueryes($sql, $this->arrRequest["f_action"]))
          throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      }
    }
    
    /**
     * Insere um novo registro de cidade.
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
                  VALUES ('{$dtAtualInscricao}',
                          0,
                          '{$this->arrRequest["ds_contato"]}',
                          '{$this->arrRequest["ds_equipe"]}',
                          '{$this->arrRequest["cd_modalidade"]}',
                          '{$this->arrRequest["cd_pessoa"]}',
                          '{$this->arrRequest["cd_evento"]}')
               RETURNING cd_inscricao;
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlModalidade, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * @throws Exception
     */
    public function montarFormCadastroEvento() : string
    {
      $arrDadosEvento    = $this->obterDadosEventoInscricao();
      $dsCampoData       = $arrDadosEvento["dt_evento"];
      $dsCampoNome       = $arrDadosEvento["nm_evento"];
      $dsCampoCidade     = $arrDadosEvento["nm_cidade"];
      $dsCampoNomeEquipe = $arrDadosEvento["ds_equipe"];
      $dsCampoContato    = $arrDadosEvento["ds_contato"];
      $dsCampoHidden     = "<input type=hidden name=cd_evento value={$arrDadosEvento["cd_evento"]}>
                            <input type=hidden name=cd_pessoa value={$_SESSION["cd_pessoa"]}>";
      $dsCampoAcao       = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"inserir\" checked>Inserir</label>";
      
      if (hasValue($arrDadosEvento["cd_inscricao"]))
      {
        $dsCampoHidden .= "<input type=hidden name=cd_inscricao value={$arrDadosEvento["cd_inscricao"]}>";
        $dsCampoAcao    = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"atualizar\" checked>Alterar</label>
                           <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"deletar\">Excluir</label>";
      }
      
      return <<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          {$dsCampoHidden}
          <input type="hidden" name="tabela" id="id_tabela" value="inscricao">
          <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
          <table>
            <tr>
              <th>Evento</th>
              <td style="text-align: left">{$dsCampoNome}</td>
              <th>Cidade</th>
              <td style="text-align: left">{$dsCampoCidade}</td>
            </tr>
            <tr>
              <th>Data</th>
              <td style="text-align: left">{$dsCampoData}</td>
              <th>Modalidades</th>
              <td style="text-align: left">
                <select name="cd_modalidade" id="cd_modalidade">{$this->obterOpModalidadesEvento($arrDadosEvento["cd_modalidade"])}</select>
              </td>
            </tr>
            <tr>
              <th>Contato</th>
              <td style="text-align: left"><input type="text" name="ds_contato" id="ds_contato" size="25" minlength="2" value="{$dsCampoContato}"></td>
              <th>Equipe</th>
              <td style="text-align: left"><input type="text" name="ds_equipe" id="ds_equipe" size="25" minlength="2" value="{$dsCampoNomeEquipe}" oninput="validateInput(this)"></td>
            </tr>
            <tr>
              <th>Ação:</th>
              <td colspan="4">
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
     * @throws Exception
     */
    public function montarFormListagemCadastro(): string
    {
      $dsCampoHidden  = "";
      $dsLinksRetorno = "";
      $Inscricao      = $this->obterDadosEventoInscricao();
      $dsTRows        = "";
      
      if (!empty($Inscricao))
      {
        foreach ($Inscricao as $evento)
        {
          $dsModalidade   = $this->obterDescricaoModalidade($evento["cd_modalidade"]);
          $dsLinkEditar   = "<a href=\"man_inscricao.php?cd_evento={$evento["cd_evento"]}&cd_pessoa={$_SESSION["cd_pessoa"]}\">Alterar Inscrição</a>";
          
          $dsTRows .=<<<HTML
            <tr>
              <td>{$evento["nm_evento"]}</td>
              <td>{$evento["dt_evento"]}</td>
              <td>{$evento["nm_cidade"]}</td>
              <td>{$evento["ds_equipe"]}</td>
              <td>{$evento["ds_contato"]}</td>
              <td>{$dsModalidade["ds_descricao"]}</td>
              <td style="text-align: center">{$dsLinkEditar}</td>
            </tr>
HTML;
        }
      }
      else
      {
        return <<<HTML
          <input type=hidden id=ds_operacao value=cadastrar>
          <input type=hidden id=ds_origem   value=inscricao>";
HTML;
      }
      
      //Define a operacao executada ao chamar a tela e cria um alerta
      if (isset($_REQUEST["id_operacao"]))
        $dsCampoHidden = "<input type=\"hidden\" id=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">" .
                         "<input type=\"hidden\" id=\"ds_origem\"   value=\"inscricao\">";
      
      return <<<HTML
        <div class=container>
          <h3>Listagem de Inscrições</h3>
          {$dsCampoHidden}
          <table>
            <tr>
              <th>Evento</th>
              <th>Data</th>
              <th>Cidade</th>
              <th>Equipe</th>
              <th>Contato</th>
              <th>Distância (KM)</th>
              <th>-</th>
            </tr>
            {$dsTRows}
          </table>
          <p><a href="sel_evento.php">Listagem de Eventos</a> | <a href="index.php">Voltar ao Início</a></p>
        </div>
HTML;
    }
    
    /**
     * Retorna a descrição de uma modalidade
     * @param int $cdModalidade
     * @return mixed
     * @throws Exception
     */
    protected function obterDescricaoModalidade(int $cdModalidade)
    {
      $sqlInscricao =<<<SQL
        SELECT m.ds_descricao
          FROM modalidade m
         WHERE m.cd_modalidade = '{$cdModalidade}';
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlInscricao))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      return $this->ConexaoBanco->getLastQueryResults()[0];
    }
    
    /**
     * Obtem dados do evento para realização
     * da inscrição do usuario no evento e para a tela de listagem.
     * @throws Exception
     */
    protected function obterDadosEventoInscricao()
    {
      $sqlWhere = " AND i.cd_pessoa = '{$this->arrRequest["cd_pessoa"]}' ";
      
      if (isset($this->arrRequest["cd_evento"]) && isset($this->arrRequest["cd_pessoa"]))
      {
        $sqlWhere = " AND e.cd_evento = '{$this->arrRequest["cd_evento"]}'
                      AND i.cd_pessoa = '{$this->arrRequest["cd_pessoa"]}'";
      }
      
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
          JOIN cidade         c ON c.cd_cidade = e.cd_cidade
          JOIN uf             u ON     u.cd_uf = c.cd_uf
         WHERE TRUE
          {$sqlWhere}
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlInscricao))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrDadosInscricoesEventos = $this->ConexaoBanco->getLastQueryResults();
      
      if (isset($this->arrRequest["cd_evento"]))
        $arrDadosInscricoesEventos = $arrDadosInscricoesEventos[0];
      
      return $arrDadosInscricoesEventos;
    }
    
    /**
     * Obtem e retorna uma lista de modalidades em forma de array
     * para popular o campo SELECT da tela.
     *
     * @param null $cdModalidade
     * @return string
     * @throws Exception
     */
    protected function obterOpModalidadesEvento($cdModalidade = null): string
    {
      $sqlModalidades = <<<SQL
        SELECT m.cd_modalidade                              AS value,
               m.vl_km_distancia || ' / ' || m.ds_descricao AS description
          FROM modalidade m
          JOIN modalidade_evento me ON me.cd_modalidade = m.cd_modalidade
         WHERE me.cd_evento = '{$this->arrRequest["cd_evento"]}'
         ORDER BY m.vl_km_distancia
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlModalidades))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrRetorno      = $this->ConexaoBanco->getLastQueryResults();
      $arrOptionsModal = [];
      
      // Loop para concatenar as opções em uma variável
      foreach ($arrRetorno as $modal)
      {
        $idSelected = "";
        
        //Se já existe uma inscrição, o cd_modalidade vai estar setado e será definido como selecionado ao carregar a tela
        if (hasValue($cdModalidade) && ($cdModalidade == $modal["value"]))
          $idSelected = "selected";
        
        $arrOptionsModal[] = "<option value=\"{$modal["value"]}\" $idSelected>{$modal["description"]}</option>";
      }
      
      setFirstEmpty($arrOptionsModal);
      return implode($arrOptionsModal);
    }
  }