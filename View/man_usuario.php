<?php
  require_once("../admin_guard.php");
  require_once("../Controllers/CadastroUsuarioController.php");

  try
  {
    $CadastroUsuarioController = new CadastroUsuarioController();
    $arrCidades                = $CadastroUsuarioController->obterCidades();
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=usuario&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $arrOpcoesSexo = ["F" => "Feminino", "M" => "Masculino"];

  $tituloPagina = "Cadastro de Usuário";
  require("header.php");
?>
  <div class="container">
    <h3>Cadastro de Usuário</h3>
    <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
      <input type="hidden" name="tabela" id="id_tabela" value="usuario">
      <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
      <table class="ficha">
        <tr>
          <th>Nome</th>
          <td style="text-align: left"><input type="text" name="nm_pessoa" id="nm_pessoa" size="30" minlength="2" oninput="validateInput(this)"></td>
          <th>Dt. Nascimento</th>
          <td style="text-align: left"><input type="date" name="dt_nascimento" id="dt_nascimento"></td>
        </tr>
        <tr>
          <th>Senha</th>
          <td style="text-align: left"><input type="password" name="ds_senha" id="ds_senha" size="20" minlength="5"></td>
          <th>Email</th>
          <td style="text-align: left"><input type="email" name="ds_email" id="ds_email"></td>
        </tr>
        <tr>
          <th>Telefone</th>
          <td style="text-align: left"><input type="text" name="nr_telefone" id="nr_telefone" minlength="11" maxlength="11" oninput="validateInput(this)"></td>
          <th>Cidade</th>
          <td style="text-align: left">
            <select name="cd_cidade" id="cd_cidade">
              <option value=""></option>
              <?php foreach ($arrCidades as $cidade): ?>
                <option value="<?= h($cidade["value"]) ?>"><?= h($cidade["description"]) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th>Sexo</th>
          <td colspan="3" style="text-align: left">
            <select name="ds_sexo" id="ds_sexo">
              <option value=""></option>
              <?php foreach ($arrOpcoesSexo as $value => $descricao): ?>
                <option value="<?= h($value) ?>"><?= h($descricao) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th>Ação:</th>
          <td colspan="3">
            <label><input type="radio" name="f_action" id="f_action" value="inserir" checked>Inserir</label>
          </td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: center">
            <input type="submit" name="btn_submit" id="btn_submit" value="Confirmar">
          </td>
        </tr>
      </table>
    </form>
    <p><a href="sel_usuario.php">Listagem de Usuários</a></p>
  </div>
<?php require("footer.php"); ?>
