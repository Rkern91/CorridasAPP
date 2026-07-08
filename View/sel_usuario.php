<?php
  require_once("../admin_guard.php");
  require_once("../Controllers/CadastroUsuarioController.php");

  try
  {
    $CadastroUsuarioController = new CadastroUsuarioController();
    $arrUsuarios                = $CadastroUsuarioController->obterListagemUsuarios();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=usuario&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $dsOperacao    = $_REQUEST["id_operacao"] ?? "";
  $tituloPagina  = "Usuários";
  $layoutModerno = true;
  require("header.php");
?>
  <div class="page">
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <div class="page-head">
      <div>
        <h2 class="page-title">Usuários</h2>
        <p class="page-sub">Usuários cadastrados no sistema</p>
      </div>
      <a class="btn" href="man_usuario.php">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5h2v6h6v2h-6v6h-2v-6H5v-2h6V5z"/></svg>
        Adicionar Usuário
      </a>
    </div>
    <div class="panel">
      <?php if (empty($arrUsuarios)): ?>
        <p class="empty-state">Nenhum usuário cadastrado.</p>
      <?php else: ?>
        <table class="table-modern">
          <tr>
            <th class="t-center">Cód.</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
          </tr>
          <?php foreach ($arrUsuarios as $usuario): ?>
            <tr>
              <td class="t-center"><?= h($usuario["cd_pessoa"]) ?></td>
              <td><b><?= h($usuario["nm_pessoa"]) ?></b></td>
              <td><?= h($usuario["ds_email"]) ?></td>
              <td><?= h($usuario["tipo_usuario"]) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php require("footer.php"); ?>
