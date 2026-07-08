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
    <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
  <?php endif; ?>
  <p class="auth-subtitle">Acesse sua conta para continuar</p>
  <?php if ($dsErroLogin): ?>
    <div class="alert alert-danger"><?= h($dsErroLogin) ?></div>
  <?php endif; ?>
  <form method="post" name="form" id="form" class="auth-form">
    <input type="hidden" name="tabela" id="id_tabela" value="login">
    <input type="hidden" name="tela"   id="id_tela"   value="login">
    <div class="field">
      <label for="ds_email">E-mail</label>
      <input type="email" placeholder="voce@exemplo.com" name="ds_email" id="ds_email" minlength="2" required autofocus>
    </div>
    <div class="field">
      <label for="ds_senha">Senha</label>
      <input type="password" placeholder="Sua senha" name="ds_senha" id="ds_senha" required>
    </div>
    <input type="submit" name="btn_submit" id="btn_submit" class="btn-block" value="Entrar">
    <p class="auth-alt">Não tem uma conta? <a href="man_cadastro_usuario.php">Cadastre-se</a></p>
  </form>
  <div class="social-links">
    <a href="#" aria-label="Instagram" title="Instagram">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
    </a>
    <a href="#" aria-label="Facebook" title="Facebook">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
    </a>
    <a href="#" aria-label="X" title="X">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932 6.064-6.933zm-1.291 19.491h2.039L6.486 3.24H4.298l13.312 17.404z"/></svg>
    </a>
    <a href="#" aria-label="YouTube" title="YouTube">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
    </a>
  </div>
<?php require("footer.php"); ?>
