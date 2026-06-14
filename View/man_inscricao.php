<?php
  require_once("../session.php");
  require_once("../Controllers/InscricaoController.php");

  try
  {
    $InscricaoController = new InscricaoController();
    $arrEvento           = $InscricaoController->ControladorInscricao->obterDadosEventoInscricao();
    $arrModalidades      = $InscricaoController->ControladorInscricao->obterModalidadesEvento();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=inscricao&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $cdInscricao     = $arrEvento["cd_inscricao"]  ?? "";
  $cdModalidadeSel = $arrEvento["cd_modalidade"] ?? "";
  $idEdicao        = hasValue($cdInscricao);
?>

<!DOCTYPE HTML>
<html lang="pt-BR">
<?php require_once("head.php"); ?>
<body>
<div class="container">
  <h3>Inscrição</h3>
  <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
    <input type="hidden" name="cd_evento" value="<?= $arrEvento["cd_evento"] ?? "" ?>">
    <input type="hidden" name="cd_pessoa" value="<?= $_SESSION["cd_pessoa"] ?>">
    <?php if ($idEdicao): ?>
      <input type="hidden" name="cd_inscricao" value="<?= $cdInscricao ?>">
    <?php endif; ?>
    <input type="hidden" name="tabela" id="id_tabela" value="inscricao">
    <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
    <table>
      <tr>
        <th>Evento</th>
        <td style="text-align: left"><?= $arrEvento["nm_evento"] ?? "" ?></td>
        <th>Cidade</th>
        <td style="text-align: left"><?= $arrEvento["nm_cidade"] ?? "" ?></td>
      </tr>
      <tr>
        <th>Data</th>
        <td style="text-align: left"><?= $arrEvento["dt_evento"] ?? "" ?></td>
        <th>Modalidades</th>
        <td style="text-align: left">
          <select name="cd_modalidade" id="cd_modalidade">
            <option value=""></option>
            <?php foreach ($arrModalidades as $modal): ?>
              <option value="<?= $modal["value"] ?>" <?= ($cdModalidadeSel == $modal["value"]) ? "selected" : "" ?>><?= $modal["description"] ?></option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>Contato</th>
        <td style="text-align: left"><input type="text" name="ds_contato" id="ds_contato" size="25" minlength="2" value="<?= $arrEvento["ds_contato"] ?? "" ?>"></td>
        <th>Equipe</th>
        <td style="text-align: left"><input type="text" name="ds_equipe" id="ds_equipe" size="25" minlength="2" value="<?= $arrEvento["ds_equipe"] ?? "" ?>" oninput="validateInput(this)"></td>
      </tr>
      <tr>
        <th>Ação:</th>
        <td colspan="4">
          <?php if ($idEdicao): ?>
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
  <p><a href="sel_evento.php">Listagem de Eventos</a> | <a href="index.php">Voltar ao Início</a></p>
</div>
</body>
<?php include("footer.html");?>
</html>
