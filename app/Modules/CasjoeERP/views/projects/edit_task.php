<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .main-content { padding: 10px !important; }
            .top-bar { flex-direction: column; gap: 10px; }
            .top-bar h2 { font-size: 1.3rem !important; }
            .card { margin: 10px auto !important; max-width: 100% !important; }
            .form-control, .btn { font-size: 16px !important; }
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Edit Task</h2>
            <a href="/erp/tasks" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: 20px auto;">
            <form method="POST" action="/erp/tasks/update">
                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Project</label>
                    <select name="project_id" class="form-control" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $task['project_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Assign To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">Unassigned</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $e['id'] == $task['assigned_to'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="low" <?= $task['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= $task['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= $task['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="review" <?= $task['status'] == 'review' ? 'selected' : '' ?>>Review</option>
                        <option value="done" <?= $task['status'] == 'done' ? 'selected' : '' ?>>Done</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="<?= $task['due_date'] ?>">
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%; background: #4facfe;">Update Task</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
