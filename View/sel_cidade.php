<?php
  require_once("../admin_guard.php");
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
  <div class="container">
    <h3>Listagem de Cidades</h3>
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <?php if (empty($arrCidades)): ?>
      <p class="muted">Nenhuma cidade cadastrada.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Cód.</th>
          <th>Cidade</th>
          <th>-</th>
        </tr>
        <?php foreach ($arrCidades as $cidade): ?>
          <tr>
            <td style="text-align: center"><?= h($cidade["cd_cidade"]) ?></td>
            <td><?= h($cidade["nm_cidade"]) ?></td>
            <td style="text-align: center"><a href="man_cidade.php?cd_cidade=<?= h($cidade["cd_cidade"]) ?>">Editar</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
    <p><a href="man_cidade.php">Adicionar Cidade</a></p>
  </div>
<?php require("footer.php"); ?>
