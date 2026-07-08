<?php
  // Cabeçalho/layout reutilizável.
  // Variáveis aceitas (definidas pela View antes do require):
  //   $tituloPagina  (string) - título exibido no <title>
  //   $layoutSidebar (bool)   - true = layout interno com menu lateral (padrão)
  //                             false = layout centralizado (login/cadastro)
  //   $layoutLargo   (bool)   - cartão largo no layout centralizado
  //   $layoutModerno (bool)   - true = conteúdo no Tema Padrão (cards sobre
  //                             fundo cinza); false = tema legado (padrão)
  require_once(__DIR__ . "/../session.php");
  require_once(__DIR__ . "/../helpers.inc.php");

  $tituloPagina  = $tituloPagina  ?? "CorridasAPP";
  $layoutSidebar = $layoutSidebar ?? true;
  $layoutModerno = $layoutModerno ?? false;

  if ($layoutSidebar)
  {
    $cdPessoa = $_SESSION["cd_pessoa"] ?? "";
    $isAdmin  = (($_SESSION["id_tipo_usuario"] ?? null) == 1);
    $atual    = basename($_SERVER["PHP_SELF"]);

    // [rótulo, href, [páginas que deixam o item ativo]]
    $itensMenu = $isAdmin
      ? [
          ["Eventos",      "sel_evento.php",                                 ["sel_evento.php", "man_evento.php"]],
          ["Modalidades",  "sel_modalidade.php",                             ["sel_modalidade.php", "man_modalidade.php"]],
          ["Cidades",      "sel_cidade.php",                                 ["sel_cidade.php", "man_cidade.php"]],
          ["Usuários",     "sel_usuario.php",                                ["sel_usuario.php", "man_usuario.php"]],
          ["Meu Cadastro", "man_cadastro_usuario.php?cd_pessoa={$cdPessoa}", ["man_cadastro_usuario.php"]],
          ["Extrato",      "con_dados_usuario.php?cd_pessoa={$cdPessoa}",    ["con_dados_usuario.php"]]
        ]
      : [
          ["Eventos",           "sel_evento.php",                                 ["sel_evento.php", "man_inscricao.php"]],
          ["Minhas Inscrições", "sel_inscricao.php",                              ["sel_inscricao.php"]],
          ["Meu Cadastro",      "man_cadastro_usuario.php?cd_pessoa={$cdPessoa}", ["man_cadastro_usuario.php"]],
          ["Extrato",           "con_dados_usuario.php?cd_pessoa={$cdPessoa}",    ["con_dados_usuario.php"]]
        ];

    // Caminho de migalhas: Início » seção ativa (deduzido do menu)
    $dsSecaoAtiva = "";

    foreach ($itensMenu as $item)
    {
      if (in_array($atual, $item[2]))
      {
        $dsSecaoAtiva = $item[0];
        break;
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CorridasAPP | <?= $tituloPagina ?></title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php if ($layoutSidebar): ?>
  <div class="app">
    <aside class="sidebar">
      <div class="brand"><span class="brand-mark">&#9656;</span> CorridasAPP</div>
      <nav class="nav">
        <?php foreach ($itensMenu as $item): ?>
          <?php [$rotulo, $href, $ativos] = $item; ?>
          <a class="nav-link<?= in_array($atual, $ativos) ? " active" : "" ?>" href="<?= $href ?>"><?= $rotulo ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="sidebar-footer">
        <a class="nav-link logout" href="logout.php?cd_pessoa=<?= $cdPessoa ?>">Sair</a>
      </div>
    </aside>
    <div class="main">
      <div class="crumbs">
        <?php if ($dsSecaoAtiva !== "" && $atual !== "index.php"): ?>
          <a href="index.php">Início</a> &raquo; <b><?= h($dsSecaoAtiva) ?></b>
        <?php else: ?>
          <b>Início</b>
        <?php endif; ?>
      </div>
      <main class="content<?= $layoutModerno ? " content-modern" : "" ?>">
<?php else: ?>
  <div class="auth-wrap">
    <div class="auth-card<?= ($layoutLargo ?? false) ? " wide" : "" ?>">
      <div class="brand brand-center"><span class="brand-mark">&#9656;</span> CorridasAPP</div>
<?php endif; ?>
