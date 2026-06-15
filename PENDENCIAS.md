# Pendências / Backlog

Itens conhecidos, levantados durante a refatoração (ver `PROGRESSO.md`), que não bloqueiam
o que já foi entregue. Indicar qual priorizar quando quiser seguir.

- [ ] **Exclusão de conta de usuário** (`Model/FormUsuario.php::deletarAcao`): hoje está
  vazio; o fluxo de "excluir cadastro" só faz logout. Definir regra (cascata de
  inscrições? soft delete?) e implementar.

- [ ] **Ownership de inscrição na alteração/exclusão**: `cd_pessoa` é forçado a partir da
  sessão na escrita, mas alterar/excluir inscrição usa `cd_inscricao` sem validar que a
  inscrição pertence ao usuário logado. Hardening de segurança (`Model/FormInscricao.php`).

- [ ] **Senhas-semente em texto puro** (`sql/script_inserts.sql`): migram para hash
  automaticamente no 1º login de cada usuário, mas o seed em si ainda está em texto puro.
  Avaliar se vale gerar hashes já no script ou documentar como esperado em ambiente de
  desenvolvimento.

- [ ] **`FormEvento::obterDadosEvento` usa `INNER JOIN modalidade_evento`**: um evento sem
  nenhuma modalidade vinculada não aparece na tela de edição. Hoje todo evento é criado com
  ao menos uma modalidade pela UI, mas vale revisar para `LEFT JOIN` (evento sem modalidade
  editável).
