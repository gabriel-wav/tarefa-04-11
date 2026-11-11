<?php
declare(strict_types=1);

class TaskPresenter
{
    public function __construct(
        private Task $model,
        private TaskViewInterface $view
    ) {
    }

    public function index(): void
    {
        try {
            $tasks = $this->model->getAll();
            foreach ($tasks as &$task) {
                $task['title'] = strtoupper($task['title']);
            }
            unset($task);

            $this->view->displayTasks($tasks);
        } catch (Throwable $exception) {
            $this->view->showError($exception->getMessage());
        }
    }

    public function create(string $title, string $description, string $dueDate, string $assignee): void
    {
        try {
            $this->model->save($title, $description, $dueDate, $assignee);
            $this->redirect();
        } catch (Throwable $exception) {
            $this->view->showError($exception->getMessage());
        }
    }

    public function complete(int $id): void
    {
        try {
            $this->model->complete($id);
            $this->redirect();
        } catch (Throwable $exception) {
            $this->view->showError($exception->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $this->model->delete($id);
            $this->redirect();
        } catch (Throwable $exception) {
            $this->view->showError($exception->getMessage());
        }
    }

    private function redirect(): void
    {
        header('Location: index.php');
    }
}
