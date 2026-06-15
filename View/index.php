<?php
  require_once("../auth_guard.php");

  $dsOperacao   = $_REQUEST["id_operacao"] ?? "";
  $isAdmin      = (($_SESSION["id_tipo_usuario"] ?? null) == 1);
  $tituloPagina = "Início";
  require("header.php");
?>
  <?php if ($dsOperacao): ?>
    <input type="hidden" id="ds_operacao" value="<?= h($dsOperacao) ?>">
  <?php endif; ?>
  <div class="container">
    <h3>Bem-vindo ao CorridasAPP</h3>
    <p class="muted">
      <?php if ($isAdmin): ?>
        Área administrativa — gerencie eventos, modalidades e cidades pelo menu à esquerda.
      <?php else: ?>
        Use o menu à esquerda para ver eventos, gerenciar suas inscrições e seus dados.
      <?php endif; ?>
    </p>
  </div>
<?php require("footer.php"); ?>
