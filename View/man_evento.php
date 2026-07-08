<?php
  require_once("../admin_guard.php");
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

  $idEdicao      = ($cdEvento !== "");
  $tituloPagina  = $idEdicao ? "Editar Evento" : "Adicionar Evento";
  $layoutModerno = true;
  require("header.php");
?>
  <div class="page">
    <div class="page-head">
      <div>
        <h2 class="page-title"><?= $idEdicao ? "Editar Evento" : "Adicionar Evento" ?></h2>
        <p class="page-sub"><?= $idEdicao ? "Altere os dados do evento ou exclua o registro" : "Preencha os dados para cadastrar um novo evento" ?></p>
      </div>
    </div>
    <div class="panel panel-form">
      <form action="../Controllers/ProcessActionFormController.php" id="form" method="post" class="form-modern">
        <?php if ($idEdicao): ?>
          <input type="hidden" name="cd_evento" value="<?= h($arrEvento["cd_evento"]) ?>">
        <?php endif; ?>
        <input type="hidden" name="tabela"             id="id_tabela"          value="evento">
        <input type="hidden" name="tela"               id="id_tela"            value="manutencao">
        <input type="hidden" name="arr_cd_modalidades" id="arr_cd_modalidades" value="<?= h($dsCdsModal) ?>">
        <input type="hidden" name="qt_modalidades"     id="qt_modalidades"     value="<?= h($qtModal) ?>">
        <div class="field">
          <label for="nm_evento">Nome</label>
          <input type="text" name="nm_evento" id="nm_evento" minlength="2" placeholder="Nome do evento" value="<?= h($dsNome) ?>" oninput="validateInput(this)">
        </div>
        <div class="field">
          <label for="cd_cidade">Cidade</label>
          <select name="cd_cidade" id="cd_cidade">
            <option value=""></option>
            <?php foreach ($arrCidades as $cidade): ?>
              <option value="<?= h($cidade["value"]) ?>" <?= ($cdCidade == $cidade["value"]) ? "selected" : "" ?>><?= h($cidade["description"]) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field field-row">
          <div>
            <label for="dt_evento">Data</label>
            <input type="date" name="dt_evento" id="dt_evento" value="<?= h($dsData) ?>">
          </div>
          <div>
            <label for="hr_evento">Hora</label>
            <input type="time" name="hr_evento" id="hr_evento" value="<?= h($dsHora) ?>">
          </div>
        </div>
        <div class="field">
          <label for="op_id_modalidades">Modalidades</label>
          <div class="field-group">
            <select id="op_id_modalidades">
              <?php foreach ($arrModalidades as $modal): ?>
                <option value="<?= h($modal["value"]) ?>"><?= h($modal["description"]) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="icon-btn" id="add" title="Adicionar Modalidade" aria-label="Adicionar Modalidade" onclick="alterarModalidadesSelecionadas('add')">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
            </button>
            <button type="button" class="icon-btn" id="rem" title="Remover Modalidade" aria-label="Remover Modalidade" onclick="alterarModalidadesSelecionadas('rem')">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 11h14v2H5z"/></svg>
            </button>
          </div>
          <p class="field-hint">Distâncias (km) das modalidades selecionadas:</p>
          <input type="text" id="dsModals" value="<?= h($dsKmModal) ?>" readonly>
        </div>
        <div class="field">
          <label>Ação</label>
          <div class="radio-group">
            <?php if ($idEdicao): ?>
              <label><input class="f_action" type="radio" name="f_action" value="atualizar" checked>Alterar</label>
              <label class="opt-danger"><input class="f_action" type="radio" name="f_action" value="deletar">Excluir</label>
            <?php else: ?>
              <label><input type="radio" name="f_action" id="f_action" value="inserir" checked>Inserir</label>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-foot">
          <a class="link-action" href="sel_evento.php">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            Listagem de Eventos
          </a>
          <input type="submit" name="btn_submit" id="btn_submit" value="Confirmar">
        </div>
      </form>
    </div>
  </div>
<?php require("footer.php"); ?>
