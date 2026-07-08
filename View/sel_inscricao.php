<?php
  require_once("../auth_guard.php");
  require_once("../Controllers/InscricaoController.php");

  try
  {
    $InscricaoController = new InscricaoController();
    $arrInscricoes       = $InscricaoController->obterListagemInscricoes();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=inscricao&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $dsOperacao    = $_REQUEST["id_operacao"] ?? "";
  $cdPessoa      = $_SESSION["cd_pessoa"] ?? "";
  $tituloPagina  = "Minhas Inscrições";
  $layoutModerno = true;
  require("header.php");
?>
  <div class="page">
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <div class="page-head">
      <div>
        <h2 class="page-title">Minhas Inscrições</h2>
        <p class="page-sub">Acompanhe as suas inscrições em eventos</p>
      </div>
      <a class="link-action" href="sel_evento.php">
        Ver Eventos
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8.59 16.59 13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
      </a>
    </div>
    <div class="panel">
      <?php if (empty($arrInscricoes)): ?>
        <p class="empty-state">Você ainda não possui inscrições. <a href="sel_evento.php">Ver eventos disponíveis</a></p>
      <?php else: ?>
        <table class="table-modern">
          <tr>
            <th>Evento</th>
            <th class="t-center">Data</th>
            <th>Cidade</th>
            <th>Equipe</th>
            <th>Contato</th>
            <th class="t-center">Distância (KM)</th>
            <th class="t-center">Ações</th>
          </tr>
          <?php foreach ($arrInscricoes as $inscricao): ?>
            <tr>
              <td><b><?= h($inscricao["nm_evento"]) ?></b></td>
              <td class="t-center"><?= h($inscricao["dt_evento"]) ?></td>
              <td><?= h($inscricao["nm_cidade"]) ?></td>
              <td><?= h($inscricao["ds_equipe"]) ?></td>
              <td><?= h($inscricao["ds_contato"]) ?></td>
              <td class="t-center"><?= h($inscricao["ds_descricao"]) ?></td>
              <td class="t-center">
                <a class="link-action" href="man_inscricao.php?cd_evento=<?= h($inscricao["cd_evento"]) ?>&cd_pessoa=<?= h($cdPessoa) ?>">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                  Alterar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php require("footer.php"); ?>
