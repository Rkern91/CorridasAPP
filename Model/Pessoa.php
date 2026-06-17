<?php
  require_once("Database.php");
  require_once("../helpers.inc.php");

  class Pessoa
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
    }

    /**
     * Insere um novo registro.
     *
     * @return void
     * @throws Exception
     */
    public function inserirAcao()
    {
      $sqlPessoa = "INSERT INTO pessoa (nm_pessoa, nr_telefone, dt_nascimento, ds_sexo,
                                       cd_cidade, ds_email, ds_senha)
                         VALUES ($1, $2, $3, $4, $5, $6, $7)
                      RETURNING cd_pessoa";

      $this->Database->execute($sqlPessoa, [
        $this->arrRequest["nm_pessoa"],
        $this->arrRequest["nr_telefone"],
        $this->arrRequest["dt_nascimento"],
        $this->arrRequest["ds_sexo"],
        $this->arrRequest["cd_cidade"],
        $this->arrRequest["ds_email"],
        password_hash($this->arrRequest["ds_senha"], PASSWORD_DEFAULT)
      ]);
    }

    /**
     * Atualiza o registro selecionado.
     *
     * @return void
     * @throws Exception
     */
    public function atualizarAcao()
    {
      $arrParams = [
        $this->arrRequest["nm_pessoa"],
        $this->arrRequest["nr_telefone"],
        $this->arrRequest["dt_nascimento"],
        $this->arrRequest["ds_sexo"],
        $this->arrRequest["cd_cidade"],
        $this->arrRequest["ds_email"]
      ];

      // A senha só é alterada se uma nova foi informada; em branco mantém a atual.
      $dsSenha = $this->arrRequest["ds_senha"] ?? "";

      if ($dsSenha !== "")
      {
        $sqlSenha    = ", ds_senha = $7";
        $arrParams[] = password_hash($dsSenha, PASSWORD_DEFAULT);
      }
      else
        $sqlSenha = "";

      $arrParams[] = $this->arrRequest["cd_pessoa"];
      $nrParamCdPessoa = count($arrParams);

      $sqlPessoa = "UPDATE pessoa
                       SET nm_pessoa     = $1,
                           nr_telefone   = $2,
                           dt_nascimento = $3,
                           ds_sexo       = $4,
                           cd_cidade     = $5,
                           ds_email      = $6
                           {$sqlSenha}
                     WHERE cd_pessoa = \${$nrParamCdPessoa}
                 RETURNING cd_pessoa";

      $this->Database->execute($sqlPessoa, $arrParams);
    }

    /**
     * Exclui o registro selecionado.
     * @return void
     * @throws Exception
     */
    public function deletarAcao()
    {
      //TODO: Implementar exclusao do usuario (Fase 5), considerando inscrições vinculadas.
    }

    /**
     * Retorna os dados da pessoa para a tela de manutenção (edição).
     *
     * @return array
     * @throws Exception
     */
    public function obterDadosPessoa(): array
    {
      $sqlDadosPessoa =<<<SQL
        SELECT p.nm_pessoa,
               p.nr_telefone,
               p.dt_nascimento,
               p.ds_sexo,
               p.cd_cidade,
               p.ds_email
          FROM pessoa p
         WHERE p.cd_pessoa = $1
SQL;

      $arrPessoa = $this->Database->select($sqlDadosPessoa, [$this->arrRequest["cd_pessoa"]]);

      return $arrPessoa[0] ?? [];
    }

    /**
     * Retorna os dados consolidados da pessoa para a tela de extrato.
     *
     * @return array
     * @throws Exception
     */
    public function obterExtratoUsuario(): array
    {
      $sqlExtrato =<<<SQL
        SELECT p.nr_telefone,
               p.ds_email,
               (CASE
                  WHEN p.cd_id_tipo = 1 THEN 'Usuário Administrativo'
                  WHEN p.cd_id_tipo = 2 THEN 'Usuario Comum'
                END)                                  AS tipo_usuario,
               p.cd_pessoa || ' / ' || p.nm_pessoa    AS nm_pessoa,
               TO_CHAR(p.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento,
               u.ds_sigla || ' / ' || c.nm_cidade     AS nm_cidade
          FROM pessoa p
          JOIN cidade c ON c.cd_cidade = p.cd_cidade
          JOIN uf     u ON     u.cd_uf = c.cd_uf
         WHERE p.cd_pessoa = $1
SQL;

      $arrExtrato = $this->Database->select($sqlExtrato, [$this->arrRequest["cd_pessoa"]]);

      return $arrExtrato[0] ?? [];
    }

    /**
     * Retorna a listagem de usuários para a tela administrativa.
     *
     * @return array
     * @throws Exception
     */
    public function obterListagemUsuarios(): array
    {
      $sqlUsuarios =<<<SQL
        SELECT p.cd_pessoa,
               p.nm_pessoa,
               p.ds_email,
               (CASE
                  WHEN p.cd_id_tipo = 1 THEN 'Administrador'
                  ELSE 'Comum'
                END) AS tipo_usuario
          FROM pessoa p
         ORDER BY p.nm_pessoa
SQL;

      return $this->Database->select($sqlUsuarios);
    }

    /**
     * Retorna a lista de cidades para popular o campo de seleção.
     *
     * @return array Lista de [value, description].
     * @throws Exception
     */
    public function obterCidades() : array
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
  }
