<?php
  require_once("../../funcoes/funcoes.inc.php");
  
  $dsCampoHidden        = "";
  $dsCampoDescricao     = "";
  $dsCampoData          = "";
  $dsCampoHora          = "";
  $dsCampoDistancia     = "";
  $dsCampoInscricao     = "";
  $dsCampoAcao          = "<label><input type=\"radio\" name=\"f_action\" id=\"f_action\" value=\"insert\" checked>Inserir</label>";
  $arrOptions           = [];
  $ArrDadosModalidade   = [];
  $arrOptionsModalidade = [];

  if (isset($_REQUEST["cd_modalidade"]))
  {
    $arrDadosModalidade = obtemDadosModalidade($_REQUEST["cd_modalidade"]);

    $dsCampoData        = $arrDadosModalidade["dt_largada_modalidade"];
    $dsCampoHora        = $arrDadosModalidade["hr_largada_modalidade"];
    $dsCampoDescricao   = $arrDadosModalidade["ds_descricao"];
    $dsCampoDistancia   = $arrDadosModalidade["vl_km_distancia"];
    $dsCampoInscricao   = $arrDadosModalidade["vl_valor"];
    $dsCampoHidden      = "<input type=hidden name=cd_key value={$arrDadosModalidade["cd_modalidade"]}>";
    $dsCampoAcao        = "<label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"update\" checked>Alterar</label>
                           <label><input class=\"f_action\" type=\"radio\" name=\"f_action\" value=\"delete\">Excluir</label>";
  }
  
  $form = <<<HTML
    <form action="../../processActionForm.php" id="form" method="post">
      {$dsCampoHidden}
      <input type="hidden" name="tabela" id="id_tela" value="modalidade">
      <table>
        <tr>
          <th>Modalidade</th>
          <td colspan="3" style="text-align: left"><input type="text" name="ds_descricao" id="ds_descricao" size="40" minlength="2" value="{$dsCampoDescricao}" oninput="validateInput(this)"></td></tr>
        </tr>
        <tr>
          <th>Valor (R$)</th>
          <td style="text-align: left"><input type="text" name="vl_valor" id="vl_valor" value="{$dsCampoInscricao}" onchange="ajustarFormatoValores(this)"></td>
          <th>Distância (KM)</th>
          <td style="text-align: left"><input type="text" name="vl_km_distancia" minlength="1" maxlength="3" size="3" id="vl_km_distancia" value="{$dsCampoDistancia}"></td>
        </tr>
        <tr>
          <th>Data</th>
          <td style="text-align: left"><input type="date" name="dt_largada_modalidade" id="dt_largada_modalidade" value="{$dsCampoData}"></td>
          <th>Hora</th>
          <td><input type="time" name="hr_largada_modalidade" id="hr_largada_modalidade" value="{$dsCampoHora}"></td>
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
HTML;

?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>Manutenção de Modalidade</title>
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
  </head>
  <body>
    <div class="container">
      <h3>Manutenção de Modalidade</h3>
      <?php echo $form ?>
      <p><a href="sel_modalidade.php">Listagem de Modalidade</a></p>
    </div>
    <script src="../../js/funcoes.js"></script>
  </body>
</html>

