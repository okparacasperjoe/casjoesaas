<!DOCTYPE html>
<html lang="en">
<head>
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
                        <th style="padding: 10px;">Status</th>
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
</body>
</html>
