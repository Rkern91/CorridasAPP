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

  $dsOperacao   = $_REQUEST["id_operacao"] ?? "";
  $tituloPagina = "Usuários";
  require("header.php");
?>
  <div class="container">
    <h3>Listagem de Usuários</h3>
    <?php if ($dsOperacao): ?>
      <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
    <?php endif; ?>
    <?php if (empty($arrUsuarios)): ?>
      <p class="muted">Nenhum usuário cadastrado.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Cód.</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Tipo</th>
        </tr>
        <?php foreach ($arrUsuarios as $usuario): ?>
          <tr>
            <td style="text-align: center"><?= h($usuario["cd_pessoa"]) ?></td>
            <td><?= h($usuario["nm_pessoa"]) ?></td>
            <td><?= h($usuario["ds_email"]) ?></td>
            <td><?= h($usuario["tipo_usuario"]) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
    <p><a href="man_usuario.php">Adicionar Usuário</a></p>
  </div>
<?php require("footer.php"); ?>
