<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moniepoint Settings | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500;}
        .form-group input[type="text"], .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-color);
            color: var(--text-color);
        }
        .settings-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        .btn-primary { background: #007bff; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1>Moniepoint POS Integration</h1>
            </div>

            <div class="settings-card">
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div style="padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
                    </div>
                <?php endif; ?>

                <form action="/casjoe-erp/settings/moniepoint" method="POST">
                    <div class="form-group">
                        <label>Moniepoint Client ID</label>
                        <input type="text" name="client_id" value="<?php echo htmlspecialchars($integration['client_id'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Moniepoint Client Secret</label>
                        <input type="password" name="client_secret" value="<?php echo htmlspecialchars($integration['client_secret'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Terminal Serial Number</label>
                        <input type="text" name="terminal_serial" value="<?php echo htmlspecialchars($integration['terminal_serial'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group" style="display:flex; align-items:center; gap: 10px;">
                        <input type="checkbox" name="is_active" id="is_active" <?php echo (isset($integration['is_active']) && $integration['is_active']) ? 'checked' : ''; ?>>
                        <label for="is_active" style="margin:0;">Enable Integration</label>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
