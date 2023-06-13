<?php
  require_once(__DIR__ . "/../../funcoes/funcoes.inc.php");
  
  $dsTableModalidades = "";
  $dsCampoHidden      = "";
  $Modalidades        = obtemDadosModalidade();
  $dsTRows            = "";
  
  if (!empty($Modalidades))
  {
    foreach ($Modalidades as $modalidade)
    {
      $dsLinkEditar = "<a href=\"man_modalidade.php?cd_modalidade={$modalidade["cd_modalidade"]}\">Editar</a>";
      $vlInscricao  = Format_Number($modalidade["vl_valor"], 2, "sys", "pt_BR");
      $dsTRows .=<<<HTML
        <tr>
          <td style="text-align: center">{$modalidade["cd_modalidade"]}</td>
          <td>{$modalidade["ds_descricao"]}</td>
          <td style="text-align: center">{$modalidade["dt_largada_modalidade"]}</td>
          <td style="text-align: center">{$modalidade["vl_km_distancia"]}</td>
          <td style="text-align: center">R$ {$vlInscricao}</td>
          <td style="text-align: center">{$dsLinkEditar}</td>
        </tr>
HTML;
      
      $dsTableModalidades =
        ">
          <h3>Listagem de Modalidades</h3>
          <table>
            <tr>
              <th>Cód.</th>
              <th>Modalidade</th>
              <th>Data/Hora</th>
              <th>Distância (KM)</th>
              <th>Vl. Inscrição</th>
              <th>-</th>
            </tr>
            {$dsTRows}
          </table>
          <p><a href=man_modalidade.php>Adicionar Modalidade</a> | <a href=>Voltar ao Início</a></p>
          </div>";
    }
  }
  else
    $_REQUEST["id_operacao"] = "cadastrar";
  
  //Define a operacao executada ao chamar a tela e cria um alerta
  if (isset($_REQUEST["id_operacao"]))
  {
    $dsCampoHidden = "<input type=\"hidden\" id=\"ds_operacao\" value=\"{$_REQUEST["id_operacao"]}\">" .
                     "<input type=\"hidden\" id=\"ds_origem\"   value=\"modalidade\">";
  }
?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <title>Listagem de Modalidades</title>
  </head>
  <body>
  <?php
    echo $dsTableModalidades;
    echo $dsCampoHidden;
  ?>
  <script src="../../js/verificacaoForm.js"></script>
  </body>
</html>