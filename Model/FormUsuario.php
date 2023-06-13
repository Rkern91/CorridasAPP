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
      //TODO: Implementar atualização de dados
//      $sqlPessoa = "UPDATE pessoa
//                       SET nm_pessoa     = '{$this->arrRequest["nm_pessoa"]}',
//                           nr_telefone   = '{$this->arrRequest["nr_telefone"]}',
//                           dt_nascimento = '{$this->arrRequest["dt_nascimento"]}',
//                           ds_sexo       = '{$this->arrRequest["ds_sexo"]}',
//                           cd_cidade     = '{$this->arrRequest["cd_cidade"]}',
//                           cd_id_tipo    = '{$this->arrRequest["cd_id_tipo"]}',
//                           ds_email      = '{$this->arrRequest["ds_email"]}',
//                           ds_senha      = '{$this->arrRequest["ds_senha"]}'
//                     WHERE cd_pessoa = '{$this->arrRequest["cd_pessoa"]}'
//                 RETURNING cd_pessoa";
//
//      if (!$this->ConexaoBanco->runQueryes($sqlPessoa, $this->arrRequest["f_action"]))
//        throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
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
     * Monta o formulário da tela para edição
     * ou novo registro do processo.
     *
     * @return string
     * @throws Exception
     */
    public function montarFormManutencaoCadastroUsuario() : string
    {
      return <<<HTML
        <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
          <input type="hidden" name="tabela"   id="id_tabela" value="pessoa">
          <input type="hidden" name="tela"     id="id_tela"   value="cadastro">
          <input type="hidden" name="f_action" id="f_action"  value="inserir">
          <table>
            <tr>
              <th>Nome</th>
              <td style="text-align: left"><input type="text" name="nm_pessoa" id="nm_pessoa" size="40" minlength="2" oninput="validateInput(this)"></td>
              <th>Dt. Nascimento</th>
              <td style="text-align: left"><input type="date" name="dt_nascimento" id="dt_nascimento"></td>
            </tr>
            <tr>
              <th>Telefone</th>
              <td style="text-align: left"><input type="text" name="nr_telefone" id="nr_telefone" minlength="11" maxlength="11" oninput="validateInput(this)"></td>
              <th>Email</th>
              <td style="text-align: left"><input type="email" name="ds_email" id="ds_email"></td>
            </tr>
            <tr>
              <th>Sexo</th>
              <td style="text-align: left"><select name="ds_sexo" id="ds_sexo">
                                                         <option value=""></option>
                                                         <option value="F">Feminino</option>
                                                         <option value="M">Masculino</option>
                                                       </select>
              </td>
              <th>Tipo Usuário</th>
              <td style="text-align: left"><select name="cd_id_tipo" id="cd_id_tipo">{$this->obtemTipoUsuario()}</select></td>
            </tr>
            <tr>
              <th>Cidade</th>
              <td style="text-align: left"><select name="cd_cidade" id="cd_cidade">{$this->obterOpCidades()}</select></td>
              <th>Senha</th>
              <td style="text-align: left"><input type="password" name="ds_senha" id="ds_senha" size="40" minlength="5"></td>
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
        $arrOptionsTipoPessoa[] = "<option value=\"{$tipoUsuario["value"]}\">{$tipoUsuario["description"]}</option>";
      
      setFirstEmpty($arrOptionsTipoPessoa);
      return implode($arrOptionsTipoPessoa);
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
        $arrOptionsCidades[] = "<option value=\"{$cidade["value"]}\">{$cidade["description"]}</option>";
      
      setFirstEmpty($arrOptionsCidades);
      return implode($arrOptionsCidades);
    }
    
    /**
     * Verifica se o usuario atual está ligado a algum evento
     * e bloqueia a exclusão.
     *
     * @return boolean
     * @throws Exception
     */
    protected function validarExistenciaPendencias() : bool
    {
      //TODO: Implementar validacao de pendencias (Tabelas ligadas ao usuario)
//      $arrPendencias = [
//        "inscricao"
//      ];
//
//      foreach ($arrPendencias as $dsTablePendencia)
//      {
//        $sqlPendenciasCidade =<<<SQL
//        SELECT COUNT(*) AS qt_eventos
//          FROM {$dsTablePendencia} tp
//         WHERE tp.cd_cidade = '{$this->arrRequest["cd_pessoa"]}'
//SQL;
//
//        if (!$this->ConexaoBanco->runQueryes($sqlPendenciasCidade))
//          throw new Exception("DESCRIÇÃO: " . $this->ConexaoBanco->getLastQueryError());
//
//        if ($this->ConexaoBanco->getLastQueryResults()[0]["qt_eventos"] > 0)
//          throw new Exception("A cidade selecionada está ligada a um(a) ou mais {$dsTablePendencia}(s)!");
//      }
//
//      return false;
    }
  }