<?php
declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    foreach (['Model', 'Presenter', 'View'] as $directory) {
        $file = __DIR__ . '/src/' . $directory . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $model = new Task($pdo);
    $view = new TaskHtmlView();
    $presenter = new TaskPresenter($model, $view);

    $action = $_GET['action'] ?? 'index';
    if ($action === 'create') {
        $presenter->create(
            (string) ($_POST['title'] ?? ''),
            (string) ($_POST['description'] ?? ''),
            (string) ($_POST['due_date'] ?? ''),
            (string) ($_POST['assignee'] ?? '')
        );
    } elseif ($action === 'complete') {
        $presenter->complete((int) ($_GET['id'] ?? 0));
    } elseif ($action === 'delete') {
        $presenter->delete((int) ($_GET['id'] ?? 0));
    } else {
        $presenter->index();
    }
} catch (Throwable $exception) {
    (new TaskHtmlView())->showError($exception->getMessage());
}
