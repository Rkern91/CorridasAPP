# CorridasAPP

Sistema de gerenciamento de eventos de corrida, desenvolvido em PHP puro e PostgreSQL, seguindo uma arquitetura MVC manual (sem frameworks).

## Origem

Este projeto nasceu como uma aplicação de CRUD simples em modelo MVC, desenvolvida na disciplina de Desenvolvimento Web da faculdade. O objetivo original era exercitar conceitos de redirecionamento do fluxo de uma aplicação Web, bem como estruturar páginas simples em HTML, aproveitando a oportunidade para treinar conceitos e práticas com PHP.

## Finalidade atual

O projeto continua sendo um ambiente de estudo, sem pretensão de se tornar uma aplicação em produção. Hoje ele também serve para aprofundar conhecimentos em:

* Engenharia de software e boas práticas de arquitetura (MVC, separação de responsabilidades, segurança).
* Programação assistida por IA, usada como ferramenta de apoio ao desenvolvimento.
* Engenharia de IA e arquiteturas de sistemas em um contexto prático.

## Stack

* PHP 8.2
* PostgreSQL
* MVC manual
* CSS puro
* Sem frameworks PHP ou JavaScript

## Estrutura do projeto

```
Controllers/   → recebem requisições e coordenam o fluxo da aplicação
Model/         → regras de negócio e acesso a dados
View/          → renderização de telas
config/        → configurações da aplicação
sql/           → schema e dados de exemplo
docs/          → documentação do projeto (padrões de código, backlog, progresso)
```

## Arquitetura

Fluxo de leitura:

```
View → Controller → Model → Database
```

Fluxo de escrita:

```
View → ProcessActionFormController → Model → Database
```

### Regras arquiteturais

* `Database` é a única camada autorizada a acessar o PostgreSQL.
* Uso obrigatório de prepared statements.
* Models retornam dados, nunca HTML.
* Views não acessam Models diretamente.
* Sem frameworks PHP ou JavaScript.

## Autenticação

* `auth_guard.php` exige autenticação.
* `admin_guard.php` exige perfil administrador.

Perfis de usuário:

* `1` = administrador
* `2` = usuário comum

## Documentação

* Padrões de código: [docs/CODING_STANDARDS.md](docs/CODING_STANDARDS.md)
* Backlog: [docs/PENDENCIAS.md](docs/PENDENCIAS.md)
* Histórico da refatoração: [docs/PROGRESSO.md](docs/PROGRESSO.md)

---

Este README é um documento vivo e deve evoluir junto com o projeto.
