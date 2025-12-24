<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Permissions | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <h2>System Permissions</h2>
        <div class="card">
            <ul>
                <?php foreach ($permissions as $key => $label): ?>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <strong><?= htmlspecialchars($key) ?></strong>: <?= htmlspecialchars($label) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
</div>
</body>
</html>
