# Padrões de Código

Estas regras são obrigatórias para todo código novo ou alterado.

## Indentação

Utilizar sempre 2 espaços.

Não utilizar tabs.

## Chaves

Utilizar estilo Allman.

Exemplo:

```php
if ($condicao)
{
  // ...
}
```

## If Simples

Quando houver apenas uma instrução, não utilizar chaves.

Exemplo:

```php
if (!$resultado)
  throw new Exception("Erro.");
```

## Alinhamento de Atribuições

Quando existirem atribuições sequenciais relacionadas, alinhar visualmente os sinais de igualdade.

Exemplo:

```php
$nmCidade   = $request["nm_cidade"];
$cdUf       = $request["cd_uf"];
$cdCidade   = $request["cd_cidade"];
$dsOperacao = $request["f_action"];
```

## Convenção de Variáveis

Seguir os prefixos utilizados no banco de dados e converter para camelCase.

Exemplos:

```php
nm_cidade      -> $nmCidade
cd_uf          -> $cdUf
ds_descricao   -> $dsDescricao
dt_evento      -> $dtEvento
vl_valor       -> $vlValor
```

Prefixos usuais:

* `cd_` → código / chave
* `nm_` → nome
* `ds_` → descrição / texto
* `dt_` → data
* `hr_` → hora
* `vl_` → valor
* `nr_` → número
* `qt_` → quantidade
* `id_` → identificador ou status
* `ar_` → array

## Banco de Dados

* Utilizar prepared statements.
* Nunca interpolar valores diretamente em SQL.
* Utilizar a classe `Database` para acesso ao banco.
* Utilizar transações quando houver operações multi-tabela.

## Views

* Utilizar `h()` para saída de dados dinâmicos.
* Não implementar regras de negócio em Views.
* Não acessar Models diretamente.

## Models

* Retornar apenas dados.
* Não gerar HTML.
* Centralizar regras de negócio.

## Controllers

* Coordenar o fluxo entre View e Model.
* Não conter SQL.
* Não conter HTML.