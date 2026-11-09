# TaskFlow PHP

Lista de tarefas com CRUD completo em PHP e MySQL, feita em duas versões para comparar as abordagens:

- `tarefas.php`: versão tradicional, o PHP monta a página a cada ação
- `index.html` + `api.php`: versão dinâmica, o JavaScript conversa com uma API em JSON usando `fetch`, sem recarregar a página

![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)

## API

Todas as rotas ficam em `api.php` e são escolhidas pelo parâmetro `acao`:

| acao | método | parâmetros |
|---|---|---|
| listar | GET | |
| cadastrar | POST | titulo |
| alternar_status | POST | id, concluida |
| excluir | POST | id |

```json
{ "sucesso": true, "dados": [ { "id": 1, "titulo": "Estudar PHP", "concluida": 0 } ] }
```

## Rodando localmente

1. Rode o `schema.sql` no MySQL
2. Ajuste o `config.php` com os dados do seu banco (os valores que estão lá são só de exemplo)
3. Na pasta do projeto: `php -S localhost:8000`
4. Abra `http://localhost:8000/index.html` (versão com fetch) ou `http://localhost:8000/tarefas.php` (versão tradicional)

## Próximos passos

- Corrigir `dia(` para `die(` no `conexao.php`
- Editar o título de uma tarefa
- Filtro de pendentes e concluídas

---
Walace Soares Vidigal Junior · Licença MIT
