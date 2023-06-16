<?php
  
  class TelaUserLoginController
  {
    const ID_USUARIO_ADM   = 1;
    const ID_USUARIO_COMUM = 2;
    
    /**
     * Construtor de Classe
     */
    public function __construct()
    {
      switch ($_SESSION["id_tipo_usuario"])
      {
        case self::ID_USUARIO_ADM:   $this->montarFormUsuarioAdm();   break;
        case self::ID_USUARIO_COMUM: $this->montarFormUsuarioComum(); break;
      }
    }
    
    /**
     * Cria o formulário para usuarios administrativos
     * @return void
     */
    private function montarFormUsuarioAdm()
    {
      echo <<<HTML
        <h3>Área Administrativa</h3>
        <table>
          <tbody>
            <tr>
              <th>Áreas</th>
              <th colspan="2">Ações</th>
            </tr>
            <tr>
              <th>Eventos</th>
              <td><a href="../View/sel_evento.php">Ir p/ Eventos</a></td>
              <td><a href="../View/man_evento.php" title="Adicionar Evento">( + )</a></td>
            </tr>
            <tr>
              <th>Modalidades</th>
              <td><a href="../View/sel_modalidade.php">Ir p/ Modalidades</a></td>
              <td><a href="../View/man_modalidade.php" title="Adicionar Modalidade">( + )</a></td>
            </tr>
            <tr>
              <th>Cidades</th>
              <td><a href="../View/sel_cidade.php">Ir p/ Cidades</a></td>
              <td><a href="../View/man_cidade.php" title="Adicionar Cidade">( + )</a></td>
            </tr>
            <tr>
              <th>Cadastro</th>
              <td><a href="../View/man_cadastro_usuario.php?cd_pessoa={$_SESSION["cd_pessoa"]}">Editar</a></td>
              <td><a href="../View/con_dados_usuario.php?cd_pessoa={$_SESSION["cd_pessoa"]}">Extrato</a></td>
            </tr>
          </tbody>
        </table>
        <p><a href="logout.php?cd_pessoa={$_SESSION["cd_pessoa"]}">Sair</a></p>
HTML;
    }
    
    /**
     * Cria o formulário para usuários comuns
     * @return void
     */
    private function montarFormUsuarioComum()
    {
      echo <<<HTML
        <h3>MENU</h3>
        <table>
          <tbody>
            <tr>
              <th>Áreas</th>
              <th colspan="2">Ações</th>
            </tr>
            <tr>
              <th>Eventos</th>
              <td colspan="2"><a href="sel_evento.php">Ir p/ Eventos</a></td>
            </tr>
            <tr>
              <th>Inscrições</th>
              <td colspan="2"><a href="sel_inscricao.php">Visualizar</a></td>
            </tr>
            <tr>
              <th>Cadastro</th>
              <td><a href="man_cadastro_usuario.php?cd_pessoa={$_SESSION["cd_pessoa"]}">Editar</a></td>
              <td><a href="con_dados_usuario.php?cd_pessoa={$_SESSION["cd_pessoa"]}">Extrato</a></td>
            </tr>
          </tbody>
        </table>
        <p><a href="logout.php?cd_pessoa={$_SESSION["cd_pessoa"]}">Sair</a></p>
HTML;
    }
  }