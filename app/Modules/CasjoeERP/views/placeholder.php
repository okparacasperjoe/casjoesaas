<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title><?= $title ?? 'ERP Module' ?> | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2><?= $title ?? 'Module' ?></h2>
        </div>

        <div class="card" style="text-align: center; padding: 50px;">
            <ion-icon name="construct-outline" style="font-size: 64px; color: #fff; opacity: 0.5; margin-bottom: 20px;"></ion-icon>
            <h3>Coming Soon</h3>
            <p style="color: #e0e0e0;">The <strong><?= $title ?? 'requested' ?></strong> feature is currently under development.</p>
            <br>
            <a href="/erp" class="btn">Back to ERP Dashboard</a>
        </div>
    </main>
</div>
</body>
</html>
