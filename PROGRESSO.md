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

## Fase 2 — Models limpos ✅ (validada, commit `9cc612f`)

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

## Fase 3 — Controllers como intermediadores reais ✅ (commit `530a9ed`)

Objetivo: o Controller passa a **comandar o Model** e entregar os dados à View; a View
**deixa de acessar o Model diretamente**.

### O que foi alterado e por quê
- **Controllers de leitura reescritos** (`CidadeController`, `ModalidadeController`,
  `EventoController`, `CadastroUsuarioController`, `InscricaoController`): o Model virou
  propriedade **privada** e cada controller expõe métodos `obter*()` que delegam ao Model.
  Removido o `tratarErros()` vazio que engolia exceções — agora elas propagam para o
  `try/catch` da View (que redireciona para `erro.php`).
- **Views atualizadas** (10 telas `sel_*`/`man_*` + usuário): passaram a chamar
  `$XController->obter...()` em vez de `$XController->ControladorX->obter...()`. A View não
  conhece mais o Model.
- Mantidos como estão (escopo de outras fases): `ProcessActionFormController` (dispatcher
  de escrita — a View já não toca o Model na escrita, pois faz POST para o controller),
  `LoginController` (já intermediava), `TelaUserLoginController` (menu/HTML → Fase 4).

### Verificação feita (contêineres no ar, porta 8082)
- `php -l` em todos os Controllers/Views: sem erros. Nenhum `->Controlador` remanescente
  nas Views (grep).
- Smoke test HTTP autenticado (admin e comum): todas as telas HTTP 200, **zero erros/
  warnings**. Dados fluindo pelos novos métodos do controller — comprovado em cidade
  (9 itens), modalidade ("CORRIDA 5KM"), usuário ("Rafael Kern" / extrato).
- Obs.: evento/inscrição renderizam vazios **porque a tabela `evento` foi esvaziada
  durante os testes manuais** (excluir evento remove em cascata inscrições e vínculos de
  modalidade). Não é regressão; a delegação é idêntica à das entidades comprovadas.

### Como testar manualmente
Recriar ao menos um evento (admin) e repetir os fluxos: listar/editar cidade, modalidade,
evento; editar próprios dados e extrato; como comum, inscrever-se e gerenciar inscrição.

## Pendências / dúvidas em aberto para o desenvolvedor
- **Código comentado** com referência a `ConexaoBanco` em `FormUsuario::deletarAcao`
  (TODO de exclusão de usuário). Implementar exclusão na Fase 5?
- **Senhas em texto puro:** confirmado para a Fase 5.
- **Output escaping (htmlspecialchars):** sugerido para a Fase 4.
- **`FormEvento::obterDadosEvento` usa `INNER JOIN modalidade_evento`:** um evento sem
  modalidade vinculada não aparece na edição. Hoje todo evento é criado com modalidade
  pela UI, mas vale revisar (Fase 4/5).

---

## Fase 4 — Views e novo layout ✅ (commit `597fa5a`)

