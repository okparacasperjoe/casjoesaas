<?php $active = 'mail_audiences'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audiences | Casjoe Mail</title>
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
                        <h1>Audiences</h1>
                        <p class="subtitle">Manage your subscriber lists and contacts.</p>
                    </div>
                    <a href="/mail/lists/create" class="hero-btn">
                        <ion-icon name="add-outline"></ion-icon> Create Audience
                    </a>
                </div>
            </div>

            <div class="erp-content">
                <div class="section-header">
                    <ion-icon name="people-outline"></ion-icon> All Audiences
                </div>

                <div class="module-card" style="padding: 0; min-height: 200px;">
                    <?php if (empty($lists)): ?>
                        <div style="text-align: center; padding: 40px; color: var(--erp-text-muted);">
                            <ion-icon name="people-outline" style="font-size: 3rem; margin-bottom: 10px;"></ion-icon>
                            <p style="margin-bottom:20px; color:white;">No audiences found. Create a list to start adding subscribers.</p>
                            <a href="/mail/lists/create" class="hero-btn">Create Audience</a>
                        </div>
                    <?php else: ?>
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Subscribers</th>
                                    <th>Created</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lists as $list): ?>
                                    <tr>
                                        <td style="font-weight: 500; color: white;"><?= htmlspecialchars($list['name']) ?></td>
                                        <td><?= $list['subscriber_count'] ?></td>
                                        <td style="font-size: 0.9rem;"><?= date('M d, Y', strtotime($list['created_at'])) ?></td>
                                        <td style="text-align: right; display: flex; justify-content: flex-end; gap: 10px; align-items:center;">
                                            <a href="/mail/lists/view?id=<?= $list['id'] ?>" class="btn-icon" style="color:var(--erp-gold);" title="Manage Audience">
                                                <ion-icon name="settings-outline"></ion-icon>
                                            </a>
                                            <button class="btn-icon danger" title="Delete"><ion-icon name="trash-outline"></ion-icon></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
