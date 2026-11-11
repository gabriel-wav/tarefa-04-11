# MVC e MVP nesta aplicação

## O que muda

No MVC, o Controller recebe a requisição, aciona o Model e escolhe uma resposta; a View costuma receber dados e renderizar a tela. No MVP, o Presenter assume a coordenação da apresentação e conversa com a View por uma interface. A View fica passiva: ela recebe dados ou uma mensagem e renderiza, sem buscar dados nem decidir regras de apresentação.

## Por que mudar

A interface explícita entre Presenter e View reduz o acoplamento com HTML. O Presenter pode ser testado sem navegador, usando um mock de `TaskViewInterface`, e outra implementação de View pode ser adicionada sem mudar o Model.

## Onde mudou

- `src/Presenter/TaskPresenter.php` concentra a coordenação e transforma os títulos para maiúsculo antes de chamar a View.
- `src/View/TaskViewInterface.php` define as operações que qualquer View precisa oferecer.
- `src/View/TaskHtmlView.php` implementa o contrato e inclui `list.php`, que é apenas o template HTML.
- `src/Model/Task.php` mantém acesso ao SQLite, validação e operações de tarefas.
- `index.php` faz a composição por injeção de dependência e encaminha a ação para o Presenter.
- `tests/TaskPresenterTest.php` verifica a formatação do título com dublês do Model e da View.

O projeto publicado continha a versão spaghetti em `index.php`, embora a atividade descreva uma versão MVC anterior. A implementação foi organizada diretamente a partir dos recursos existentes, preservando título, descrição, vencimento, responsável, conclusão e exclusão.
