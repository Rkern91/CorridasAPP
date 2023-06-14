<?php
  require_once("ConexaoBanco.php");
  require_once("../helpers.inc.php");
  
  class FormModalidade
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
                           SET ds_descricao    = '{$this->arrRequest["ds_descricao"]}',
                               vl_valor        = '{$this->arrRequest["vl_inscricao"]}',
                               vl_km_distancia = '{$this->arrRequest["vl_km_distancia"]}',
                               dt_largada      = '{$this->arrRequest["dt_largada"]}'
                         WHERE cd_modalidade   = '{$this->arrRequest["cd_modalidade"]}'
                     RETURNING cd_modalidade";
      
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
      $sqlModalidade =<<<SQL
        INSERT INTO modalidade (ds_descricao, dt_largada, vl_km_distancia, vl_valor)
             VALUES ('{$this->arrRequest["ds_descricao"]}', '{$this->arrRequest["dt_largada"]}', '{$this->arrRequest["vl_km_distancia"]}', '{$this->arrRequest["vl_inscricao"]}')
          RETURNING cd_modalidade
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlModalidade, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * Monta a tela de listagem.
     *
     * @return string
     * @throws Exception
     */
    public function montarFormListagemModalidade(): string
    {
      $dsCampoHidden = "";
      $Modalidades   = $this->obtemDadosModalidade();
      $dsTRows       = "";
      
      if (!empty($Modalidades))
      {
        foreach ($Modalidades as $modalidade)
        {
          $dsLinkEditar = "<a href=\"man_modalidade.php?cd_modalidade={$modalidade["cd_modalidade"]}\">Editar</a>";
          $vlInscricao  = padronizaMoeda($modalidade["vl_valor"], 2, "sys", "pt_BR");
          
          $dsTRows .=<<<HTML
            <tr>
              <td style="text-align: center">{$modalidade["cd_modalidade"]}</td>
              <td>{$modalidade["ds_descricao"]}</td>
              <td style="text-align: center">{$modalidade["dt_largada_modalidade"]}</td>
              <td style="text-align: center">{$modalidade["vl_km_distancia"]}</td>
              <td style="text-align: center">R$ {$vlInscricao}</td>
              <td style="text-align: center">{$dsLinkEditar}</td>
            </tr>
HTML;
        }
      }
      else
      {
        return <<<HTML
          <input type=hidden id=ds_operacao value=cadastrar>
          <input type=hidden id=ds_origem   value=modalidade>";
HTML;
      }
      
      //Define a operacao executada ao chamar a tela e cria um alerta
      if (isset($_REQUEST["id_operacao"]))
        $dsCampoHidden = "<input type=\"hidden\" id=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">" .
                         "<input type=\"hidden\" id=\"ds_origem\"   value=\"modalidade\">";
      
      
      
      return <<<HTML
        <div class=container>
          <h3>Listagem de Modalidades</h3>
          {$dsCampoHidden}
          <table>
            <tr>
              <th>Cód.</th>
              <th>Modalidade</th>
              <th>Data/Hora</th>
              <th>Distância (KM)</th>
              <th>Vl. Inscrição</th>
              <th>-</th>
            </tr>
            {$dsTRows}
          </table>
          <p><a href=man_modalidade.php>Adicionar Modalidade</a> | <a href="index.php">Voltar ao Início</a></p>
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
    public function montarFormManutencaoModalidade() : string
    {
      $dsCampoHidden        = "";
      $dsCampoDescricao     = "";
      $dsCampoData          = "";
      $dsCampoHora          = "";
      $dsCampoDistancia     = "";
      $dsCampoInscricao     = "";
      $dsCampoAcao          = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"inserir\" checked>Inserir</label>";
      
      if (isset($this->arrRequest["cd_modalidade"]))
      {
        $arrDadosModalidade = $this->obtemDadosModalidade();
        $dsCampoData        = $arrDadosModalidade["dt_largada_modalidade"];
        $dsCampoHora        = $arrDadosModalidade["hr_largada_modalidade"];
        $dsCampoDescricao   = $arrDadosModalidade["ds_descricao"];
        $dsCampoDistancia   = $arrDadosModalidade["vl_km_distancia"];
        $dsCampoInscricao   = padronizaMoeda($arrDadosModalidade["vl_valor"], 2, "sys", "pt_BR");
        $dsCampoHidden      = "<input type=hidden name=cd_modalidade value={$arrDadosModalidade["cd_modalidade"]}>";
        $dsCampoAcao        = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"atualizar\" checked>Alterar</label>
                               <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"deletar\">Excluir</label>";
      }
      
      return<<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          {$dsCampoHidden}
          <input type="hidden" name="tabela" id="id_tabela" value="modalidade">
          <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
          <table>
            <tr>
              <th>Modalidade</th>
              <td colspan="3" style="text-align: left"><input type="text" name="ds_descricao" id="ds_descricao" size="40" minlength="2" value="{$dsCampoDescricao}" oninput="validateInput(this)"></td></tr>
            </tr>
            <tr>
              <th>Valor (R$)</th>
              <td style="text-align: left"><input type="text" name="vl_valor" id="vl_valor" value="{$dsCampoInscricao}" onchange="ajustarFormatoValores(this)"></td>
              <th>Distância (KM)</th>
              <td style="text-align: left"><input type="text" name="vl_km_distancia" minlength="1" maxlength="3" size="3" id="vl_km_distancia" value="{$dsCampoDistancia}"></td>
            </tr>
            <tr>
              <th>Data</th>
              <td style="text-align: left"><input type="date" name="dt_largada_modalidade" id="dt_largada_modalidade" value="{$dsCampoData}"></td>
              <th>Hora</th>
              <td><input type="time" name="hr_largada_modalidade" id="hr_largada_modalidade" value="{$dsCampoHora}"></td>
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
     * Retorna os dados de modalidades para a listagem ou manutenção..
     *
     * @return array|false
     * @throws Exception
     */
    protected function obtemDadosModalidade()
    {
      $sqlWhere  = "";
      
      if (isset($this->arrRequest["cd_modalidade"]))
      {
        $sqlFields = " m.vl_km_distancia                   AS vl_km_distancia,
                     TO_CHAR(m.dt_largada, 'YYYY-MM-DD') AS dt_largada_modalidade,
                     TO_CHAR(m.dt_largada, 'HH:MI')      AS hr_largada_modalidade ";
        $sqlWhere  = " AND m.cd_modalidade = {$this->arrRequest["cd_modalidade"]}";
      }
      else
      {
        $sqlFields = " m.vl_km_distancia || 'KM'                 AS vl_km_distancia,
                     TO_CHAR(m.dt_largada, 'DD/MM/YYYY HH:MI') AS dt_largada_modalidade ";
      }
      
      $sqlModalidades =<<<SQL
        SELECT m.cd_modalidade,
               m.ds_descricao,
               m.vl_valor,
               {$sqlFields}
          FROM modalidade m
         WHERE TRUE
           {$sqlWhere}
         ORDER BY m.ds_descricao;
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlModalidades))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrDadosModalidades = $this->ConexaoBanco->getLastQueryResults();
      
      if (isset($this->arrRequest["cd_modalidade"]) && $arrDadosModalidades > 0)
        $arrDadosModalidades = $arrDadosModalidades[0];
      
      return $arrDadosModalidades;
    }
    
    /**
     * Verifica se a modalidade atual está ligada a algum evento
     * e bloqueia a exclusão.
     *
     * @return boolean
     * @throws Exception
     */
    function validarExistenciaPendenciasModalidade() : bool
    {
      $sqlPendenciasModal =<<<SQL
        SELECT COUNT(*) AS qt_evento
          FROM modalidade_evento e
         WHERE e.cd_modalidade = '{$this->arrRequest["cd_modalidade"]}'
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlPendenciasModal))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      if ($this->ConexaoBanco->getLastQueryResults()[0]["qt_evento"] > 0)
        throw new Exception("A modalidade selecionada está ligada a uma ou mais eventos!");
      
      return false;
    }
  }