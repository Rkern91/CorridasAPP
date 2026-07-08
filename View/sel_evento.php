<?php
  require_once("../auth_guard.php");
  require_once("../Controllers/EventoController.php");

  try
  {
    $EventoController = new EventoController();
    $arrEventos       = $EventoController->obterListagemEventos();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=evento&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $dsOperacao     = $_REQUEST["id_operacao"] ?? "";
  $cdPessoa       = $_SESSION["cd_pessoa"] ?? "";
  $idUsuarioComum = isset($_SESSION["id_tipo_usuario"]) && $_SESSION["id_tipo_usuario"] == 2;
  $tituloPagina   = "Eventos";
  $layoutModerno  = true;
  require("header.php");
?>
  <div class="page">
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <div class="page-head">
      <div>
        <h2 class="page-title">Eventos</h2>
        <p class="page-sub"><?= $idUsuarioComum ? "Escolha um evento e faça sua inscrição" : "Gerencie os eventos de corrida do sistema" ?></p>
      </div>
      <?php if (!$idUsuarioComum): ?>
        <a class="btn" href="man_evento.php">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
          Adicionar Evento
        </a>
      <?php endif; ?>
    </div>
    <div class="panel">
      <?php if (empty($arrEventos)): ?>
        <p class="empty-state">Nenhum evento <?= $idUsuarioComum ? "disponível no momento" : "cadastrado" ?>.</p>
      <?php else: ?>
        <table class="table-modern">
          <tr>
            <th class="t-center">Cód.</th>
            <th>Evento</th>
            <th class="t-center">Data</th>
            <th>Cidade</th>
            <th class="t-center">Modalidades (KMs)</th>
            <th class="t-center">Ações</th>
          </tr>
          <?php foreach ($arrEventos as $evento): ?>
            <?php
              // Monta o tooltip com as descrições das modalidades do evento.
              $dsTipModalidade = "";

              if (isset($evento["ds_descricacao"]))
              {
                $dsModalidades = explode(",", str_replace("\"", "", trim($evento["ds_descricacao"], "{}")));

                foreach ($dsModalidades as $modal)
                  $dsTipModalidade .= "$modal\n";
              }
            ?>
            <tr>
              <td class="t-center"><?= h($evento["cd_evento"]) ?></td>
              <td><b><?= h($evento["nm_evento"]) ?></b></td>
              <td class="t-center"><?= h($evento["dt_evento"]) ?></td>
              <td><?= h($evento["nm_cidade"]) ?></td>
              <td class="t-center" title="<?= h($dsTipModalidade) ?>"><?= h($evento["ds_modalidades"]) ?></td>
              <td class="t-center">
                <?php if ($idUsuarioComum): ?>
                  <a class="link-action" href="man_inscricao.php?cd_evento=<?= h($evento["cd_evento"]) ?>&cd_pessoa=<?= h($cdPessoa) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4zM6 10V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    Inscrever-se
                  </a>
                <?php else: ?>
                  <a class="link-action" href="man_evento.php?cd_evento=<?= h($evento["cd_evento"]) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    Editar
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php require("footer.php"); ?>
