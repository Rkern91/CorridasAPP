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

  $dsOperacao   = $_REQUEST["id_operacao"] ?? "";
  $cdPessoa     = $_SESSION["cd_pessoa"] ?? "";
  $tituloPagina = "Minhas Inscrições";
  require("header.php");
?>
  <div class="container">
    <?php if (empty($arrInscricoes)): ?>
      <h3>Minhas Inscrições</h3>
      <p class="muted">Você ainda não possui inscrições.</p>
      <p><a href="sel_evento.php">Ver Eventos</a></p>
    <?php else: ?>
      <h3>Listagem de Inscrições</h3>
      <?php if ($dsOperacao): ?>
        <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
        <input type="hidden" id="ds_origem"   value="inscricao">
      <?php endif; ?>
      <table>
        <tr>
          <th>Evento</th>
          <th>Data</th>
          <th>Cidade</th>
          <th>Equipe</th>
          <th>Contato</th>
          <th>Distância (KM)</th>
          <th>-</th>
        </tr>
        <?php foreach ($arrInscricoes as $inscricao): ?>
          <tr>
            <td><?= h($inscricao["nm_evento"]) ?></td>
            <td><?= h($inscricao["dt_evento"]) ?></td>
            <td><?= h($inscricao["nm_cidade"]) ?></td>
            <td><?= h($inscricao["ds_equipe"]) ?></td>
            <td><?= h($inscricao["ds_contato"]) ?></td>
            <td><?= h($inscricao["ds_descricao"]) ?></td>
            <td style="text-align: center"><a href="man_inscricao.php?cd_evento=<?= h($inscricao["cd_evento"]) ?>&cd_pessoa=<?= h($cdPessoa) ?>">Alterar Inscrição</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <p><a href="sel_evento.php">Listagem de Eventos</a></p>
    <?php endif; ?>
  </div>
<?php require("footer.php"); ?>
