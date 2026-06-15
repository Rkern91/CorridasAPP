<?php
  require_once("../admin_guard.php");
  require_once("../Controllers/ModalidadeController.php");

  try
  {
    $ModalidadeController = new ModalidadeController();
    $arrModalidades       = $ModalidadeController->obterListagemModalidades();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=modalidade&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $dsOperacao   = $_REQUEST["id_operacao"] ?? "";
  $tituloPagina = "Modalidades";
  require("header.php");
?>
  <div class="container">
    <h3>Listagem de Modalidades</h3>
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <?php if (empty($arrModalidades)): ?>
      <p class="muted">Nenhuma modalidade cadastrada.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Cód.</th>
          <th>Modalidade</th>
          <th>Data/Hora</th>
          <th>Distância (KM)</th>
          <th>Vl. Inscrição</th>
          <th>-</th>
        </tr>
        <?php foreach ($arrModalidades as $modalidade): ?>
          <tr>
            <td style="text-align: center"><?= h($modalidade["cd_modalidade"]) ?></td>
            <td><?= h($modalidade["ds_descricao"]) ?></td>
            <td style="text-align: center"><?= h($modalidade["dt_largada_modalidade"]) ?></td>
            <td style="text-align: center"><?= h($modalidade["vl_km_distancia"]) ?></td>
            <td style="text-align: center">R$ <?= h(padronizaMoeda($modalidade["vl_valor"], 2, "sys", "pt_BR")) ?></td>
            <td style="text-align: center"><a href="man_modalidade.php?cd_modalidade=<?= h($modalidade["cd_modalidade"]) ?>">Editar</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
    <p><a href="man_modalidade.php">Adicionar Modalidade</a></p>
  </div>
<?php require("footer.php"); ?>
