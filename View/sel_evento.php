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
  require("header.php");
?>
  <div class="container">
    <h3>Listagem de Eventos</h3>
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <?php if (empty($arrEventos)): ?>
      <p class="muted">Nenhum evento <?= $idUsuarioComum ? "disponível no momento" : "cadastrado" ?>.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Cód.</th>
          <th>Evento</th>
          <th>Data</th>
          <th>Cidade</th>
          <th>Modalidades (KMs)</th>
          <th>-</th>
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
            <td style="text-align: center"><?= h($evento["cd_evento"]) ?></td>
            <td><?= h($evento["nm_evento"]) ?></td>
            <td style="text-align: center"><?= h($evento["dt_evento"]) ?></td>
            <td><?= h($evento["nm_cidade"]) ?></td>
            <td style="text-align: center" title="<?= h($dsTipModalidade) ?>"><?= h($evento["ds_modalidades"]) ?></td>
            <td style="text-align: center">
              <?php if ($idUsuarioComum): ?>
                <a href="man_inscricao.php?cd_evento=<?= h($evento["cd_evento"]) ?>&cd_pessoa=<?= h($cdPessoa) ?>">Inscrever-se!</a>
              <?php else: ?>
                <a href="man_evento.php?cd_evento=<?= h($evento["cd_evento"]) ?>">Editar</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
    <?php if (!$idUsuarioComum): ?>
      <p><a href="man_evento.php">Adicionar Evento</a></p>
    <?php endif; ?>
  </div>
<?php require("footer.php"); ?>
