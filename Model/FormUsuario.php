<?php
  require_once("ConexaoBanco.php");
  require_once("../helpers.inc.php");
  
  class FormUsuario
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
     * Insere um novo registro.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $sqlPessoa= "INSERT INTO pessoa (nm_pessoa, nr_telefone, dt_nascimento, ds_sexo,
                                       cd_cidade, cd_id_tipo, ds_email, ds_senha)
                         VALUES ('{$this->arrRequest["nm_pessoa"]}', '{$this->arrRequest["nr_telefone"]}', '{$this->arrRequest["dt_nascimento"]}', '{$this->arrRequest["ds_sexo"]}',
                                 '{$this->arrRequest["cd_cidade"]}', '{$this->arrRequest["cd_id_tipo"]}', '{$this->arrRequest["ds_email"]}', '{$this->arrRequest["ds_senha"]}')
                      RETURNING cd_pessoa";
      
      if (!$this->ConexaoBanco->runQueryes($sqlPessoa, $this->arrRequest["f_action"]))
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
      $sqlPessoa = "UPDATE pessoa
                       SET nm_pessoa     = '{$this->arrRequest["nm_pessoa"]}',
                           nr_telefone   = '{$this->arrRequest["nr_telefone"]}',
                           dt_nascimento = '{$this->arrRequest["dt_nascimento"]}',
                           ds_sexo       = '{$this->arrRequest["ds_sexo"]}',
                           cd_cidade     = '{$this->arrRequest["cd_cidade"]}',
                           cd_id_tipo    = '{$this->arrRequest["cd_id_tipo"]}',
                           ds_email      = '{$this->arrRequest["ds_email"]}',
                           ds_senha      = '{$this->arrRequest["ds_senha"]}'
                     WHERE cd_pessoa = '{$this->arrRequest["cd_pessoa"]}'
                 RETURNING cd_pessoa";

      if (!$this->ConexaoBanco->runQueryes($sqlPessoa, $this->arrRequest["f_action"]))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      //TODO: Implementar exclusao do usuario
