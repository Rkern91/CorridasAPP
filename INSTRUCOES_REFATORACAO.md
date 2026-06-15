# Instruções de Refatoração — CorridasAPP

> Documento de briefing para o Claude trabalhando na IDE.
> Leia este arquivo por completo **antes de qualquer ação**.

---

## 1. Contexto do projeto

CorridasAPP é um sistema de **cadastro de eventos de corrida**, escrito em **PHP puro** (sem framework — isso é intencional e deve ser mantido). Para cada evento de corrida o sistema lida com **modalidades**, **cidades** e **atletas (usuários)**.

A arquitetura segue **MVC manual**:

- **Controllers/** — recebem as chamadas vindas das telas (`man_*`, `sel_*`) e deveriam delegar aos métodos do Model.
- **Model/** — responde aos Controllers; contém regras de negócio e acesso a dados.
- **View/** — telas do sistema; fazem chamadas ao Controller.

Existe uma classe `ConexaoBanco` que centraliza/intermedia as conexões ao banco (PostgreSQL). **Essa abstração deve ser mantida** como intermediadora — apenas amadurecida.

### O que NÃO mudar
- A linguagem: continua **PHP puro**, sem Laravel/Symfony/etc. Construção manual.
- O **banco de dados**: o schema (tabelas e campos) está bem organizado. **Não altere tabelas, colunas nem dados.** Explore o schema para entender o domínio, mas não o modifique.
- A **essência** do desenho original: separação de responsabilidades em MVC e a classe intermediadora de banco.

---

## 2. Regras de trabalho (LEIA COM ATENÇÃO)

Estas regras são inegociáveis nesta colaboração:

1. **NÃO faça commits, merges, exclusão de arquivos ou de branches sem aprovação explícita do desenvolvedor.** Sempre que algo estiver pronto para ser commitado, **pare, descreva o que será commitado e peça verificação/teste** antes. O desenvolvedor testa, e só então autoriza. **Quando um commit é aprovado, o `push` correspondente já fica autorizado junto** — não precisa pedir aprovação separada para o push (mas pode confirmar ambos quando achar mais seguro).
2. **Trabalhe por fases.** Cada fase tem um escopo fechado. Só inicie a próxima fase após o desenvolvedor confirmar que a anterior foi validada.
3. **Não quebre o que funciona.** Cada entidade (cidade, evento, modalidade, usuário) deve continuar funcionando após sua refatoração. Antes de declarar uma fase pronta, descreva como testá-la manualmente.
4. **Pergunte quando houver ambiguidade.** Não invente regras de negócio. Se algo não estiver claro no código, pergunte antes de assumir.
5. **Mantenha um registro de progresso** (ver seção 7).

---

## 3. Padrões de código (obrigatórios em todo código novo ou alterado)

O desenvolvedor adota os padrões abaixo no dia a dia. **Siga-os à risca** em qualquer linha que você escrever ou modificar. Sobrescreva o estilo padrão da IDE quando ele conflitar com estes pontos.

1. **Indentação de 2 espaços.** Sempre. Nunca 4 espaços nem tabs, em nenhum nível de aninhamento. IDEs e modelos costumam usar 4 — aqui é 2.

2. **Chaves sempre em linha nova (estilo Allman).** A abertura e o fechamento de chaves ficam em sua própria linha, nunca na mesma linha do `if`/`for`/`function`/etc.
   ```php
   if ($condicao)
   {
     // ...
   }
   ```
   Errado:
   ```php
   if ($condicao) {  // chave na mesma linha — NÃO fazer
   }
   ```

3. **`if` simples (uma única instrução) não usa chaves.**
   ```php
   if (!$resultado)
     throw new Exception("...");
   ```

4. **Alinhamento de `=` em atribuições.** Quando houver atribuições em linhas próximas/sequenciais, **alinhe os sinais de `=` um sob o outro**, usando quantos espaços forem necessários para formar um bloco visual.
   ```php
   $nmCidade   = $request["nm_cidade"];
   $cdUf       = $request["cd_uf"];
   $cdCidade   = $request["cd_cidade"];
   $dsOperacao = $request["f_action"];
   ```

5. **Nomenclatura de variáveis seguindo o padrão do banco.** Leia os scripts em `sql/` e **identifique o padrão de prefixos das colunas e tabelas** (ex.: `nm_` para nome/descrição textual, `cd_` para código/chave, `ds_` para descrição, `dt_`, `qt_`, etc.). Aplique esse mesmo padrão, em camelCase, ao nomear variáveis em PHP — por exemplo, a coluna `nm_cidade` vira `$nmCidade`, `cd_uf` vira `$cdUf`. Mantenha consistência com o que já existe no código.

> Se tiver **qualquer dúvida** sobre um desses padrões ou sobre como aplicá-lo a um caso específico, **pergunte ao desenvolvedor antes** — ele confirma.

---

## 4. Setup de Git (primeira coisa a fazer)

Antes de qualquer alteração de código:

1. Garanta que a branch atual (`master`/`main`) está limpa e atualizada.
2. **Crie uma branch de backup da versão original.** Sugestão de nome: `old` (ou `versao-original`, à escolha do desenvolvedor). Essa branch é o retrato fiel do projeto como está hoje e **não deve ser tocada depois de criada**.
3. **Crie a branch de trabalho** para a refatoração (sugestão: `refactor`). Todo o trabalho novo acontece aqui.
4. A `master`/`main` só recebe o resultado final, via merge, **depois que tudo estiver validado** pelo desenvolvedor.

> Lembrete: criar branches localmente é permitido, mas **não faça push nem commits sem aprovação** conforme a Regra 1. Proponha os comandos e aguarde o OK.

---

## 5. Fase 0 — Descoberta (NÃO escreva código nesta fase)

Antes de propor qualquer mudança, faça o reconhecimento completo e entregue um **relatório de entendimento** ao desenvolvedor. Nesta fase você apenas lê e documenta:

1. **Mapeie a estrutura.** Liste todos os arquivos e descreva, em uma linha cada, o papel de cada um.
2. **Entenda o domínio pelo banco.** Leia os scripts em `sql/` (`script_tabelas_evento.sql`, `script_inserts.sql`) e descreva as entidades e seus relacionamentos. **Sem alterar nada.**
3. **Entenda o ambiente.** O ambiente roda em **dois contêineres Docker separados e independentes do projeto** (não há `docker-compose.yml` nem `Dockerfile` dentro do CorridasAPP):
   - um contêiner com **Apache + PHP 8.2**;
   - um contêiner com **PostgreSQL** (o banco).
   
   Ambos são iniciados pelo desenvolvedor por fora do projeto. **Não crie arquivos de Docker no projeto.** O que você precisa é localizar, dentro da pasta do projeto, o **arquivo de configuração de conexão ao banco** (host, porta, dbname, usuário, senha) e confirmar que entende como a aplicação se conecta ao PostgreSQL. Se precisar testar e o ambiente não estiver no ar, **peça ao desenvolvedor para subir os contêineres** em vez de tentar criar/subir você mesmo.
4. **Identifique os problemas conhecidos** (alguns já mapeados pelo desenvolvedor):
   - Model fazendo trabalho de View (montagem de HTML dentro de `Form*.php`).
   - Controllers quase vazios — apenas instanciam o Model em vez de intermediar de fato.
   - Views acessando o Model através do Controller, em vez de o Controller comandar.
   - Fluxo de login misturando **usuário comum** e **administrador** no mesmo caminho.
   - `ConexaoBanco` sem **prepared statements** (risco de SQL Injection — note as queries que interpolam `$arrRequest[...]` direto no SQL), com `runQueryes` controlando operações por uma string frágil, e sem suporte a transações.
5. **Entregue o relatório** e aguarde o desenvolvedor confirmar que seu entendimento está correto antes de seguir.

---

## 6. Fases de refatoração (escopo de cada uma)

Execute uma fase por vez, com aprovação entre elas. A ordem é proposta — o desenvolvedor pode reordenar.

### Fase 1 — Camada de banco (`ConexaoBanco` → `Database`)
É o alicerce; refatorar primeiro facilita o resto.
- Manter o papel de intermediadora única das conexões.
- Introduzir **prepared statements** (parâmetros `$1, $2...` no PostgreSQL) em todas as operações.
- Separar responsabilidades em métodos claros, ex.: `select()`, `execute()`, `lastInsertId()`, e suporte a transações (`begin/commit/rollback`).
- Centralizar configuração de conexão (ex.: `config/database.php`), lendo do ambiente Docker.

### Fase 2 — Models limpos
- Remover **todo HTML** dos Models. O Model retorna **dados** (arrays/objetos), nunca markup.
- Manter regras de negócio (ex.: validação de pendências antes de excluir uma cidade).

### Fase 3 — Controllers como intermediadores reais
- Controller recebe a requisição, valida, chama o Model e entrega os dados à View.
- A View deixa de acessar o Model diretamente.

### Fase 4 — Views e novo layout
- Telas passam a apenas **renderizar** os dados recebidos do Controller.
- **Novo layout:** menu fixo na **lateral esquerda**, conteúdo (formulário/listagens) exibido na **área à direita**. Layout limpo e responsivo, substituindo o visual rudimentar atual.
- Extrair `head`/`footer` para layouts reutilizáveis.
- Consulte a skill de design de frontend do ambiente se disponível para tokens visuais e boas práticas.

### Fase 5 — Separação de papéis (admin × usuário)
- Em vez de dois projetos separados, introduzir **controle de perfil** (ex.: `ds_perfil = 'admin' | 'usuario'`).
- Usuário comum: cadastro e ajuste dos próprios dados.
- Administrador: gestão de eventos, cidades, modalidades, etc.
- Proteger rotas/telas administrativas com verificação de sessão/perfil.

> Observação: a separação de perfil **não deve exigir mudança de schema** se já houver campo de perfil. Se não houver, **pare e pergunte** antes de propor qualquer alteração no banco.

---

## 7. Registro de progresso

Crie e mantenha um arquivo `PROGRESSO.md` na raiz da branch de trabalho, atualizado ao fim de cada fase, contendo:
- Fase atual e status.
- O que foi alterado (arquivos tocados e por quê).
- O que falta.
- Pendências/dúvidas em aberto para o desenvolvedor.

Isso garante que, mesmo se a sessão reiniciar, o contexto não se perca.

---

## 8. Resumo do fluxo esperado

1. Ler este documento.
2. Criar branch `old` (backup) e branch `refactor` (trabalho) — propor comandos, aguardar OK.
3. **Fase 0 (Descoberta):** relatório de entendimento, validação do Docker e do banco — sem código.
4. Aguardar confirmação do entendimento.
5. Executar as fases 1→5, uma de cada vez, **pedindo verificação/teste antes de cada commit**.
6. Manter `PROGRESSO.md` atualizado.
7. Merge na `master`/`main` somente após validação final do desenvolvedor.

**Em caso de dúvida sobre escopo, banco, ou regra de negócio: pergunte. Nunca assuma.**
