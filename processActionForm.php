<?php
  require_once("funcoes/funcoes.inc.php");

  /**
   * Itera sobre as modalidades adicionadas ao evento e
   * insere o novo registro na tabela de ligação.
   * @param $objConn
   * @param $cdEvento
   * @param $arrModalidadesEvento
   * @return void
   */
  function adicionarModalidadesEvento($objConn, $cdEvento, $arrModalidadesEvento)
  {
    foreach (explode(",", $arrModalidadesEvento) as $cdModalidade)
    {
      $sqlInsertModalidade = "INSERT INTO modalidade_evento (cd_modalidade, cd_evento) VALUES ('{$cdModalidade}', '{$cdEvento}')";
      $retornoModalidade   = pg_query($objConn, $sqlInsertModalidade);
      
      if (!$retornoModalidade)
      {
        $error_message = pg_last_error($retornoModalidade);
        header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_INSERT . "&dsOrigem=evento&dsMensagem=" . urlencode($error_message));
        exit;
      }
    }
  }
  
  /**
   * Adiciona, altera ou excluí um registro de cidade.
   *
   * @param $arrRequest
   */
  function processarAcaoCidade($arrRequest)
  {
    //Obtem uma conexao com o banco e insere o novo evento no banco
    $conexao   = obtemConn();
    $sqlCidade = "";
    
    if (isset($conexao["idErro"]))
    {
      header("Location: erro/erro.php?id_erro={$conexao["idErro"]}&dsOrigem=cidade");
      exit;
    }
    
    if ($arrRequest["f_action"] == "delete")
    {
      //Se não existem pendencias, entra e remove a cidade
      if (!validarExistenciaPendenciasCidade($conexao, $arrRequest["cd_key"]))
      {
        $sqlDeleteCidade = "DELETE FROM cidade WHERE cd_cidade = '{$arrRequest["cd_key"]}'";

        if (!$retornoExclusao = pg_query($conexao, $sqlDeleteCidade))
        {
          $error_message = pg_last_error($retornoExclusao);
          header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_DELETE . "&dsOrigem=cidade&dsMensagem=" . urlencode($error_message));
          exit;
        }
      }
    }
    else
    {
      switch ($_REQUEST["f_action"])
      {
        case "update":
          $sqlCidade = "UPDATE cidade
                           SET nm_cidade = '{$arrRequest["nm_cidade"]}',
                               cd_uf     = '{$arrRequest["cd_uf"]}',
                         WHERE cd_cidade = '{$arrRequest["cd_key"]}'";
          break;
        case "insert":
          $sqlCidade = "INSERT INTO cidade (nm_cidade, cd_uf)
                             VALUES ('{$arrRequest["nm_cidade"]}', '{$arrRequest["cd_uf"]}')";
          break;
      }
      
      //Executa o SQL gerado
      if (!$retornoCidade = pg_query($conexao, $sqlCidade))
      {
        $error_message = pg_last_error($retornoCidade);
        header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_INSERT . "&dsOrigem=evento&dsMensagem=" . urlencode($error_message));
        exit;
      }
    }
    
    encerraConn($conexao);
  }
  
  /**
   * Adiciona, altera ou excluí um registro de evento.
   * @param $arrRequest
   */
  function processarAcaoEvento($arrRequest)
  {
    //Obtem uma conexao com o banco e insere o novo evento no banco
    $conexao   = obtemConn();
    $sqlEvento = "";
    
    if (isset($conexao["idErro"]))
    {
      header("Location: erro/erro.php?id_erro={$conexao["idErro"]}&dsOrigem=evento");
      exit;
    }
    
    if ($arrRequest["f_action"] == "delete")
    {
      removerDependenciasEvento($conexao, $arrRequest["cd_key"]);
      
      $sqlDeleteEvento = "DELETE FROM evento WHERE cd_evento = '{$arrRequest["cd_key"]}'";
      
      if (!$retornoExclusao = pg_query($conexao, $sqlDeleteEvento))
      {
        $error_message = pg_last_error($retornoExclusao);
        header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_DELETE . "&dsOrigem=evento&dsMensagem=" . urlencode($error_message));
        exit;
      }
    }
    else
    {
      $dtEvento = "{$arrRequest["dt_evento"]} {$arrRequest["hr_evento"]}:00";
      
      switch ($_REQUEST["f_action"])
      {
        case "update":
          $sqlEvento = "UPDATE evento
                           SET nm_evento = '{$arrRequest["nm_evento"]}',
                               dt_evento = '{$dtEvento}',
                               cd_cidade = '{$arrRequest["cd_cidade"]}'
                         WHERE cd_evento = '{$arrRequest["cd_key"]}'
                     RETURNING cd_evento";
        break;
        case "insert":
          $sqlEvento = "INSERT INTO evento (nm_evento, dt_evento, cd_cidade)
                             VALUES ('{$arrRequest["nm_evento"]}', '{$dtEvento}', '{$arrRequest["cd_cidade"]}')
                          RETURNING cd_evento";
        break;
      }
      
      //Executa o SQL gerado
      if (!$retornoEvento = pg_query($conexao, $sqlEvento))
      {
        $error_message = pg_last_error($retornoEvento);
        header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_INSERT . "&dsOrigem=evento&dsMensagem=" . urlencode($error_message));
        exit;
      }
      
      //Se tudo correu bem na insercao do evento, adiciona as modalidades selecionadas ao evento
      $arrLinhas = pg_fetch_row($retornoEvento);
      $cdEvento  = $arrLinhas[0];  //Obtem a chave primaria do evento inserido
      
      //Remove algum evento ja inserido anteriormente para nao gerar duplicatas
      removerDependenciasEvento($conexao, $cdEvento);
      adicionarModalidadesEvento($conexao, $cdEvento, $arrRequest["arr_cd_modalidades"]);
    }
    
    encerraConn($conexao);
  }
  
  /**
   * Adiciona, altera ou excluí um registro de Cidade.
   * @param $arrRequest
   * @return void
   */
  function processarAcaoModalidade($arrRequest)
  {
    $conexao       = obtemConn();
    $sqlModalidade = "";
    
    if (isset($conexao["idErro"]))
    {
      header("Location: erro/erro.php?id_erro={$conexao["idErro"]}&dsOrigem=modalidade");
      exit;
    }
    
    if ($arrRequest["f_action"] == "delete")
    {
      //Se não existem pendencias, entra e remove a modalidade
      if (!validarExistenciaPendenciasModalidade($conexao, $arrRequest["cd_key"]))
      {
        $sqlDeleteModalidade = "DELETE FROM modalidade WHERE cd_modalidade = '{$arrRequest["cd_key"]}'";
        
        if (!$retornoExclusao = pg_query($conexao, $sqlDeleteModalidade))
        {
          $error_message = pg_last_error($retornoExclusao);
          header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_DELETE . "&dsOrigem=modalidade&dsMensagem=" . urlencode($error_message));
          exit;
        }
      }
    }
    else
    {
      $dtEvento             = "{$arrRequest["dt_largada_modalidade"]} {$arrRequest["hr_largada_modalidade"]}:00";
      $vlInscricao          = preg_replace("/[^0-9\s,]/", "", $arrRequest["vl_valor"]);
      $vlInscricaoFormatado = Format_Number($vlInscricao, 2, "pt_BR", "sys");

      switch ($arrRequest["f_action"])
      {
        case "update":
          $sqlModalidade = "UPDATE modalidade
                               SET ds_descricao    = '{$arrRequest["ds_descricao"]}',
                                   vl_valor        = '{$vlInscricaoFormatado}',
                                   vl_km_distancia = '{$arrRequest["vl_km_distancia"]}',
                                   dt_largada      = '{$dtEvento}'
                             WHERE cd_modalidade   = '{$arrRequest["cd_key"]}'";
          break;
        case "insert":
          $sqlModalidade = "INSERT INTO modalidade (ds_descricao, dt_largada, vl_km_distancia, vl_valor)
                                 VALUES ('{$arrRequest["ds_descricao"]}', '{$dtEvento}', '{$arrRequest["vl_km_distancia"]}', '{$vlInscricaoFormatado}')";
          break;
      }
      
      //Executa o SQL gerado
      if (!$retornoModalidade = pg_query($conexao, $sqlModalidade))
      {
        $error_message = pg_last_error($retornoModalidade);
        header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_INSERT . "&dsOrigem=evento&dsMensagem=" . urlencode($error_message));
        exit;
      }
    }
    
    encerraConn($conexao);
  }
  
  /**
   * Verifica se a cidade atual está ligada a algum evento
   * e bloqueia a exclusão.
   * @param $objConn
   * @param $cdCidade
   * @return boolean
   */
  function validarExistenciaPendenciasCidade($objConn, $cdCidade) : bool
  {
    $sqlPendenciasCidade =<<<SQL
      SELECT COUNT(*) AS qt_eventos
        FROM evento e
       WHERE e.cd_cidade = '{$cdCidade}'
SQL;
    
    $qtPendencias = pg_fetch_all(pg_query($objConn, $sqlPendenciasCidade))[0]["qt_eventos"];

    if ($qtPendencias > 0)
    {
      $dsMsg = "Não foi possível remover a cidade. A cidade selecionada está ligada a um ou mais eventos e não pode ser excluida!";
      header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_DELETE . "&dsOrigem=cidade&dsMensagem=" . urlencode($dsMsg));
      exit;
    }
    
    return false;
  }
  
  /**
   * Verifica se a modalidade atual está ligada a algum evento
   * e bloqueia a exclusão.
   * @param $objConn
   * @param $cdModalidade
   * @return boolean
   */
  function validarExistenciaPendenciasModalidade($objConn, $cdModalidade) : bool
  {
    $sqlPendenciasModal =<<<SQL
      SELECT COUNT(*) AS qt_eventos
        FROM modalidade_evento me
       WHERE me.cd_modalidade = '{$cdModalidade}'
SQL;
  
    $qtPendencias = pg_fetch_all(pg_query($objConn, $sqlPendenciasModal))[0]["qt_eventos"];
    
    if ($qtPendencias > 0)
    {
      $dsMsg = "Não foi possível remover a modalidade. A modalidade selecionada está ligada a um ou mais eventos e não pode ser excluida!";
      header("Location: erro/erro.php?id_erro=" . Erros::ID_ERRO_DELETE . "&dsOrigem=modalidade&dsMensagem=" . urlencode($dsMsg));
      exit;
    }
    
    return false;
  }
  
  //Valida qual tabela foi submetida e executa a ação
  switch ($_REQUEST["tabela"])
  {
    case "cidade":
      processarAcaoCidade($_REQUEST);
      header("Location: adm/Cidade/sel_cidade.php?id_operacao={$_REQUEST["f_action"]}");
      exit;
    case "evento":
      processarAcaoEvento($_REQUEST);
      header("Location: adm/Evento/sel_evento.php?id_operacao={$_REQUEST["f_action"]}");
      exit;
    case "modalidade":
      processarAcaoModalidade($_REQUEST);
      header("Location: adm/Modalidade/sel_modalidade.php?id_operacao={$_REQUEST["f_action"]}");
      exit;
  }