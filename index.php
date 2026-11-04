<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI (Expandido)
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DA TABELA (Acoplamento de Infraestrutura)
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    assignee TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES MISTURADOS
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dueDate = trim($_POST['due_date'] ?? '');
    $assignee = trim($_POST['assignee'] ?? '');

    // Validações de campos obrigatórios
    if (empty($title)) {
        $error = "O título da tarefa não pode estar vazio!";
    } elseif (empty($dueDate)) {
        $error = "A data de vencimento é obrigatória!";
    } elseif (empty($assignee)) {
        $error = "O responsável pela tarefa é obrigatório!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, assignee) VALUES (:title, :description, :due_date, :assignee)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description !== '' ? $description : null);
        $stmt->bindValue(':due_date', $dueDate);
        $stmt->bindValue(':assignee', $assignee);
        $stmt->execute();

        // Redirecionamento misturado com a lógica
        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }

    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS MISTURADA COM A VISUALIZAÇÃO
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master - Spaghetti Expandido</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; color: #333; display: flex; justify-content: center; padding: 40px 15px; margin: 0; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 650px; }
        h1 { font-size: 1.6rem; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 12px; margin-top: 0; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; font-size: 0.9rem; margin-bottom: 15px; }
        
        .form-task { display: flex; flex-direction: column; gap: 12px; margin: 20px 0; background: #fafafa; padding: 16px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .form-row { display: flex; gap: 10px; }
        .form-row input, .form-row select { flex: 1; }
        
        input[type="text"], input[type="date"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: inherit;
            font-size: 0.95rem;
        }
        textarea { resize: vertical; min-height: 60px; }
        
        button { background: #2563eb; color: white; border: none; padding: 12px 16px; border-radius: 4px; cursor: pointer; font-size: 1rem; font-weight: 600; }
        button:hover { background: #1d4ed8; }
        
        ul { list-style: none; padding: 0; margin: 0; }
        li { display: flex; justify-content: space-between; align-items: flex-start; padding: 14px; border-bottom: 1px solid #eee; gap: 10px; }
        li.done .task-title { text-decoration: line-through; color: #9ca3af; }
        li.done .task-desc { color: #9ca3af; }
        
        .task-content { flex: 1; }
        .task-title { font-weight: 600; font-size: 1.05rem; }
        .task-desc { margin: 6px 0; color: #555; font-size: 0.9rem; }
        .task-meta { display: flex; gap: 15px; font-size: 0.82rem; color: #6b7280; margin-top: 4px; }
        .badge { background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 12px; font-weight: 500; }
        
        .actions { display: flex; align-items: center; }
        .actions a { text-decoration: none; margin-left: 10px; font-size: 1.1rem; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h1>Task Master (Spaghetti Edition)</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" class="form-task">
        <div>
            <input type="text" name="title" placeholder="Título da tarefa *" autocomplete="off" required>
        </div>
        <div>
            <textarea name="description" placeholder="Descrição da tarefa (opcional)..."></textarea>
        </div>
        <div class="form-row">
            <input type="date" name="due_date" required title="Data de Vencimento">
            <input type="text" name="assignee" placeholder="Responsável *" autocomplete="off" required>
        </div>
        <button type="submit">Adicionar Tarefa</button>
    </form>

    <ul>
        <?php if (empty($tasks)): ?>
            <li style="justify-content: center; color: #9ca3af; font-style: italic;">Nenhuma tarefa cadastrada.</li>
        <?php endif; ?>

        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div class="task-content">
                    <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                    <?php if (!empty($task['description'])): ?>
                        <div class="task-desc"><?php echo nl2br(htmlspecialchars($task['description'])); ?></div>
                    <?php endif; ?>
                    <div class="task-meta">
                        <span>📅 Vencimento: <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($task['due_date']))); ?></strong></span>
                        <span>👤 Responsável: <span class="badge"><?php echo htmlspecialchars($task['assignee']); ?></span></span>
                    </div>
                </div>
                
                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
