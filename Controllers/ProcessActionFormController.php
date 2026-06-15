<?php
  require_once("ClassLoaderController.php");
  require_once("../helpers.inc.php");
  require_once("../session.php");

  class ProcessActionFormController
  {
    /**
     * Array de classes instanciadas de forma din�mica
     * no m�todo CallProcessAction
     * @var array|string[]
     */
    protected array $opNmClass = [
      "cidade"      => "FormCidade",
      "evento"      => "FormEvento",
      "modalidade"  => "FormModalidade",
      "pessoa"      => "FormUsuario",
      "inscricao"   => "FormInscricao"
    ];
    
    /**
     * Nome da tabela atual onde
     * ser� realizado opera��es de banco
     * @var string|mixed
     */
    public string $nmTabela;
    
    /**
     * A��o executada no formul�rio
     * @var string|mixed
     */
    public string $formAction;
    
    /**
     * Dados enviados
     * @var array
     */
    public array  $arrRequest;
    
    /**
     * Construtor da classe
     * Recebe o request por parametro e define a tabela e acao submetida.
     * @param $arrRequest
     */
    public function __construct($arrRequest)
    {
      $this->nmTabela   = $arrRequest["tabela"];
      $this->formAction = $arrRequest["f_action"];
      $this->arrRequest = $arrRequest;
    }

    /**
     * Garante que a ação é permitida ao usuário atual:
     * - cadastro de novo usuário (pessoa/inserir) é público;
     * - demais ações exigem login;
     * - cidade/evento/modalidade são exclusivas de administradores;
     * - em ações sobre dados próprios (pessoa/inscricao), força o cd_pessoa da sessão.
     * @return void
     */
    protected function validarPermissao()
    {
      $tabelasAdmin    = ["cidade", "evento", "modalidade"];
      $cadastroPublico = ($this->nmTabela == "pessoa" && $this->formAction == "inserir");

      if (!$cadastroPublico && !isset($_SESSION["cd_pessoa"]))
      {
        header("Location: ../View/login.php");
        exit;
      }

      if (in_array($this->nmTabela, $tabelasAdmin) && (($_SESSION["id_tipo_usuario"] ?? null) != 1))
      {
        $dsMensagem = "Acesso negado: ação restrita a administradores.";
        header("Location: ../View/erro.php?dsOrigem={$this->nmTabela}&dsMensagem=" . urlencode($dsMensagem));
        exit;
      }

      //Usuário só mexe nos próprios dados/inscrições: o cd_pessoa vem da sessão, não do request.
      if (!$cadastroPublico && in_array($this->nmTabela, ["pessoa", "inscricao"]))
        $this->arrRequest["cd_pessoa"] = $_SESSION["cd_pessoa"];
    }

    /**
     * Método responsável por processar a requisição do formulário
     * para cada classe do Projeto de forma dinâmica.
     * @return void
     */
    public function callProcessAction()
    {
      $this->validarPermissao();

      try
      {
        //Monta o nome do metodo que vai ser chamado na classe.
        $formAction = "{$this->formAction}Acao";
        $ActionCall = new $this->opNmClass[$this->nmTabela]($this->arrRequest);
        $ActionCall->{$formAction}();
        
        if ($this->nmTabela == "pessoa")
        {
          if ($this->formAction == "inserir")
          {
            header("Location: ../View/login.php?id_operacao=cadastro");
            exit;
          }
          
          if ($this->formAction == "atualizar")
          {
            header("Location: ../View/index.php?id_operacao=atualizar");
            exit;
          }
          
          if ($this->formAction == "deletar")
          {
            session_destroy();
            header("Location: ../View/login.php?id_operacao=exclusaoCadastro");
            exit;
          }
        }
        
        header("Location: ../View/sel_{$this->nmTabela}.php?id_operacao={$this->formAction}");
        exit;
      }
      catch (Exception $e)
      {
        $this->padronizarRetornoErro($e->getMessage());
      }
    }
    
    /**
     * Padroniza o retorno de erros
     * e redireciona para a tela de erros.
     * @param $dsMensagem - Descric��o do erro ocorrido na opera��o.
     * @return void
     */
    protected function padronizarRetornoErro($dsMensagem)
    {
      $error_message = "Erro ao {$this->formAction} registro. {$dsMensagem}";
      header("Location: ../View/erro.php?id_erro=" . Database::$opIdErrosBd[$this->formAction] . "&dsOrigem={$this->nmTabela}&dsMensagem=" . urlencode($error_message));
      exit;
    }
  }
  
  $ProcessActionFormController = new ProcessActionFormController($_REQUEST);
  $ProcessActionFormController->callProcessAction();
  