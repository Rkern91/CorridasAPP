<?php
  require_once("../Controllers/LoginController.php");
?>
<!DOCTYPE HTML>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="../style.css">
    <title>Corridas APP | Login</title>
  </head>
  <body>
  <div class="container">
      <h3>CorridasAPP</h3>
      <form method="post">
        <input type="hidden" name="tabela" id="id_tabela" value="login">
        <table>
          <tr>
            <th>Email</th>
            <td colspan="3" style="text-align: left"><input type="email" placeholder="Email" name="ds_email" id="ds_email" size="40" minlength="2""></td>
          </tr>
          <tr>
            <th>Senha</th>
            <td><input type="password" placeholder="Senha" name="ds_senha" id="ds_senha"></td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: center"><input type="submit" name=btn_submit id="btn_submit" value="Entrar"></td>
          </tr>
        </table>
       <p><a href="registro.php" class="text-center"><strong>Cadastre-se</strong> agora mesmo!</a></p>
      </form>
    </div>
  </body>
<?php
  try
  {
    if (isset($_POST["ds_email"]))
      $LoginController = new LoginController();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário. DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=login&dsMensagem=" . urlencode($error_message));
    exit;
  }
?>
</html>

