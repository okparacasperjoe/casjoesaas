<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Task | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .main-content { padding: 10px !important; }
            .top-bar { flex-direction: column; gap: 10px; align-items: stretch !important; }
            .top-bar h2 { font-size: 1.3rem !important; }
            .top-bar div { display: flex; flex-direction: column; gap: 8px; width: 100%; }
            .top-bar .btn { width: 100%; justify-content: center; }
            .card { margin: 10px auto !important; max-width: 100% !important; }
            .card h3 { font-size: 1.2rem !important; }
            .card > div[style*="grid"] { grid-template-columns: 1fr !important; gap: 10px !important; }
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Task Details</h2>
            <div>
                <a href="/erp/tasks/edit?id=<?= $task['id'] ?>" class="btn" style="background: #feca57;"><ion-icon name="create-outline"></ion-icon> Edit</a>
                <a href="/erp/tasks" class="btn" style="background: #666;">Back to List</a>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 20px auto;">
            <h3 style="margin-top: 0; color: #ffffff;"><?= htmlspecialchars($task['title']) ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div>
                    <p style="margin: 10px 0;"><strong>Project:</strong> <?= htmlspecialchars($task['project_name'] ?? 'N/A') ?></p>
                    <p style="margin: 10px 0;">
                        <strong>Priority:</strong> 
                        <span style="
                            background: <?= $task['priority'] == 'high' ? '#ff6b6b' : ($task['priority'] == 'medium' ? '#feca57' : '#48dbfb') ?>;
                            color: white;
                            padding: 4px 12px;
                            border-radius: 6px;
                            font-weight: 600;
                        "><?= ucfirst($task['priority']) ?></span>
                    </p>
                    <p style="margin: 10px 0;">
                        <strong>Status:</strong> 
                        <span style="background: rgba(255, 255, 255, 0.1); color: #fff; padding: 4px 12px; border-radius: 6px;">
                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                        </span>
                    </p>
                </div>
                
                <div>
                    <p style="margin: 10px 0;"><strong>Assigned To:</strong> <?= htmlspecialchars($task['assignee_name'] ?? 'Unassigned') ?></p>
                    <p style="margin: 10px 0;"><strong>Due Date:</strong> <?= $task['due_date'] ? date('F j, Y', strtotime($task['due_date'])) : 'No due date' ?></p>
                    <p style="margin: 10px 0;"><strong>Created:</strong> <?= date('F j, Y', strtotime($task['created_at'])) ?></p>
                </div>
            </div>

            <?php if (!empty($task['description'])): ?>
            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid rgba(255, 255, 255, 0.1);">
                <h4 style="margin-bottom: 10px; color: #ffffff;">Description</h4>
                <p style="color: #eeeeee; line-height: 1.6;"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
            </div>
            <?php endif; ?>

<div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; text-align: center;">
                <a href="/erp/tasks/edit?id=<?= $task['id'] ?>" class="btn" style="background: #feca57; margin-right: 10px;">
                    <ion-icon name="create-outline"></ion-icon> Edit Task
                </a>
                <form method="POST" action="/erp/tasks/delete" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                    <button type="submit" class="btn" style="background: #ff6b6b;">
                        <ion-icon name="trash-outline"></ion-icon> Delete Task
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
