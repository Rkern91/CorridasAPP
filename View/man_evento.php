<?php
  require_once("../Controllers/EventoController.php");

  try
  {
    $EventoController = new EventoController();
    $arrCidades      = $EventoController->obterCidades();
    $arrModalidades  = $EventoController->obterModalidades();

    $cdEvento   = $_REQUEST["cd_evento"] ?? "";
    $arrEvento  = [];
    $cdCidade   = "";
    $dsNome     = "";
    $dsData     = "";
    $dsHora     = "";
    $dsCdsModal = "";
    $dsKmModal  = "";
    $qtModal    = "";

    if ($cdEvento !== "")
    {
      $arrEvento  = $EventoController->obterDadosEvento();
      $cdCidade   = $arrEvento["cd_cidade"] ?? "";
      $dsNome     = $arrEvento["nm_evento"] ?? "";
      $dsData     = $arrEvento["dt_evento"] ?? "";
      $dsHora     = $arrEvento["hr_evento"] ?? "";
      $dsCdsModal = trim($arrEvento["arr_cd_modalidades"] ?? "", "{}");
      $dsKmModal  = trim($arrEvento["arr_km_distancia"]   ?? "", "{}");
      $qtModal    = trim($arrEvento["qt_modalidade"]      ?? "", "{}");
    }
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=evento&dsMensagem=" . urlencode($error_message));
    exit;
  }
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
  <?php require_once("head.php"); ?>
  <body>
    <div class="container">
      <h3>Manutenção de Evento</h3>
      <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
        <?php if ($cdEvento !== ""): ?>
          <input type="hidden" name="cd_evento" value="<?= $arrEvento["cd_evento"] ?>">
        <?php endif; ?>
        <input type="hidden" name="tabela"             id="id_tabela"          value="evento">
        <input type="hidden" name="tela"               id="id_tela"            value="manutencao">
        <input type="hidden" name="arr_cd_modalidades" id="arr_cd_modalidades" value="<?= $dsCdsModal ?>">
        <input type="hidden" name="qt_modalidades"     id="qt_modalidades"     value="<?= $qtModal ?>">
        <table>
          <tr>
            <th>Nome</th>
            <td colspan="3" style="text-align: left"><input type="text" name="nm_evento" id="nm_evento" size="40" minlength="2" value="<?= $dsNome ?>" oninput="validateInput(this)"></td>
          </tr>
          <tr>
            <th>Cidade</th>
            <td colspan="3" style="text-align: left">
              <select name="cd_cidade" id="cd_cidade">
                <option value=""></option>
                <?php foreach ($arrCidades as $cidade): ?>
                  <option value="<?= $cidade["value"] ?>" <?= ($cdCidade == $cidade["value"]) ? "selected" : "" ?>><?= $cidade["description"] ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <tr>
            <th>Data</th>
            <td style="text-align: left"><input type="date" name="dt_evento" id="dt_evento" value="<?= $dsData ?>"></td>
            <th>Hora</th>
            <td><input type="time" name="hr_evento" id="hr_evento" value="<?= $dsHora ?>"></td>
          </tr>
          <tr>
            <th>Modalidades</th>
            <td colspan="3" style="text-align: left">
              <select id="op_id_modalidades">
                <?php foreach ($arrModalidades as $modal): ?>
                  <option value="<?= $modal["value"] ?>"><?= $modal["description"] ?></option>
                <?php endforeach; ?>
              </select>
              <a title="Adicionar Modalidade" id="add" onclick="alterarModalidadesSelecionadas('add')">(+)</a> |
              <a title="Remover Modalidade"   id="rem" onclick="alterarModalidadesSelecionadas('rem')">(-)</a>
              <input type="text" id="dsModals" value="<?= $dsKmModal ?>" readonly>
            </td>
          </tr>
          <tr>
            <th>Ação:</th>
            <td colspan="3">
              <?php if ($cdEvento !== ""): ?>
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
      <p><a href="sel_evento.php">Listagem de Eventos</a></p>
    </div>
  </body>
  <?php include("footer.html");?>
</html>
