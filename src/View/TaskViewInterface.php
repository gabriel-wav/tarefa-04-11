<?php
declare(strict_types=1);

interface TaskViewInterface
{
    public function displayTasks(array $tasks): void;

    public function showError(string $message): void;
}
