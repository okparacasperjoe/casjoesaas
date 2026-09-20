<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
