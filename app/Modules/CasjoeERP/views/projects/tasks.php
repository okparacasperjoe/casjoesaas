<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Tasks | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>My Tasks</h2>
            <a href="/erp/tasks/create" class="btn"><ion-icon name="add-outline"></ion-icon> New Task</a>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Task</th>
                        <th style="padding: 10px;">Project</th>
                        <th style="padding: 10px;">Priority</th>
                        <th style="padding: 10px;">Status</th><th style="padding: 10px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($task['title']) ?></td>
                            <td style="padding: 10px; color: #666;"><?= htmlspecialchars($task['project_name']) ?></td>
                            <td style="padding: 10px;"><?= ucfirst($task['priority']) ?></td>
                            <td style="padding: 10px;">
                                <span style="background: #eee; padding: 2px 8px; border-radius: 4px;">
                                    <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
        <dialog id="editTaskModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <form action="/erp/tasks/update" method="POST">
                <input type="hidden" name="id" id="edit_task_id">
                <h3 style="color:#000066; margin-top:0;">Edit Task</h3>
                <div class="form-group"><label>Task Title *</label><input type="text" name="title" id="edit_task_title" class="form-control" required></div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_task_status" class="form-control">
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" id="edit_task_priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" onclick="document.getElementById('editTaskModal').close()" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn" style="background:#000066; color:#fff;">Update</button>
                </div>
            </form>
        </dialog>
        <script>
        function editTask(t) {
            document.getElementById('edit_task_id').value = t.id;
            document.getElementById('edit_task_title').value = t.title;
            document.getElementById('edit_task_status').value = t.status;
            document.getElementById('edit_task_priority').value = t.priority;
            document.getElementById('editTaskModal').showModal();
        }
        </script>
</body>
</html>
