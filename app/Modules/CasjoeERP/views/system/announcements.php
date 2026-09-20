<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Announcements | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Announcements</h2>
            <a href="/erp/announcements/create" class="btn"><ion-icon name="megaphone-outline"></ion-icon> New Post</a>
        </div>

        <div style="display: grid; gap: 20px;">
            <?php foreach ($announcements as $announce): ?>
                <div class="card" style="border-left: 4px solid var(--primary);">
                    <h3 style="margin-top: 0;"><?= htmlspecialchars($announce['title']) ?></h3>
                    <p style="color: #e0e0e0;"><?= nl2br(htmlspecialchars($announce['content'])) ?></p>
                    <div style="margin-top: 10px; font-size: 0.8rem; color: #888;">
                        Posted on <?= $announce['created_at'] ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>
