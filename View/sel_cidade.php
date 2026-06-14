<?php
  require_once("../auth_guard.php");
  require_once("../Controllers/CidadeController.php");

  try
  {
    $CidadeController = new CidadeController();
    $arrCidades       = $CidadeController->obterListagemCidades();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=cidade&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $dsOperacao   = $_REQUEST["id_operacao"] ?? "";
  $tituloPagina = "Cidades";
  require("header.php");
?>
  <?php if (empty($arrCidades)): ?>
    <input type="hidden" id="ds_operacao" value="cadastrar">
    <input type="hidden" id="ds_origem"   value="cidade">
  <?php else: ?>
    <div class="container">
      <h3>Listagem de Cidades</h3>
      <?php if ($dsOperacao): ?>
        <input type="hidden" id="ds_operacao" value="<?= $dsOperacao ?>">
        <input type="hidden" id="ds_origem"   value="cidade">
      <?php endif; ?>
      <table>
        <tr>
          <th>Cód.</th>
          <th>Cidade</th>
          <th>-</th>
        </tr>
        <?php foreach ($arrCidades as $cidade): ?>
          <tr>
            <td style="text-align: center"><?= $cidade["cd_cidade"] ?></td>
            <td><?= $cidade["nm_cidade"] ?></td>
            <td style="text-align: center"><a href="man_cidade.php?cd_cidade=<?= $cidade["cd_cidade"] ?>">Editar</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <p><a href="man_cidade.php">Adicionar Cidade</a></p>
    </div>
  <?php endif; ?>
<?php require("footer.php"); ?>
