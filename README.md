# AprovaEnem

Plataforma web gratuita para realização de simulados personalizados do ENEM, com acompanhamento de desempenho por meio de estatísticas detalhadas.

## Stack

- **Backend:** Laravel 13 (PHP 8.3+), arquitetura MVC
- **Frontend:** Blade + Tailwind CSS v4
- **Banco de dados:** PostgreSQL 18
- **Ambiente:** Docker (Laravel Sail)

## Requisitos

- [Docker](https://www.docker.com/) instalado e em execução

## Configuração inicial

1. Clone o repositório:

   ```bash
   git clone https://github.com/joaoribeiro74/AprovaEnem.git
   cd AprovaEnem
   ```

2. Copie o arquivo de variáveis de ambiente:

   ```bash
   cp .env.example .env
   ```

   As variáveis já vêm configuradas para o ambiente Docker (PostgreSQL, nomes de host dos containers, etc.) — normalmente não é necessário alterar nada aqui para rodar localmente.

3. Instale as dependências

   ```bash
    ./run composer install
   ```

4. Suba os containers:

   ```bash
   ./run up -d
   ```

5. Gere a chave da aplicação:

   ```bash
   ./run key:generate
   ```

6. Rode as migrations (ou use `./run db:fresh` no lugar, se quiser já popular com dados de exemplo):

   ```bash
   ./run migrate
   ```

7. Crie o link do armazenamento público e importe as questões do ENEM:

   ```bash
   ./run artisan storage:link
   ./run artisan questions:import --all
   ```

   A importação de todos os anos pode levar alguns minutos. Para um teste rápido, importe só um ano: `./run artisan questions:import 2023`.

8. Instale as dependências do frontend e compile os assets (a versão do Node também vem do container, não da máquina):

   ```bash
   ./run npm install
   ./run dev
   ```

9. Acesse [http://localhost](http://localhost).

## Comandos úteis (`./run`)

O projeto inclui um script `run` com atalhos para os comandos mais usados:

| Comando | Descrição |
|---|---|
| `./run up -d` | Sobe os containers |
| `./run down` | Derruba os containers |
| `./run down -v` | Derruba os containers **e apaga os dados do banco** (recria do zero na próxima subida) |
| `./run test` | Roda os testes (banco `testing`, separado do banco de desenvolvimento) |
| `./run pint` | Verifica/corrige o estilo do código (Laravel Pint) |
| `./run db:console` | Abre o console `psql` |
| `./run db:reset` | Reseta o banco de desenvolvimento (`migrate:fresh`) |
| `./run db:populate` | Roda as seeders |
| `./run db:fresh` | Reset + seed em um só comando |
| `./run dev` | Compila o Tailwind/assets em modo watch (Vite) |
| `./run artisan <comando>` | Atalho genérico para qualquer comando Artisan sem um atalho dedicado |
| `./run npm <comando>` | Atalho genérico para qualquer comando npm |
| `./run key:generate` | Gera a `APP_KEY` da aplicação |
| `./run migrate` | Roda as migrations pendentes |

> `db:fresh` já executa o seed automaticamente — não é necessário rodar `db:populate` em seguida (isso causaria erro de chave duplicada nos dados de exemplo).

## Importação

A importação pode ser repetida quantas vezes for preciso: as questões existentes são atualizadas, sem duplicar registros. As imagens são copiadas para `storage/app/public/questions` e os links no texto são reescritos para o armazenamento da aplicação.

```bash
./run artisan questions:import 2023          # um ano
./run artisan questions:import 2021 2022     # vários anos
./run artisan questions:import --all         # todos os anos encontrados na pasta de dados
```

Para corrigir uma questão, edite o `details.json` dela e importe o ano de novo.

Se o mesmo número aparecer duas vezes com o mesmo idioma (por exemplo, as duas versões de uma questão de língua estrangeira sem o campo `language`), a importação para e lista as pastas envolvidas, para você preencher o campo.

## Conferência dos dados

| Comando | Descrição |
|---|---|
| `./run artisan questions:audit <ano>` | Procura problemas nas questões importadas: alternativas faltando, gabarito ausente, textos vazios e imagens que não foram encontradas |
| `./run artisan questions:check-layouts` | Compara a área de cada questão nos arquivos com a definida em `config/enem.php` e mostra, por ano, quantas são sobrescritas. |

Em ambiente local, as questões importadas podem ser conferidas no navegador em `http://localhost/dev/questions/{ano}`. A página aceita os filtros `?language=ingles`, `?language=espanhol`, `?language=any`, `?from=10&to=20` e `?per_page=20`.

## Testes

O ambiente de testes usa um banco PostgreSQL separado (`testing`), criado automaticamente pelo Sail no mesmo container de banco de dados — os testes não afetam os dados de desenvolvimento.

```bash
./run test
```

## Licença

Copyright (C) 2026 João Vitor Ribeiro Ribas Carvalho.

Este projeto é distribuído sob a [GNU GPL-2.0](LICENSE), por incluir um conjunto de dados
licenciado sob essa licença.

### Dados das questões

O conjunto de dados em `database/data/enem-api` é uma versão modificada da pasta `public` do
projeto [enem.dev](https://github.com/yunger7/enem-api) (GPL-2.0). As modificações estão
descritas em [`NOTICE.md`](database/data/enem-api/NOTICE.md).
