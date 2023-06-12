<?php
  require_once("ConexaoBanco.php");
  require_once("../helpers.inc.php");
  
  class FormCidade
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
                         VALUES ('{$this->arrRequest["nm_cidade"]}', '{$this->arrRequest["cd_uf"]}')
                      RETURNING cd_cidade";
      
      if (!$this->ConexaoBanco->runQueryes($sqlCidade, "insert"))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
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
                       SET nm_cidade = '{$this->arrRequest["nm_cidade"]}',
                           cd_uf     = '{$this->arrRequest["cd_uf"]}'
                     WHERE cd_cidade = '{$this->arrRequest["cd_cidade"]}'
                 RETURNING cd_cidade";
      
      if (!$this->ConexaoBanco->runQueryes($sqlCidade, "update"))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
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
        if (!$this->ConexaoBanco->runQueryes("DELETE FROM cidade WHERE cd_cidade = '{$this->arrRequest["cd_cidade"]}'", "delete"))
          throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * Monta a tela de listagem.
     *
     * @return string
     * @throws Exception
     */
    public function montarFormListagemCidade(): string
    {
      $dsTableCidades = "";
      $dsTRows        = "";
      $Cidade         = $this->obtemDadosCidade();
      
      if (!empty($Cidade))
      {
        foreach ($Cidade as $cidade)
        {
          $dsLinkEditar = "<a href=\"man_cidade.php?cd_cidade={$cidade["cd_cidade"]}\">Editar</a>";
          
          $dsTRows .=<<<HTML
        <tr>
          <td style="text-align: center">{$cidade["cd_cidade"]}</td>
          <td>{$cidade["nm_cidade"]}</td>
          <td style="text-align: center">{$dsLinkEditar}</td>
        </tr>
HTML;
          
          $dsTableCidades =
            "<div class=\"container\">
              <h3>Listagem de Cidades</h3>
              <table>
                <tr>
                  <th>Cód.</th>
                  <th>Cidade</th>
                  <th>-</th>
                </tr>
                {$dsTRows}
              </table>
              <p><a href=\"man_cidade.php\">Adicionar Cidade</a> | <a href=\"../index.php\">Voltar ao Início</a></p>
            </div>";
        }
      }
      else
      {
        //Define a operacao executada ao chamar a tela e cria um alerta
        $dsTableCidades = "<input type=\"hidden\" id=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">" .
          "<input type=\"hidden\" id=\"ds_origem\"   value=\"cidade\">";
      }
      
      return $dsTableCidades;
    }
    
    /**
     * Monta o formulário da tela para edição
     * ou novo registro do processo.
     *
     * @return string
     * @throws Exception
     */
    public function montarFormManutencaoCidade() : string
    {
      $dsCampoHidden     = "";
      $dsCampoDescricao  = "";
      $dsCampoAcao       = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"inserir\" checked>Inserir</label>";
      
      if (isset($this->arrRequest["cd_cidade"]))
      {
        $arrDadosCidade            = $this->obtemDadosCidade();
        $this->arrRequest["cd_uf"] = $arrDadosCidade["cd_uf"];
        $dsCampoDescricao          = $arrDadosCidade["nm_cidade"];
        $dsCampoHidden             = "<input type=hidden name=cd_cidade value={$arrDadosCidade["cd_cidade"]}>";
        $dsCampoAcao               = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"atualizar\" checked>Alterar</label>
                                      <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"deletar\">Excluir</label>";
      }
      
      return <<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          {$dsCampoHidden}
          <input type="hidden" name="tabela" id="id_tabela" value="cidade">
          <table>
            <tr>
              <th>Cidade</th>
              <td colspan="3" style="text-align: left"><input type="text" name="nm_cidade" id="nm_cidade" size="40" minlength="2" value="{$dsCampoDescricao}" oninput="validateInput(this)"></td></tr>
            </tr>
            <tr>
              <th>Estado</th>
              <td colspan="3" style="text-align: left"><select name="cd_uf" id="cd_uf">{$this->obtemOpEstados()}</select></td>
            </tr>
            <tr>
              <th>Ação:</th>
              <td colspan="3">
                {$dsCampoAcao}
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center">
                <input type="submit" name=btn_submit id="btn_submit" value="Confirmar">
              </td>
            </tr>
          </table>
        </form>
HTML;
    }
    
    /**
     * Obtem e retorna os dados de Cidades.
     *
     * @param int|null $cdCidade
     * @return array|false|mixed
     * @throws Exception
     */
    protected function obtemDadosCidade()
    {
      $sqlWhere = "";
      $sqlField = "c.nm_cidade || ' / ' || u.ds_sigla AS nm_cidade,";
      
      if (isset($this->arrRequest["cd_cidade"]))
      {
        $sqlWhere = "AND c.cd_cidade = '{$this->arrRequest["cd_cidade"]}'";
        $sqlField = "c.nm_cidade,";
      }
      
      $sqlCidades =<<<SQL
        SELECT c.cd_cidade,
               {$sqlField}
               u.cd_uf
          FROM cidade c
          JOIN uf     u ON u.cd_uf = c.cd_uf
         WHERE TRUE
          {$sqlWhere}
         ORDER BY c.nm_cidade
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlCidades))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrDadosCidades = $this->ConexaoBanco->getLastQueryResults();
      
      if (isset($this->arrRequest["cd_cidade"]) && $arrDadosCidades > 0)
        $arrDadosCidades = $arrDadosCidades[0];
      
      return $arrDadosCidades;
    }
    
    /**
     * Obtem e retorna dados de Estados.
     *
     * @return string
     * @throws Exception
     */
    protected function obtemOpEstados() : string
    {
      $sqlUf = <<<SQL
        SELECT u.cd_uf                        AS value,
               u.ds_uf || ' / ' || u.ds_sigla AS description
          FROM uf u
         ORDER BY u.ds_uf
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlUf))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrOptionsEstados = [];
      
      // Loop para concatenar as opções de estados em uma variável e
      // setar o estado selecionado no array caso esteja ocorrendo uma edição
      foreach ($this->ConexaoBanco->getLastQueryResults() as $uf)
      {
        $idSelected = "";
        
        if (isset($this->arrRequest["cd_uf"]) && ($this->arrRequest["cd_uf"] == $uf["value"]))
          $idSelected = "selected";
        
        $arrOptionsEstados[] = "<option value=\"{$uf["value"]}\" {$idSelected}>{$uf["description"]}</option>";
      }
      
      setFirstEmpty($arrOptionsEstados);
      return implode($arrOptionsEstados);
    }
    
    /**
     * Verifica se a cidade atual está ligada a algum evento
     * e bloqueia a exclusão.
     *
     * @return boolean
     * @throws Exception
     */
    protected function validarExistenciaPendenciasCidade() : bool
    {
      $sqlPendenciasCidade =<<<SQL
        SELECT COUNT(*) AS qt_eventos
          FROM evento e
         WHERE e.cd_cidade = '{$this->arrRequest["cd_cidade"]}'
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlPendenciasCidade))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
        
      if ($this->ConexaoBanco->getLastQueryResults()[0]["qt_eventos"])
        throw new Exception("A cidade selecionada está ligada a um ou mais eventos!");
      
      return false;
    }
  }