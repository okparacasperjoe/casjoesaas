<?php $active = 'mail_templates'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Templates | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mail_app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar_mail.php'; ?>

        <main class="erp-main">
            <!-- Mobile Nav -->
            <?php include __DIR__ . '/../../../../Core/Views/partials/mobile_nav.php'; ?>

            <div class="erp-hero">
                <div class="erp-hero-top">
                    <div>
                        <h1>Email Templates</h1>
                        <p class="subtitle">Design beautiful emails for your campaigns and automations.</p>
                    </div>
                    <a href="/mail/templates/create" class="hero-btn">
                        <ion-icon name="add-outline"></ion-icon> Create Template
                    </a>
                </div>
            </div>

            <div class="erp-content">
                <div class="section-header">
                    <ion-icon name="duplicate-outline"></ion-icon> Template Library
                </div>

                <div class="module-card" style="padding: 0; min-height: 200px; background: transparent; border: none; box-shadow: none;">
                    <?php if (empty($templates)): ?>
                        <div class="module-card" style="text-align: center; padding: 40px; color: var(--erp-text-muted);">
                            <ion-icon name="duplicate-outline" style="font-size: 3rem; margin-bottom: 10px;"></ion-icon>
                            <p style="margin-bottom:20px; color:white;">No templates found. Start designing your first template!</p>
                            <a href="/mail/templates/create" class="hero-btn">Create Template</a>
                        </div>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
                            <?php foreach ($templates as $template): ?>
                                <div class="module-card" style="padding: 15px;">
                                    <h3 style="margin: 0 0 15px 0; font-size: 1.1rem; color: white;"><?= htmlspecialchars($template['name']) ?></h3>
                                    <div style="width: 100%; padding-top: 100%; position: relative; overflow: hidden; background: #fff; border: 1px solid var(--erp-border); border-radius: 8px; margin-bottom: 15px;">
                                        <iframe 
                                            srcdoc="<?= htmlspecialchars($template['content']) ?>" 
                                            style="position: absolute; top: 0; left: 0; width: 400%; height: 400%; border: 0; transform: scale(0.25); transform-origin: 0 0; pointer-events: none; background: #fff;"
                                            scrolling="no"
                                        ></iframe>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display:flex; gap:10px;">
                                            <a href="/mail/templates/view?id=<?= $template['id'] ?>" class="btn-icon" style="color:var(--erp-gold);" title="View"><ion-icon name="eye-outline"></ion-icon></a>
                                            <a href="/mail/templates/edit?id=<?= $template['id'] ?>" class="btn-icon" title="Edit"><ion-icon name="create-outline"></ion-icon></a>
                                        </div>
                                        <form action="/mail/templates/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Delete this template?');">
                                            <input type="hidden" name="id" value="<?= $template['id'] ?>">
                                            <button type="submit" class="btn-icon danger" title="Delete"><ion-icon name="trash-outline"></ion-icon></button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
