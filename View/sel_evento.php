<?php
  require_once(__DIR__ . "/../../funcoes/funcoes.inc.php");
  
  $dsTableEventos = "";
  $dsCampoHidden  = "";
  $Eventos        = obtemDadosEventoListagem();
  $dsTRows        = "";

  if (!empty($Eventos))
  {
    foreach ($Eventos as $evento)
    {
      $dsLinkEditar    = "<a href=\"man_evento.php?cd_evento={$evento["cd_evento"]}\">Editar</a>";
      $dsTipModalidade = "";
      
      //Se existir modalidade atrelada ao evento, cria uma especie de tip ao sobrepor o mouse na coluna
      if (str_value($evento["ds_descricacao"]))
      {
        $itensModalidade = "";
        $dsModalidades   = explode(",", str_replace("\"", "", trim($evento["ds_descricacao"], "{}")));
        
        foreach ($dsModalidades as $modal)
          $itensModalidade .= "$modal\n";
        
        $dsTipModalidade = "$itensModalidade";
      }
      
      $dsTRows .=<<<HTML
        <tr>
          <td style="text-align: center">{$evento["cd_evento"]}</td>
          <td>{$evento["nm_evento"]}</td>
          <td style="text-align: center">{$evento["dt_evento"]}</td>
          <td>{$evento["nm_cidade"]}</td>
          <td style="text-align: center" title="{$dsTipModalidade}">{$evento["ds_modalidades"]}</td>
          <td style="text-align: center">{$dsLinkEditar}</td>
        </tr>
HTML;
      
      $dsTableEventos =
        "<div class=\"container\">
          <h3>Listagem de Eventos</h3>
          <table>
            <tr>
              <th>Cód.</th>
              <th>Evento</th>
              <th>Data</th>
              <th>Cidade</th>
              <th>Modalidades (KMs)</th>
              <th>-</th>
            </tr>
            {$dsTRows}
          </table>
          <p><a href=man_evento.php>Adicionar Evento</a> | <a href=\"../index.php\">Voltar ao Início</a></p>
          </div>";
    }
  }
  else
    $_REQUEST["id_operacao"] = "cadastrar";
  
  //Define a operacao executada ao chamar a tela e cria um alerta
  if (isset($_REQUEST["id_operacao"]))
  {
    $dsCampoHidden = "<input type=\"hidden\" class=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">" .
                     "<input type=\"hidden\" class=\"ds_origem\"   value=\"evento\">";
  }
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8"/>
  <link rel="stylesheet" type="text/css" href="../../css/style.css">

  <title>Listagem de Eventos</title>
</head>
<body>
<?php
  echo $dsTableEventos;
  echo $dsCampoHidden;
?>
<script src="../../js/verificacaoForm.js"></script>
</body>
</html>