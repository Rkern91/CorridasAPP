<?php
  require_once("../session.php");
  require_once("../Controllers/LoginController.php");

  $dsErroLogin = "";

  if (isset($_POST["ds_email"]))
  {
    try
    {
      $LoginController = new LoginController();

      if ($LoginController->realizarLoginUsuario())
      {
        header("Location: index.php?id_operacao=login");
        exit;
      }

      $dsErroLogin = "Usuário ou senha incorretos.";
    }
    catch (Exception $e)
    {
      $error_message = "Erro ao realizar login. DETALHES: " . $e->getMessage();
      header("Location: erro.php?dsOrigem=login&dsMensagem=" . urlencode($error_message));
      exit;
    }
  }

  $dsOperacao    = $_REQUEST["id_operacao"] ?? "";
  $tituloPagina  = "Entrar";
  $layoutSidebar = false;
  require("header.php");
?>
  <?php if ($dsOperacao): ?>
    <input type="hidden" id="ds_operacao" value="<?= $dsOperacao ?>">
  <?php endif; ?>
  <?php if ($dsErroLogin): ?>
    <div class="alert alert-danger"><?= $dsErroLogin ?></div>
  <?php endif; ?>
  <form method="post" name="form" id="form">
    <input type="hidden" name="tabela" id="id_tabela" value="login">
    <input type="hidden" name="tela"   id="id_tela"   value="login">
    <table>
      <tr>
        <th>Email</th>
        <td><input type="email" placeholder="Email" name="ds_email" id="ds_email" size="30" minlength="2"></td>
      </tr>
      <tr>
        <th>Senha</th>
        <td><input type="password" placeholder="Senha" name="ds_senha" id="ds_senha"></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center"><input type="submit" name="btn_submit" id="btn_submit" value="Entrar"></td>
      </tr>
    </table>
    <p class="muted"><a href="man_cadastro_usuario.php"><strong>Cadastre-se</strong> agora mesmo!</a></p>
  </form>
<?php require("footer.php"); ?>
