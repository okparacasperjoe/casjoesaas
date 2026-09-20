<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Moniepoint POS Integration | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--text-color); }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-color);
            color: var(--text-color);
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .settings-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            max-width: 650px;
        }
        .btn-primary { 
            background: #0284c7; 
            color: #fff; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 1rem; 
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #0369a1; }
        .hint-text {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="/erp/settings" class="btn" style="padding: 8px 12px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                        <ion-icon name="arrow-back-outline"></ion-icon> Settings
                    </a>
                    <h2 style="margin: 0;">Moniepoint POS Integration</h2>
                </div>
            </div>

            <div class="settings-card">
                <?php if (isset($_SESSION['flash_message']) || isset($_GET['saved'])): ?>
                    <div style="padding: 12px 16px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 600;">
                        &#10003; <?= htmlspecialchars($_SESSION['flash_message'] ?? 'Moniepoint settings saved successfully!') ?>
                        <?php unset($_SESSION['flash_message']); ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
                    <h3 style="margin: 0 0 8px 0; font-size: 1.15rem;">Connect Moniepoint Smart POS</h3>
                    <p style="margin: 0; color: #64748b; font-size: 0.9rem; line-height: 1.5;">
                        Configure your Moniepoint API credentials to enable pushing direct payment requests to your physical POS terminal and recording incoming funds automatically in your ERP Finance ledger.
                    </p>
                </div>

                <form action="/erp/settings/moniepoint" method="POST">
                    <div class="form-group">
                        <label>Moniepoint Client ID</label>
                        <input type="text" name="client_id" class="form-control" value="<?= htmlspecialchars($integration['client_id'] ?? '') ?>" placeholder="e.g. CLI_1234567890" required>
                        <small class="hint-text">Found in your Moniepoint Business Dashboard under POS Terminal Configuration &gt; Developer / API Settings.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Moniepoint Client Secret</label>
                        <input type="password" name="client_secret" class="form-control" value="<?= htmlspecialchars($integration['client_secret'] ?? '') ?>" placeholder="Enter client secret" required>
                        <small class="hint-text">Your secret OAuth authentication key provided by Moniepoint.</small>
                    </div>

                    <div class="form-group">
                        <label>POS Terminal Serial Number</label>
                        <input type="text" name="terminal_serial" class="form-control" value="<?= htmlspecialchars($integration['terminal_serial'] ?? '') ?>" placeholder="e.g. MP12345678" required>
                        <small class="hint-text">The serial number printed on the back or in the settings of your Moniepoint Smart POS device.</small>
                    </div>

                    <div class="form-group" style="display:flex; align-items:center; gap: 10px; margin-top: 25px; padding: 15px; background: rgba(2, 132, 199, 0.05); border-radius: 8px; border: 1px solid rgba(2, 132, 199, 0.15);">
                        <input type="checkbox" name="is_active" id="is_active" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= (!empty($integration['is_active'])) ? 'checked' : '' ?>>
                        <label for="is_active" style="margin:0; cursor: pointer; font-size: 0.95rem;">Enable Moniepoint POS Terminal Payments in ERP</label>
                    </div>

                    <div style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Save Moniepoint Settings</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
