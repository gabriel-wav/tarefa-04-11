# AULA DE 11-11-2026 - MINI PROJETO MVC - MVP - MVVM - PARTE 3

Projeto desenvolvido para a atividade prática de migração do Task Master para MVP.

## Arquitetura

- `src/Model/Task.php`: persistência SQLite, validações e operações das tarefas.
- `src/Presenter/TaskPresenter.php`: coordena as ações e prepara os dados para a apresentação.
- `src/View/TaskViewInterface.php`: contrato independente do formato da interface.
- `src/View/TaskHtmlView.php`: implementação HTML que inclui o template passivo `list.php`.
- `index.php`: monta as dependências e roteia as ações para o Presenter.

Leia [docs/mvc-vs-mvp.md](docs/mvc-vs-mvp.md) para a comparação entre os padrões.

## Novas Funcionalidades Adicionadas
- **Descrição**: Um texto explicativo sobre os detalhes da tarefa (opcional).
- **Data de Vencimento**: Prazo limite para conclusão no formato de data (obrigatório).
- **Responsável**: Nome da pessoa encarregada de executar a tarefa (obrigatório).

## Tecnologias
- PHP 8+
- SQLite (PDO)
- HTML5 / CSS3

## Como executar localmente
1. Abra o terminal na pasta do projeto:
   ```bash
   php -S localhost:8000
   ```
2. Acesse no navegador:
   `http://localhost:8000`

## Teste do Presenter

Com PHPUnit instalado, execute:

```bash
phpunit tests/TaskPresenterTest.php
```

## Como executar no GitHub Codespaces
1. Abra o repositório no GitHub.
2. Clique em **<> Code** -> aba **Codespaces** -> **Create codespace on main**.
3. Abra o terminal integrado (`Ctrl + '`) e digite:
   ```bash
   php -S localhost:8000
   ```
4. Clique no botão de abrir na porta 8000 gerada pelo VS Code Web.
