<!DOCTYPE html>
<html lang="en">
<head>
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