Tema escolhido pelo dev: **Profissional Sóbrio (claro)** — fundo claro, sidebar
azul-ardósia (#243447), acento azul (#2563eb). CSS puro, sem framework.

### O que foi alterado e por quê
- **`style.css` reescrito**: design tokens (cores/raio/sombra), layout flex com **menu
  fixo à esquerda + conteúdo à direita**, cartões, tabelas, formulários e botões
  estilizados, layout de autenticação centralizado e **responsivo** (sidebar vira barra
  no topo < 820px).
- **Layout reutilizável** (extrai head/footer e o menu):
  - `View/header.php` — abre a página; renderiza a **sidebar com menu por perfil**
    (admin × comum, item ativo destacado) ou, com `$layoutSidebar=false`, um cartão
    centralizado (login/cadastro/erro).
  - `View/footer.php` — fecha o layout e injeta `funcoes.js`.
  - `session.php` agora faz `session_start` **idempotente** (sem guardar); novo
    `auth_guard.php` faz sessão **+ exige login** (usado no topo das telas internas).
- **Menu virou a sidebar**: `Controllers/TelaUserLoginController.php` **removido**; o menu
  (itens por perfil) agora vive em `header.php`. `index.php` virou um dashboard de
  boas-vindas.
- **Removidos** `View/head.php` e `View/footer.html` (substituídos por header/footer).
- **Todas as Views** migradas para `require("header.php")`/`require("footer.php")`:
  - Internas (exigem login via `auth_guard`): `sel_*`/`man_*` de cidade, modalidade,
    evento, inscrição + `con_dados_usuario`.
  - `man_cadastro_usuario` tem **layout duplo**: cartão centralizado para cadastro novo
    (sem login) e layout com sidebar quando o usuário logado edita os próprios dados.
  - `login.php` e `erro.php` no layout centralizado.
- **`LoginController::realizarLoginUsuario()`** agora retorna `bool` (sem `echo`/redirect
  internos); `login.php` trata sucesso (redirect) e falha (mensagem na tela). Removido o
  `session_start()` solto de `LoginController` e `CadastroUsuarioController` (a View cuida
  da sessão).

### Verificação feita (contêineres no ar, porta 8082)
- `php -l` em todos os arquivos: sem erros.
- Smoke test HTTP: login público (cartão, sem sidebar); POST login → 302; **todas** as
  telas internas (admin e comum) HTTP 200 **com sidebar e zero erros/warnings**; cadastro
  público em cartão (modo inserir, selects populados); **guard** redireciona acesso sem
  login (302); **menu por perfil** correto (admin vê Cidades/Modalidades; comum vê Minhas
  Inscrições e não vê Cidades); item ativo destacado; logout → 302.

### Observações / pendências
- **Controle de acesso real ainda é da Fase 5**: o menu já é por perfil e as telas exigem
  login, mas um usuário comum ainda conseguiria abrir `man_evento.php`/`man_cidade.php`
  pela URL. O bloqueio por perfil será feito na Fase 5.
- **Output escaping**: ainda não aplicado (mantido o comportamento atual). Pode entrar na
  Fase 5 ou como tarefa de segurança à parte.
- Senhas em texto puro: Fase 5.

---

## Fase 5 — Separação de papéis (admin × usuário) + segurança ✅ (validada, commit `4c31706`)

Decisões do dev: hashing **ampliando a coluna** + upgrade no login; **output escaping** já
nesta fase.

### Controle de acesso por perfil (sobre `cd_id_tipo`, sem mudança no domínio)
- Novo `auth_guard.php` (já existia) + novo **`admin_guard.php`** (exige `cd_id_tipo = 1`).
- **Telas administrativas** passaram a usar `admin_guard`: `sel_cidade`, `man_cidade`,
  `sel_modalidade`, `man_modalidade`, `man_evento`. `sel_evento` segue com `auth_guard`
  (o comum precisa para se inscrever).
- **`ProcessActionFormController` (endpoint de escrita) agora valida permissão** — esse é
  o bloqueio que importa, pois um comum poderia postar direto:
  - `pessoa/inserir` é **público** (cadastro novo); demais ações exigem login;
  - `cidade/evento/modalidade` → **só admin**;
  - em ações de `pessoa`/`inscricao`, o `cd_pessoa` é **forçado para o da sessão** (o
    usuário só mexe nos próprios dados).

### Hashing de senha
- **`ds_senha` ampliada para `VARCHAR(255)`** (ALTER no banco + DDL `sql/` atualizado) —
  única mudança de schema, autorizada pelo dev (bcrypt = 60 chars não cabe em 50).
- `FormUsuario::inserirAcao`/`atualizarAcao` usam **`password_hash`**; no update, a senha
  só muda se informada (em branco mantém a atual) — e a tela **não pré-preenche** a senha.
  `funcoes.js` passou a exigir senha só no cadastro novo.
- **`Usuario::realizarLogin`**: busca por e-mail e usa **`password_verify`**; se a senha
  estiver em **texto puro (legado)**, autentica e **re-grava com hash na hora** (upgrade
  transparente). `obterDadosPessoa` deixou de trazer `ds_senha` para a View.

### Output escaping (XSS)
- Novo helper **`h()`** (`htmlspecialchars`) em `helpers.inc.php` (carregado também no
  `header.php`). Aplicado às saídas dinâmicas de **todas as Views** (dados e valores
  refletidos do request). Em `erro.php`, a mensagem é escapada; os links continuam HTML.

### Verificação feita (contêineres no ar, porta 8082)
- `php -l` em todos os arquivos: sem erros. SQL dinâmico do update conferido ($7/$8).
- **Senha:** login do admin com `12345` (texto puro) → 302 e a senha virou **bcrypt**;
  login seguinte autentica via hash; senha errada fica na tela com mensagem; cadastro novo
  já grava **bcrypt** (verify OK).
- **Perfil:** comum em telas admin → 302 p/ `erro.php` ("Acesso negado…"); admin nessas
  telas → 200; comum em telas comuns → 200; comum tentando **inserir cidade via POST** →
  bloqueado e **nada criado no banco**.
- **Escaping:** 80 usos de `h()` nas Views; `h("<script>…")` → escapado.

### Como testar manualmente
1. Logar como admin e como comum (senhas atuais; serão migradas no 1º login).
2. Comum: tentar abrir `man_evento.php`/`sel_cidade.php` pela URL → deve dar "Acesso negado".
3. Editar os próprios dados deixando a senha **em branco** (mantém) e depois trocando a senha.
4. Admin: CRUD de cidade/modalidade/evento normalmente.

## Pendências / dúvidas em aberto para o desenvolvedor
- **`FormUsuario::deletarAcao` ainda vazio** (exclusão de conta não implementada; o fluxo
  só faz logout). Implementar?
- **Ownership de inscrição:** o `cd_pessoa` é forçado na escrita, mas excluir/alterar usa
  `cd_inscricao`; falta validar que a inscrição pertence ao usuário (hardening futuro).
- **`sql/script_inserts.sql`** ainda traz senhas-semente em texto puro (migram no 1º login).
- **`FormEvento::obterDadosEvento` (INNER JOIN modalidade_evento):** evento sem modalidade
  não aparece na edição (revisão futura).

---

## Ajustes pós-refatoração (durante os testes do dev)
- **Listas vazias (`sel_*`):** em vez do popup `confirm()` sugerindo cadastro, a tela agora
  carrega normalmente exibindo "Nenhum registro cadastrado." e o botão **Adicionar** abaixo
  (admin). Removido o caso `cadastrar`/`confirm()` do `funcoes.js` (sem uso) e os
  `ds_origem` órfãos das Views. Alertas de sucesso (inserir/atualizar/excluir) preservados.

---

## Conclusão da refatoração ✅
As 5 fases planejadas foram implementadas, testadas pelo dev e integradas. A branch
`refactor` (commits `8a559a6`, `9cc612f`, `530a9ed`, `597fa5a`, `4c31706` + o ajuste de
listas vazias `fb430ac`) recebeu **validação final do desenvolvedor** e foi **mesclada em
`master`** (seção 8 das instruções), com push para `origin/master`. A branch `refactor`
fica preservada como histórico das fases.

### Pendências de backlog (não bloqueiam o merge; tratar quando o dev priorizar)
- `FormUsuario::deletarAcao` ainda vazio (exclusão de conta não implementada).
- Ownership de inscrição na alteração/exclusão por `cd_inscricao` (hardening).
- `sql/script_inserts.sql` com senhas-semente em texto puro (migram no 1º login).
- `FormEvento::obterDadosEvento` com `INNER JOIN modalidade_evento` (evento sem
  modalidade não aparece na edição).
