<?php $active = 'forms'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Forms | Casjoe Mail</title>
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
                        <h1>Signup Forms</h1>
                        <p class="subtitle">Generate embed codes and shareable links for your audiences.</p>
                    </div>
                </div>
            </div>

            <div class="erp-content">
                <div class="section-header">
                    <ion-icon name="list-outline"></ion-icon> Available Forms
                </div>

                <div class="module-card" style="padding: 0; min-height: 200px;">
                    <?php if (empty($lists)): ?>
                        <div style="text-align: center; padding: 40px; color: var(--erp-text-muted);">
                            <ion-icon name="list-outline" style="font-size: 3rem; margin-bottom: 10px;"></ion-icon>
                            <p style="margin-bottom:20px; color:white;">No audiences found. Create an audience first to generate a form.</p>
                            <a href="/mail/lists/create" class="hero-btn">Create Audience</a>
                        </div>
                    <?php else: ?>
                        <div style="padding: 25px; display: grid; gap: 20px;">
                            <?php foreach ($lists as $list): ?>
                                <div style="border: 1px solid var(--erp-border); padding: 25px; border-radius: 12px; background: rgba(0,0,0,0.2);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                        <h3 style="margin: 0; color: white;"><?= htmlspecialchars($list['name']) ?></h3>
                                        <a href="/mail/subscribe?id=<?= $list['id'] ?>" target="_blank" class="hero-btn" style="text-decoration: none; padding: 8px 16px; font-size: 0.85rem;">
                                            <ion-icon name="open-outline"></ion-icon> View Live Form
                                        </a>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        <div>
                                            <label style="font-size: 11px; font-weight: bold; color: var(--erp-text-muted); display: block; margin-bottom: 5px;">SHAREABLE LINK</label>
                                            <input type="text" readonly value="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/mail/subscribe?id=' . $list['id'] ?>" 
                                                class="form-control" style="font-size: 13px; color: white; background: rgba(255,255,255,0.05); border: 1px solid var(--erp-border); padding: 10px; border-radius: 8px; width: 100%; box-sizing:border-box;">
                                        </div>
                                        <div>
                                            <label style="font-size: 11px; font-weight: bold; color: var(--erp-text-muted); display: block; margin-bottom: 5px;">EMBED CODE</label>
                                            <textarea readonly class="form-control" style="height: 40px; font-size: 11px; color: white; background: rgba(255,255,255,0.05); border: 1px solid var(--erp-border); padding: 10px; border-radius: 8px; resize: none; width: 100%; box-sizing:border-box;"><iframe src="http://<?= $_SERVER['HTTP_HOST'] ?>/mail/subscribe?id=<?= $list['id'] ?>" width="100%" height="500" frameborder="0"></iframe></textarea>
                                        </div>
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
