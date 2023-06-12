<?php
  require_once("ClassLoaderController.php");
  require_once("../helpers.inc.php");
  
  class ProcessActionFormController
  {
    /**
     * @var CidadeController $ActionFormCidade
     */
    public CidadeController $ActionFormCidade;
    
    /**
     * @var EventoController $ActionFormEvento
     */
    public EventoController $ActionFormEvento;
    
    /**
     * @var ModalidadeController $ActionFormModalidade
     */
    public ModalidadeController $ActionFormModalidade;
    
    protected array $opNmClass = [
      "cidade"     => "FormCidade",
      "evento"     => "FormEvento",
      "modalidade" => "FormModalidade"
    ];
    
    public string $nmTabela;
    public string $formAction;
    public array $arrRequest;
    
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
     * Método responsável por processar a requisição do formulário
     * para cada classe do Projeto de forma dinâmica.
     * @return void
     */
    public function callProcessAction()
    {
      try
      {
        //Monta o nome do metodo que vai ser chamado na classe.
        $formAction   = "{$this->formAction}Acao";
        $ActionCidade = new $this->opNmClass[$this->nmTabela]($this->arrRequest);
        $ActionCidade->{$formAction}();
        
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
     * @param $dsMensagem - Descricção do erro ocorrido na operação.
     * @return void
     */
    protected function padronizarRetornoErro($dsMensagem)
    {
      $error_message = "Erro ao {$this->formAction} registro. {$dsMensagem}";
      header("Location: ../erro.php?id_erro=" . ConexaoBanco::$opIdErrosBd[$this->formAction] . "&dsOrigem={$this->nmTabela}&dsMensagem=" . urlencode($error_message));
      exit;
    }
  }
  
  $ProcessActionFormController = new ProcessActionFormController($_REQUEST);
  $ProcessActionFormController->callProcessAction();
  