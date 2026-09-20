<?php $active = 'mail_settings'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Settings | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mail_app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .form-control {
            color: white !important;
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid var(--erp-border) !important;
            padding: 12px !important;
            border-radius: 8px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .form-control:focus {
            outline: none !important;
            border-color: var(--erp-gold) !important;
            background: rgba(255,255,255,0.1) !important;
        }
        .form-label {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: var(--erp-text-muted) !important;
            display: block !important;
            margin-bottom: 8px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar_mail.php'; ?>

        <main class="erp-main">
            <!-- Mobile Nav -->
            <?php include __DIR__ . '/../../../Core/Views/partials/mobile_nav.php'; ?>

            <div class="erp-hero">
                <div class="erp-hero-top">
                    <div>
                        <h1>Mail Configuration</h1>
                        <p class="subtitle">Manage sender identities and defaults.</p>
                    </div>
                </div>
            </div>

            <div class="erp-content">
                <div class="module-card" style="max-width: 600px; padding: 40px;">
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'saved'): ?>
                        <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 10px;">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            Settings saved successfully!
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/mail/settings/save">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <ion-icon name="person-circle-outline" style="color: var(--erp-gold); font-size: 1.5rem;"></ion-icon>
                            <h3 style="margin: 0; color: white; font-size: 1.2rem;">Sender Profile</h3>
                        </div>
                        <p style="margin: 0 0 30px 0; color: var(--erp-text-muted); font-size: 0.95rem; line-height: 1.5;">
                            This is how you will appear in your subscribers' inboxes. The delivery server is managed by the system administrator.
                        </p>

                        <div style="margin-bottom: 25px;">
                            <label class="form-label">From Name</label>
                            <input type="text" name="from_name" class="form-control" value="<?= htmlspecialchars($settings['from_name'] ?? '') ?>" placeholder="e.g. Casjoe Sales Team" required>
                        </div>

                        <div style="margin-bottom: 35px;">
                            <label class="form-label">From Email</label>
                            <input type="email" name="from_email" class="form-control" value="<?= htmlspecialchars($settings['from_email'] ?? '') ?>" placeholder="e.g. sales@yourdomain.com" required>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; margin-top: 40px; border-top: 1px solid var(--erp-border); padding-top: 30px;">
                            <ion-icon name="server-outline" style="color: var(--erp-gold); font-size: 1.5rem;"></ion-icon>
                            <h3 style="margin: 0; color: white; font-size: 1.2rem;">Custom SMTP Settings (Optional)</h3>
                        </div>
                        <p style="margin: 0 0 30px 0; color: var(--erp-text-muted); font-size: 0.95rem; line-height: 1.5;">
                            If you want to send emails from your own server, fill in your SMTP credentials here. Leave blank to use our default Casjoe SMTP server.
                        </p>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                            <div>
                                <label class="form-label">SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="e.g. smtp.yourserver.com">
                            </div>
                            <div>
                                <label class="form-label">SMTP Port</label>
                                <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" placeholder="e.g. 587">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                            <div>
                                <label class="form-label">SMTP Username</label>
                                <input type="text" name="smtp_username" class="form-control" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" placeholder="Username">
                            </div>
                            <div>
                                <label class="form-label">SMTP Password</label>
                                <input type="password" name="smtp_password" class="form-control" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" placeholder="Password">
                            </div>
                        </div>

                        <div style="margin-bottom: 35px;">
                            <label class="form-label">Encryption</label>
                            <select name="smtp_encryption" class="form-control" style="appearance: none;">
                                <option value="" <?= empty($settings['smtp_encryption']) ? 'selected' : '' ?>>None</option>
                                <option value="tls" <?= ($settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>

                        <button type="submit" class="module-btn" style="width: auto; padding: 12px 30px; font-size: 1rem; margin-top:0;">
                            Save Changes <ion-icon name="save-outline"></ion-icon>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