//      //Se não existem pendencias, entra e remove a cidade
//      if (!$this->validarExistenciaPendencias())
//        if (!$this->ConexaoBanco->runQueryes("DELETE FROM cidade WHERE cd_cidade = '{$this->arrRequest["cd_cidade"]}'", $this->arrRequest["f_action"]))
//          throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
    }
    
    /**
     * @throws Exception
     */
    public function montarExtratoDadosUsuario(): string
    {
      $arrDadosUsuario = $this->obterDadosPessoa("extrato");
      $nrTelefone      = padronizaFone($arrDadosUsuario["nr_telefone"], "sys", "pt_BR");
      
      return <<<HTML
        <div class="container">
          <h3>Dados do Usuário</h3>
            <table>
              <tr>
                <th>Nome</th>
                <td>{$arrDadosUsuario["nm_pessoa"]}</td>
              </tr>
              <tr>
                <th>Dt. Nascimento</th>
                <td>{$arrDadosUsuario["dt_nascimento"]}</td>
              </tr>
              <tr>
                <th>Tipo</th>
                <td>{$arrDadosUsuario["tipo_usuario"]}</td>
              </tr>
              <tr>
                <th>Cidade</th>
                <td>{$arrDadosUsuario["nm_cidade"]}</td>
              </tr>
              <tr>
                <th>Telefone</th>
                <td>{$nrTelefone}</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>{$arrDadosUsuario["ds_email"]}</td>
              </tr>
            </table>
          <a href=index.php>Voltar ao Início</a>
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
    public function montarFormManutencaoCadastroUsuario() : string
    {
      $nm_pessoa     = "";
      $nr_telefone   = "";
      $dt_nascimento = "";
      $ds_email      = "";
      $ds_senha      = "";
      $dsCampoHidden = "";
      $dsCampoAcao   = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"inserir\" checked>Inserir</label>";
      $dsLinks       = "<p><a href=\"login.php\">Voltar p/ Login</a></p>";
      
      if (isset($_SESSION["cd_pessoa"]))
      {
        $arrDadosPessoa                 = $this->obterDadosPessoa();
        $nm_pessoa                      = $arrDadosPessoa["nm_pessoa"];
        $nr_telefone                    = $arrDadosPessoa["nr_telefone"];
        $dt_nascimento                  = $arrDadosPessoa["dt_nascimento"];
        $ds_email                       = $arrDadosPessoa["ds_email"];
        $ds_senha                       = $arrDadosPessoa["ds_senha"];
        $this->arrRequest["ds_sexo"]    = $arrDadosPessoa["ds_sexo"];
        $this->arrRequest["cd_cidade"]  = $arrDadosPessoa["cd_cidade"];
        $this->arrRequest["cd_id_tipo"] = $arrDadosPessoa["cd_id_tipo"];
        
        $dsCampoHidden = "<input type=hidden name=cd_pessoa value={$_SESSION["cd_pessoa"]}>";
        $dsCampoAcao   = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"atualizar\" checked>Alterar</label>
                          <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"deletar\">Excluir</label>";
        $dsLinks       = "<a href=\"index.php\">Voltar ao Início</a>";
      }
      
      return <<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          {$dsCampoHidden}
          <input type="hidden" name="tabela"   id="id_tabela" value="pessoa">
          <input type="hidden" name="tela"     id="id_tela"   value="manutencao">
          <table>
            <tr>
              <th>Nome</th>
              <td style="text-align: left"><input type="text" name="nm_pessoa" id="nm_pessoa" size="40" minlength="2" value="{$nm_pessoa}" oninput="validateInput(this)"></td>
              <th>Dt. Nascimento</th>
              <td style="text-align: left"><input type="date" name="dt_nascimento" id="dt_nascimento" value="{$dt_nascimento}"></td>
            </tr>
            <tr>
              <th>Telefone</th>
              <td style="text-align: left"><input type="text" name="nr_telefone" id="nr_telefone" minlength="11" maxlength="11" value="{$nr_telefone}" oninput="validateInput(this)"></td>
              <th>Email</th>
              <td style="text-align: left"><input type="email" name="ds_email" id="ds_email" value="{$ds_email}"></td>
            </tr>
            <tr>
              <th>Sexo</th>
              <td style="text-align: left"><select name="ds_sexo" id="ds_sexo">{$this->obterOptionsSexo()}</select></td>
              <th>Tipo Usuário</th>
              <td style="text-align: left"><select name="cd_id_tipo" id="cd_id_tipo">{$this->obtemTipoUsuario()}</select></td>
            </tr>
            <tr>
              <th>Cidade</th>
              <td style="text-align: left"><select name="cd_cidade" id="cd_cidade">{$this->obterOpCidades()}</select></td>
              <th>Senha</th>
              <td style="text-align: left"><input type="password" name="ds_senha" id="ds_senha" size="40" minlength="5" value="{$ds_senha}"></td>
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
        {$dsLinks}
HTML;
    }
    
    /**
     * Obtem dados pessoa.
     * @throws Exception
     */
    protected function obterDadosPessoa($idTela = "manutencao")
    {
      $sqlFields = "";
      $sqlJoin   = "";
      
      switch ($idTela)
      {
        case "manutencao":
          $sqlFields = "p.nm_pessoa,
                        p.nr_telefone,
                        p.dt_nascimento,
                        p.ds_sexo,
                        p.cd_cidade,
                        p.cd_id_tipo,
                        p.ds_email,
                        p.ds_senha";
        break;
        case "extrato":
          $sqlFields = "p.nr_telefone,
                        p.ds_email,
                        (CASE
                           WHEN p.cd_id_tipo = 1 THEN 'Usuário Administrativo'
                           WHEN p.cd_id_tipo = 2 THEN 'Usuario Comum'
                         END)                                  AS tipo_usuario,
                        p.cd_pessoa || ' / ' || p.nm_pessoa    AS nm_pessoa,
                        TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
                        u.ds_sigla || ' / ' || c.nm_cidade        AS nm_cidade";
          $sqlJoin   = "JOIN cidade c ON c.cd_cidade = p.cd_cidade
                        JOIN uf     u ON     u.cd_uf = c.cd_uf";
        break;
      }
      
      $sqlDadosPessoa =<<<SQL
        SELECT {$sqlFields}
          FROM pessoa p
          {$sqlJoin}
         WHERE cd_pessoa = '{$this->arrRequest["cd_pessoa"]}'
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlDadosPessoa))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());

      return $this->ConexaoBanco->getLastQueryResults()[0];
    }
    
    /**
     * Obtem e retorna as opções de tipos de usuario.
     *
     * @return string
     * @throws Exception
     */
    protected function obtemTipoUsuario(): string
    {
      $sqlTipoUsuario =<<<SQL
        SELECT tp.cd_id_tipo AS value,
               tp.nm_tipo    AS description
          FROM tipo_pessoa tp
         ORDER BY tp.nm_tipo
SQL;
      
      if (!$this->ConexaoBanco->runQueryes($sqlTipoUsuario))
        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
      
      $arrOptionsTipoPessoa = [];
      
      // Loop para concatenar as opções em uma variável
      foreach ($this->ConexaoBanco->getLastQueryResults() as $tipoUsuario)
      {
        $idSelected = "";
        
        if (isset($this->arrRequest["cd_id_tipo"]) && ($this->arrRequest["cd_id_tipo"] == $tipoUsuario["value"]))
          $idSelected = "selected";
        
        $arrOptionsTipoPessoa[] = "<option value=\"{$tipoUsuario["value"]}\" {$idSelected}>{$tipoUsuario["description"]}</option>";
      }
      
      setFirstEmpty($arrOptionsTipoPessoa);
      return implode($arrOptionsTipoPessoa);
    }
    
    /**
     * Obtem as opções do campo Sexo.
     * @return string
     */
    protected function obterOptionsSexo(): string
    {
      $arrOptionsSexo = [
        ["value" => "F", "description" => "Feminino"],
        ["value" => "M", "description" => "Masculino"]
      ];
      
      $arrOpDsSexo = [];
      
      foreach ($arrOptionsSexo as $option)
      {
        $idSelected = "";
        
        if (isset($this->arrRequest["ds_sexo"]) && ($this->arrRequest["ds_sexo"] == $option["value"]))
          $idSelected = "selected";
        
        $arrOpDsSexo[] = "<option value=\"{$option["value"]}\" {$idSelected}>{$option["description"]}</option>";
      }
      
      setFirstEmpty($arrOpDsSexo);
      return implode($arrOpDsSexo);
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
      
      // Loop para concatenar as opções de cidades em uma variável
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
  }