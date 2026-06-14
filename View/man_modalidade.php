<?php
  require_once("../auth_guard.php");
  require_once("../Controllers/ModalidadeController.php");

  try
  {
    $ModalidadeController = new ModalidadeController();

    $cdModalidade  = $_REQUEST["cd_modalidade"] ?? "";
    $arrModalidade = [];

    if ($cdModalidade !== "")
      $arrModalidade = $ModalidadeController->obterModalidade();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=modalidade&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $dsDescricao = $arrModalidade["ds_descricao"]          ?? "";
  $dsData      = $arrModalidade["dt_largada_modalidade"] ?? "";
  $dsHora      = $arrModalidade["hr_largada_modalidade"] ?? "";
  $dsDistancia = $arrModalidade["vl_km_distancia"]       ?? "";
  $dsInscricao = isset($arrModalidade["vl_valor"]) ? padronizaMoeda($arrModalidade["vl_valor"], 2, "sys", "pt_BR") : "";

  $tituloPagina = "Manutenção de Modalidade";
  require("header.php");
?>
  <div class="container">
    <h3>Manutenção de Modalidade</h3>
    <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
      <?php if ($cdModalidade !== ""): ?>
        <input type="hidden" name="cd_modalidade" value="<?= $arrModalidade["cd_modalidade"] ?>">
      <?php endif; ?>
      <input type="hidden" name="tabela" id="id_tabela" value="modalidade">
      <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
      <table>
        <tr>
          <th>Modalidade</th>
          <td colspan="3" style="text-align: left"><input type="text" name="ds_descricao" id="ds_descricao" size="40" minlength="2" value="<?= $dsDescricao ?>" oninput="validateInput(this)"></td>
        </tr>
        <tr>
          <th>Valor (R$)</th>
          <td style="text-align: left"><input type="text" name="vl_valor" id="vl_valor" value="<?= $dsInscricao ?>" onchange="ajustarFormatoValores(this)"></td>
          <th>Distância (KM)</th>
          <td style="text-align: left"><input type="text" name="vl_km_distancia" minlength="1" maxlength="3" size="3" id="vl_km_distancia" value="<?= $dsDistancia ?>"></td>
        </tr>
        <tr>
          <th>Data</th>
          <td style="text-align: left"><input type="date" name="dt_largada_modalidade" id="dt_largada_modalidade" value="<?= $dsData ?>"></td>
          <th>Hora</th>
          <td><input type="time" name="hr_largada_modalidade" id="hr_largada_modalidade" value="<?= $dsHora ?>"></td>
        </tr>
        <tr>
          <th>Ação:</th>
          <td colspan="3">
            <?php if ($cdModalidade !== ""): ?>
              <label><input class="f_action" type="radio" name="f_action" value="atualizar" checked>Alterar</label>
              <label><input class="f_action" type="radio" name="f_action" value="deletar">Excluir</label>
            <?php else: ?>
              <label><input type="radio" name="f_action" id="f_action" value="inserir" checked>Inserir</label>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: center">
            <input type="submit" name="btn_submit" id="btn_submit" value="Confirmar">
          </td>
        </tr>
      </table>
    </form>
    <p><a href="sel_modalidade.php">Listagem de Modalidades</a></p>
  </div>
<?php require("footer.php"); ?>
