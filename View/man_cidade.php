<?php
  require_once("../auth_guard.php");
  require_once("../Controllers/CidadeController.php");

  try
  {
    $CidadeController = new CidadeController();
    $arrEstados       = $CidadeController->obterEstados();

    $cdCidade  = $_REQUEST["cd_cidade"] ?? "";
    $arrCidade = [];
    $cdUf      = "";

    if ($cdCidade !== "")
    {
      $arrCidade = $CidadeController->obterCidade();
      $cdUf      = $arrCidade["cd_uf"] ?? "";
    }
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=cidade&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $tituloPagina = "Manutenção de Cidade";
  require("header.php");
?>
  <div class="container">
    <h3>Manutenção de Cidade</h3>
    <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
      <?php if ($cdCidade !== ""): ?>
        <input type="hidden" name="cd_cidade" value="<?= $arrCidade["cd_cidade"] ?>">
      <?php endif; ?>
      <input type="hidden" name="tabela" id="id_tabela" value="cidade">
      <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
      <table>
        <tr>
          <th>Cidade</th>
          <td colspan="3" style="text-align: left"><input type="text" name="nm_cidade" id="nm_cidade" size="40" minlength="2" value="<?= $arrCidade["nm_cidade"] ?? "" ?>" oninput="validateInput(this)"></td>
        </tr>
        <tr>
          <th>Estado</th>
          <td colspan="3" style="text-align: left">
            <select name="cd_uf" id="cd_uf">
              <option value=""></option>
              <?php foreach ($arrEstados as $uf): ?>
                <option value="<?= $uf["value"] ?>" <?= ($cdUf == $uf["value"]) ? "selected" : "" ?>><?= $uf["description"] ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th>Ação:</th>
          <td colspan="3">
            <?php if ($cdCidade !== ""): ?>
              <label><input class="f_action" type="radio" name="f_action" value="atualizar" checked>Alterar</label>
              <label><input class="f_action" type="radio" name="f_action" value="deletar">Excluir</label>
            <?php else: ?>
              <label><input type="radio" name="f_action" id="f_action" value="inserir" checked>Inserir</label>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center">
            <input type="submit" name="btn_submit" id="btn_submit" value="Confirmar">
          </td>
        </tr>
      </table>
    </form>
    <p><a href="sel_cidade.php">Listagem de Cidades</a></p>
  </div>
<?php require("footer.php"); ?>
