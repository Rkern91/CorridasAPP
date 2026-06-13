<?php
  require_once("ClassLoaderController.php");
  require_once("../helpers.inc.php");
  
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
     * M�todo respons�vel por processar a requisi��o do formul�rio
     * para cada classe do Projeto de forma din�mica.
     * @return void
     */
    public function callProcessAction()
    {
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
  