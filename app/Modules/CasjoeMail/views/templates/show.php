<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Template | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .preview-container {
            background: white;
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            min-height: 400px;
            box-shadow: var(--glass-shadow);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'mail_templates'; include dirname(__DIR__) . '/partials/sidebar_mail.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="/mail/templates" class="btn-sm" style="color: var(--text-color);"><ion-icon name="arrow-back-outline"></ion-icon> Back</a>
                    <h1>Preview: <?= htmlspecialchars($template['name']) ?></h1>
                </div>
                <div>
                     <a href="/mail/templates/edit?id=<?= $template['id'] ?>" class="btn">Edit Template</a>
                </div>
            </div>

            <!-- Preview Area -->
            <div class="preview-container">
                <iframe srcdoc="<?= htmlspecialchars($template['content']) ?>" style="width: 100%; height: 600px; border: none; background: #fff;"></iframe>
            </div>
        </main>
    </div>
</body>
</html>
