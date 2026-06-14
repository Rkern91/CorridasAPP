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

## Fase 1 — Camada de banco (`ConexaoBanco` → `Database`) ✅ (validada, commit `8a559a6`)

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

## Fase 2 — Models limpos ⏳ AGUARDANDO TESTE

Objetivo: remover **todo HTML** dos Models; eles passam a retornar **apenas dados**
(arrays). O markup foi movido para as Views com PHP inline (Opção A — a componentização
de layout fica para a Fase 4).

### O que foi alterado e por quê
- **Models — HTML removido, agora só retornam dados** (métodos `montar*` substituídos
  por métodos `obter*` que devolvem arrays):
  - `FormCidade`: `obterListagemCidades()`, `obterCidade()`, `obterEstados()`.
  - `FormModalidade`: `obterListagemModalidades()`, `obterModalidade()`.
  - `FormEvento`: `obterListagemEventos()`, `obterDadosEvento()`, `obterCidades()`,
    `obterModalidades()`.
  - `FormUsuario`: `obterDadosPessoa()`, `obterExtratoUsuario()`, `obterCidades()`
    (removidos `obtemTipoUsuario`/`obterOptionsSexo`, que eram HTML/dead code; o select
    de sexo é estático e foi para a View).
  - `FormInscricao`: `obterListagemInscricoes()`, `obterDadosEventoInscricao()`,
    `obterModalidadesEvento()` (a descrição da modalidade entrou via JOIN na listagem,
    eliminando o N+1 de `obterDescricaoModalidade`).
  - Regras de negócio preservadas (validação de pendências em cidade/modalidade, transações
    em evento).
- **Views — agora montam o HTML** a partir dos dados, com `foreach`/`<?= ?>` inline e a
  lógica de `selected`/estado vazio. Reescritas: `sel_*`/`man_*` de cidade, modalidade,
  evento, inscrição; `man_cadastro_usuario.php` e `con_dados_usuario.php`.
- **Sessão nas telas de inscrição:** `sel_inscricao.php`/`man_inscricao.php` passaram a
  `require_once("../session.php")` no topo (antes de instanciar o controller, que lê
  `$_SESSION["cd_pessoa"]`). Como `head.php` usa o mesmo `require_once`, não há dupla
  `session_start`; de quebra, o `header()` de erro volta a funcionar (não há saída antes).

### Resolvido como efeito colateral (sem mexer no JS)
- **Loop de alert ao listar inscrições vazias** (estava adiado p/ Fase 4): a nova
  `sel_inscricao.php` exibe uma mensagem amigável no estado vazio em vez de emitir os
  hidden `ds_operacao=cadastrar`/`ds_origem=inscricao` que disparavam o `confirm('')` em
  loop. `funcoes.js` não foi alterado.
- **Estado-vazio de `sel_evento.php` para usuário comum:** quando não há eventos, o comum
  via o prompt "Deseja cadastrar novo evento?" (só admin cadastra). Agora o comum vê apenas
  "Nenhum evento disponível no momento"; o admin mantém o comportamento de cadastro. Isto
  é apenas cosmético — o **controle de acesso real (bloqueio de rotas/telas por perfil)
  continua sendo escopo da Fase 5**.

### Verificação feita (contêineres no ar, porta 8082)
- `php -l` em todos os Models/Views/Controllers: sem erros.
- Nenhum HTML/`montar*` remanescente nos Models (grep).
- Smoke test HTTP autenticado (admin e usuário comum): login, todas as listagens e telas
  de manutenção, extrato do usuário, e fluxo de inscrição (listar + editar) — **HTTP 200,
  zero erros fatais e zero warnings/notices**. Selects vêm com a opção correta marcada,
  campos preenchidos na edição, telefone formatado no extrato.

### Como testar manualmente
1. **Admin:** listar e editar cidade, modalidade, evento (conferir modalidades do evento),
   editar próprios dados e ver extrato.
2. **Usuário comum:** listar eventos (link "Inscrever-se"), inscrever-se em um evento novo,
   listar inscrições, alterar e excluir inscrição.
3. Conferir alertas de sucesso (inserir/atualizar/excluir) e bloqueios de pendência
   (excluir cidade/modalidade vinculada).

## Pendências / dúvidas em aberto para o desenvolvedor
- **Código comentado** com referência a `ConexaoBanco` em `FormUsuario::deletarAcao`
  (TODO de exclusão de usuário). Implementar exclusão na Fase 5?
- **Senhas em texto puro:** confirmado para a Fase 5.
- **Output escaping (htmlspecialchars):** mantido o comportamento original (sem escape de
  saída) para não alterar a renderização nesta fase; sugiro introduzir na Fase 4.

---

## Próxima fase
**Fase 3 — Controllers como intermediadores reais:** o Controller recebe a requisição,
chama o Model e entrega os dados à View; a View deixa de acessar o Model diretamente
(hoje ainda faz `$Controller->ControladorX->obter...()`). Iniciar após validação da Fase 2.
