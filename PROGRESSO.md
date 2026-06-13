# PROGRESSO — Refatoração CorridasAPP

Registro de progresso da refatoração faseada (ver `INSTRUCOES_REFATORACAO.md`).
Branch de trabalho: `refactor`. Backup intocável: `old` (commit `40d08a4`).

---

## Fase 0 — Descoberta ✅ (validada)
Relatório de entendimento entregue e aprovado pelo desenvolvedor:
- Arquitetura MVC manual, PHP puro, PostgreSQL via `pg_*`.
- Problemas confirmados: HTML nos Models, Controllers vazios, Views acessando Model,
  login misturando admin/comum, `ConexaoBanco` sem prepared statements/transações.

### Decisões do desenvolvedor
- Branches: `old` (backup) e `refactor` (trabalho).
- Credenciais: ler do `.env` via `getenv()` (config centralizada).
- Senhas em texto puro: tratar na **Fase 5** (hashing).
- Renomes de arquivos autorizados quando amadurecerem a estrutura.

---

## Fase 1 — Camada de banco (`ConexaoBanco` → `Database`) ⏳ AGUARDANDO TESTE

### O que foi alterado e por quê
- **`config/database.php` (novo):** configuração centralizada de conexão. Lê
  `DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PWD` do ambiente (carrega o `.env` se existir,
  sem sobrescrever variáveis já definidas pelo contêiner). Remove segredos do código
  versionado. Testado via CLI: retorna os parâmetros corretos lendo do `.env`.
- **`Model/Database.php` (novo, substitui `Model/ConexaoBanco.php`):** camada
  intermediadora única, agora com:
  - `select($sql, $params)` / `execute($sql, $params)` usando **`pg_query_params`**
    (placeholders `$1, $2...`) — elimina SQL Injection.
  - `lastInsertId()` (via `RETURNING`).
  - Transações: `begin()` / `commit()` / `rollback()`.
  - Erros lançam `Exception` (substitui o retorno booleano + `getLastQueryError`).
  - Conexão lida de `config/database.php`; lança exceção clara se falhar.
- **`Model/ConexaoBanco.php`:** removido (rename → `Database.php`).
- **`init.php`:** reduzido a bootstrap (timezone). Credenciais saíram daqui.
- **Models migrados** para a nova API parametrizada (toda interpolação de
  `$arrRequest[...]` no SQL foi eliminada):
  - `Model/Usuario.php` (login — era o ponto mais crítico de injeção).
  - `Model/FormCidade.php`, `Model/FormModalidade.php`, `Model/FormUsuario.php`,
    `Model/FormInscricao.php`.
  - `Model/FormEvento.php` — além de parametrizado, as operações multi-comando
    (inserir/atualizar/deletar evento + modalidades + dependências) passaram a rodar
    dentro de **transação** (begin/commit, rollback em erro).
- **`Controllers/ProcessActionFormController.php`:** referência estática atualizada
  para `Database::$opIdErrosBd`. As chaves do mapa de erros foram corrigidas de
  `inserir/update/delete` para `inserir/atualizar/deletar` (as ações reais do
  formulário), corrigindo um bug latente que gerava id de erro vazio em update/delete.

### Não alterado nesta fase (escopo de fases seguintes)
- HTML dentro dos Models (`montarForm*`) → **Fase 2**.
- Controllers como intermediadores reais / Views sem acesso ao Model → **Fase 3**.
- Layout/telas → **Fase 4**.
- Perfil admin×usuário e hashing de senha → **Fase 5**.

### Verificação automática já feita
- `php -l` em todos os arquivos PHP: sem erros de sintaxe.
- `grep` por `ConexaoBanco/runQueryes/getLastQuery`: só restam em código comentado
  (bloco TODO de exclusão de usuário em `FormUsuario::deletarAcao`).
- Carregamento de `config/database.php` testado via CLI (lê do `.env`).

### Como testar manualmente (precisa dos contêineres no ar)
> Subir os contêineres (Apache+PHP e PostgreSQL) antes de testar.
1. **Login:** logar como admin e como usuário comum (credenciais existentes no
   `script_inserts.sql`). Conferir que o menu correto aparece.
2. **Segurança (injeção):** no login, tentar email `' OR '1'='1` e senha `' OR '1'='1`.
   Deve **falhar** (antes, logava como qualquer um).
3. **Cidade:** inserir, editar, listar e excluir. Tentar excluir cidade com evento/
   pessoa vinculados → deve bloquear com mensagem de pendência.
4. **Modalidade:** inserir, editar, listar, excluir. Excluir modalidade ligada a evento
   → deve bloquear.
5. **Evento:** inserir (com modalidades), editar (trocando modalidades), excluir.
   Conferir que as modalidades do evento são atualizadas corretamente (transação).
6. **Usuário:** cadastrar novo usuário (tela de cadastro) e editar os próprios dados.
7. **Inscrição:** como usuário comum, inscrever-se em um evento e alterar a inscrição.

---

## Resolvido nesta sessão
- Arquivos de teste (`bd_teste.php`, `exe_teste.php`) movidos para `test/`, agora
  ignorada pelo git. `estrutura.txt` removido.
- Bug de `FormInscricao::deletarAcao` corrigido: passou a filtrar por `cd_inscricao`.

### Bugs encontrados no teste da Fase 1 (pré-existentes, não regressões)
- **[CORRIGIDO] Dados do evento não apareciam na tela de inscrição.** Em
  `FormInscricao::obterDadosEventoInscricao`, o filtro `i.cd_pessoa` ficava no `WHERE`
  junto de um `LEFT JOIN inscricao`; na primeira inscrição (sem registro em `inscricao`)
  o LEFT JOIN gerava NULL e o WHERE eliminava a linha do evento. Corrigido movendo
  `i.cd_pessoa` para a condição `ON` do LEFT JOIN; a listagem passou a usar
  `i.cd_inscricao IS NOT NULL` (resultado equivalente ao anterior).
- **[ADIADO → Fase 4] Loop de alert ao listar inscrições vazias.** Quando o usuário
  não tem inscrições, o Model devolve HTML de estado vazio com `ds_origem=inscricao`,
  mas `funcoes.js` (`verificarAcaoForm`) não tem o caso `inscricao` no `switch` →
  `confirm('')` com link vazio entra em loop. É problema de View/JS; será tratado na
  Fase 4, junto com a UX dos estados vazios.

## Pendências / dúvidas em aberto para o desenvolvedor
- **Código comentado** com referência a `ConexaoBanco` em `FormUsuario::deletarAcao`
  (TODO de exclusão de usuário). Implementar exclusão na Fase 5?
- **Senhas em texto puro:** confirmado para a Fase 5.

---

## Próxima fase
**Fase 2 — Models limpos:** remover todo HTML dos Models (mover para as Views),
mantendo as regras de negócio. Iniciar somente após validação da Fase 1.
