<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Log | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Activity Logs</h2>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">User</th>
                        <th style="padding: 10px;">Action</th>
                        <th style="padding: 10px;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; color: #666; font-size: 0.9em;"><?= $log['created_at'] ?></td>
                            <td style="padding: 10px;">User #<?= $log['user_id'] ?></td>
                            <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($log['action']) ?></td>
                            <td style="padding: 10px; color: #555;"><?= htmlspecialchars($log['details']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
