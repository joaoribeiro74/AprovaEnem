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

7. Instale as dependências do frontend e compile os assets (a versão do Node também vem do container, não da máquina):

   ```bash
   ./run npm install
   ./run tw
   ```

8. Acesse [http://localhost](http://localhost).

## Comandos úteis (`./run`)

O projeto inclui um script `run` com atalhos para os comandos mais usados:

| Comando | Descrição |
|---|---|
| `./run up -d` | Sobe os containers |
| `./run down` | Derruba os containers |
| `./run down -v` | Derruba os containers **e apaga os dados do banco** (recria do zero na próxima subida) |
| `./run ps` | Lista os containers em execução |
| `./run test` | Roda os testes (banco `testing`, separado do banco de desenvolvimento) |
| `./run pint` | Verifica/corrige o estilo do código (Laravel Pint) |
| `./run db:console` | Abre o console `psql` |
| `./run db:reset` | Reseta o banco de desenvolvimento (`migrate:fresh`) |
| `./run db:populate` | Roda as seeders |
| `./run db:fresh` | Reset + seed em um só comando |
| `./run php:console` | Abre o Tinker (REPL do Laravel) |
| `./run tw` | Compila o Tailwind/assets em modo watch (Vite) |
| `./run git:clean:branchs` | Remove branches locais já mescladas |
| `./run artisan <comando>` | Atalho genérico para qualquer comando Artisan sem um atalho dedicado |
| `./run npm <comando>` | Atalho genérico para qualquer comando npm |
| `./run key:generate` | Gera a `APP_KEY` da aplicação |
| `./run migrate` | Roda as migrations pendentes |

> `db:fresh` já executa o seed automaticamente — não é necessário rodar `db:populate` em seguida (isso causaria erro de chave duplicada nos dados de exemplo).

## Testes

O ambiente de testes usa um banco PostgreSQL separado (`testing`), criado automaticamente pelo Sail no mesmo container de banco de dados — os testes não afetam os dados de desenvolvimento.

```bash
./run test
```
