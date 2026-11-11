<?php
declare(strict_types=1);

class Task
{
    public function __construct(private PDO $pdo)
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                due_date TEXT NOT NULL,
                assignee TEXT NOT NULL,
                done INTEGER DEFAULT 0
            )'
        );
    }

    public function getAll(): array
    {
        $statement = $this->pdo->query('SELECT * FROM tasks ORDER BY id DESC');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(string $title, string $description, string $dueDate, string $assignee): void
    {
        $title = trim($title);
        $description = trim($description);
        $dueDate = trim($dueDate);
        $assignee = trim($assignee);

        if ($title === '') {
            throw new InvalidArgumentException('O título da tarefa não pode estar vazio!');
        }
        if ($dueDate === '') {
            throw new InvalidArgumentException('A data de vencimento é obrigatória!');
        }
        if ($assignee === '') {
            throw new InvalidArgumentException('O responsável pela tarefa é obrigatório!');
        }

        $statement = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, due_date, assignee)
             VALUES (:title, :description, :due_date, :assignee)'
        );
        $statement->execute([
            ':title' => $title,
            ':description' => $description !== '' ? $description : null,
            ':due_date' => $dueDate,
            ':assignee' => $assignee,
        ]);
    }

    public function complete(int $id): void
    {
        $statement = $this->pdo->prepare('UPDATE tasks SET done = 1 WHERE id = :id');
        $statement->execute([':id' => $id]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id');
        $statement->execute([':id' => $id]);
    }
}
