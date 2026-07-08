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

  $dsOperacao    = $_REQUEST["id_operacao"] ?? "";
  $tituloPagina  = "Modalidades";
  $layoutModerno = true;
  require("header.php");
?>
  <div class="page">
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <div class="page-head">
      <div>
        <h2 class="page-title">Modalidades</h2>
        <p class="page-sub">Gerencie as modalidades e distâncias dos eventos</p>
      </div>
      <a class="btn" href="man_modalidade.php">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
        Adicionar Modalidade
      </a>
    </div>
    <div class="panel">
      <?php if (empty($arrModalidades)): ?>
        <p class="empty-state">Nenhuma modalidade cadastrada.</p>
      <?php else: ?>
        <table class="table-modern">
          <tr>
            <th class="t-center">Cód.</th>
            <th>Modalidade</th>
            <th class="t-center">Data/Hora</th>
            <th class="t-center">Distância (KM)</th>
            <th class="t-center">Vl. Inscrição</th>
            <th class="t-center">Ações</th>
          </tr>
          <?php foreach ($arrModalidades as $modalidade): ?>
            <tr>
              <td class="t-center"><?= h($modalidade["cd_modalidade"]) ?></td>
              <td><b><?= h($modalidade["ds_descricao"]) ?></b></td>
              <td class="t-center"><?= h($modalidade["dt_largada_modalidade"]) ?></td>
              <td class="t-center"><?= h($modalidade["vl_km_distancia"]) ?></td>
              <td class="t-center">R$ <?= h(padronizaMoeda($modalidade["vl_valor"], 2, "sys", "pt_BR")) ?></td>
              <td class="t-center">
                <a class="link-action" href="man_modalidade.php?cd_modalidade=<?= h($modalidade["cd_modalidade"]) ?>">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                  Editar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php require("footer.php"); ?>
