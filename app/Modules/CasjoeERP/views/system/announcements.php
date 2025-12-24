<!DOCTYPE html>
<html lang="en">
<head>
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
