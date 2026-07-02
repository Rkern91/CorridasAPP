<?php
  require_once("../session.php");
  require_once("../Controllers/CadastroUsuarioController.php");

  try
  {
    $CadastroUsuarioController = new CadastroUsuarioController();
    $arrCidades                = $CadastroUsuarioController->obterCidades();

    $idEdicao  = isset($_SESSION["cd_pessoa"]);
    $arrPessoa = $idEdicao ? $CadastroUsuarioController->obterDadosPessoa() : [];
  }
  catch (Exception $e)
  {
    $error_message = "Erro ao obter dados do formulário! DETALHES: " . $e->getMessage();
    header("Location: erro.php?dsOrigem=cadastroUsuario&dsMensagem=" . urlencode($error_message));
    exit;
  }

  $nmPessoa     = $arrPessoa["nm_pessoa"]     ?? "";
  $nrTelefone   = $arrPessoa["nr_telefone"]   ?? "";
  $dtNascimento = $arrPessoa["dt_nascimento"] ?? "";
  $dsEmail      = $arrPessoa["ds_email"]      ?? "";
  $dsSexo       = $arrPessoa["ds_sexo"]       ?? "";
  $cdCidade     = $arrPessoa["cd_cidade"]     ?? "";

  $arrOpcoesSexo = ["F" => "Feminino", "M" => "Masculino"];

  $layoutSidebar = $idEdicao;
  $layoutLargo   = true;
  $tituloPagina  = $idEdicao ? "Meu Cadastro" : "Cadastro";
  require("header.php");
?>
  <?php if ($idEdicao): ?><div class="container"><?php endif; ?>
    <h3>Cadastro de Usuário</h3>
    <form action="../Controllers/ProcessActionFormController.php" id="form" method="post">
      <?php if ($idEdicao): ?>
        <input type="hidden" name="cd_pessoa" value="<?= h($_SESSION["cd_pessoa"]) ?>">
      <?php endif; ?>
      <input type="hidden" name="tabela" id="id_tabela" value="pessoa">
      <input type="hidden" name="tela"   id="id_tela"   value="manutencao">
      <table class="ficha">
        <tr>
          <th>Nome</th>
          <td style="text-align: left"><input type="text" name="nm_pessoa" id="nm_pessoa" size="30" minlength="2" value="<?= h($nmPessoa) ?>" oninput="validateInput(this)"></td>
          <th>Dt. Nascimento</th>
          <td style="text-align: left"><input type="date" name="dt_nascimento" id="dt_nascimento" value="<?= h($dtNascimento) ?>"></td>
        </tr>
        <tr>
          <th>Senha</th>
          <td style="text-align: left"><input type="password" name="ds_senha" id="ds_senha" size="20" minlength="5" value="" placeholder="<?= $idEdicao ? "Deixe em branco para manter" : "" ?>"></td>
          <th>Email</th>
          <td style="text-align: left"><input type="email" name="ds_email" id="ds_email" value="<?= h($dsEmail) ?>"></td>
        </tr>
        <tr>
          <th>Telefone</th>
          <td style="text-align: left"><input type="text" name="nr_telefone" id="nr_telefone" minlength="11" maxlength="11" value="<?= h($nrTelefone) ?>" oninput="validateInput(this)"></td>
          <th>Cidade</th>
          <td style="text-align: left">
            <select name="cd_cidade" id="cd_cidade">
              <option value=""></option>
              <?php foreach ($arrCidades as $cidade): ?>
                <option value="<?= h($cidade["value"]) ?>" <?= ($cdCidade == $cidade["value"]) ? "selected" : "" ?>><?= h($cidade["description"]) ?></option>
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
                <option value="<?= h($value) ?>" <?= ($dsSexo == $value) ? "selected" : "" ?>><?= h($descricao) ?></option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <tr>
          <th>Ação:</th>
          <td colspan="3">
            <?php if ($idEdicao): ?>
              <label><input class="f_action" type="radio" name="f_action" value="atualizar" checked>Alterar</label>
              <label><input class="f_action" type="radio" name="f_action" value="deletar">Excluir</label>
            <?php else: ?>
              <label><input type="radio" name="f_action" id="f_action" value="inserir" checked>Inserir</label>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: center">
            <input type="submit" name="btn_submit" id="btn_submit" value="Confirmar">
          </td>
        </tr>
      </table>
    </form>
    <?php if (!$idEdicao): ?>
      <p class="muted"><a href="login.php">Voltar p/ Login</a></p>
    <?php endif; ?>
  <?php if ($idEdicao): ?></div><?php endif; ?>
<?php require("footer.php"); ?>
