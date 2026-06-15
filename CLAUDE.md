# CorridasAPP

Sistema de gerenciamento de eventos de corrida desenvolvido para estudos utilizando PHP puro e PostgreSQL.

## Stack

* PHP 8.2
* PostgreSQL
* MVC manual
* CSS puro
* Sem frameworks

## Estrutura do Projeto

* `Controllers/` → recebem requisições e coordenam o fluxo da aplicação.
* `Model/` → regras de negócio e acesso a dados.
* `View/` → renderização de telas.
* `config/` → configurações da aplicação.
* `sql/` → schema e dados de exemplo.

## Arquitetura

Fluxo de leitura:

View → Controller → Model → Database

Fluxo de escrita:

View → ProcessActionFormController → Model → Database

### Regras arquiteturais

* `Database` é a única camada autorizada a acessar PostgreSQL.
* Utilizar sempre prepared statements.
* Models retornam dados, nunca HTML.
* Views não acessam Models diretamente.
* Não introduzir frameworks PHP ou JavaScript.
* Não criar Dockerfiles ou docker-compose para este projeto.

## Autenticação

* `auth_guard.php` exige autenticação.
* `admin_guard.php` exige perfil administrador.

Perfis:

* `1` = administrador
* `2` = usuário comum

## Documentação

* Padrões de código: `docs/CODING_STANDARDS.md`
* Backlog: `PENDENCIAS.md`

## Processo de Trabalho

* Perguntar quando houver ambiguidade.
* Não assumir regras de negócio.
* Explicar a estratégia antes de alterações grandes.
* Não alterar schema do banco sem aprovação explícita.
* Não criar commits/push sem autorização explícita.
* Atualizar documentação impactada quando necessário.

## Abordagem Esperada

Quando existirem múltiplas soluções viáveis para um problema:

* explicar os trade-offs;
* recomendar uma abordagem;
* aguardar confirmação antes de mudanças arquiteturais relevantes.

O objetivo deste projeto é evoluir a aplicação e simultaneamente servir como ambiente de aprendizado de engenharia de software, arquitetura e boas práticas.

## Checklist de Conclusão

Antes de considerar uma tarefa concluída:

* validar sintaxe dos arquivos PHP alterados;
* informar os arquivos modificados;
* descrever testes manuais recomendados;
* verificar impactos em autenticação e permissões quando aplicável;
* verificar impacto em regras de negócio existentes.

## Git

- Crie branchs para cada feature nova que adicionamos ao projeto.
- Novas branchs devem ter o nome composto por data+hora no padrão tANOMESDIA_HRMIN
- 't' é apenas um prefixo
- ANO = Ex.: 2026
- MES = Ex.: 01
- DIA = Ex.: 31
- HR  = Ex.: 21
- MIN = Ex.: 00
- Não criar commits sem autorização explícita.
- Não executar merges sem autorização explícita.

---