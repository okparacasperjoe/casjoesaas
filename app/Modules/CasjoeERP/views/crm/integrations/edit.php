<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Integration | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .form-card {
            background: #fff; border-radius: 12px; padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #eee;
            max-width: 600px;
        }
        .form-card h3 { margin: 0 0 25px 0; color: #333; font-size: 1.3rem; }
        .form-card label { display: block; font-weight: 600; color: #333; margin-bottom: 6px; font-size: 0.9rem; }
        .form-card .form-control { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; margin-bottom: 18px; }
        .form-card select.form-control { appearance: auto; }
        .form-card hr { border: none; border-top: 1px solid #eee; margin: 25px 0; }
        .form-card .form-footer { display: flex; justify-content: space-between; margin-top: 20px; }
        .webhook-display {
            background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 10px;
            padding: 15px; margin-bottom: 20px;
        }
        .webhook-display label { margin-bottom: 8px; }
        .webhook-display .url-row {
            display: flex; align-items: center; gap: 10px;
        }
        .webhook-display input {
            flex: 1; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px;
            font-size: 0.85rem; font-family: monospace; background: #fff;
        }
        .webhook-display .copy-btn {
            background: #4e73df; color: #fff; border: none; padding: 10px 16px;
            border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
            white-space: nowrap;
        }
        .webhook-display .copy-btn:hover { background: #224abe; }
        .webhook-display .hint { font-size: 0.8rem; color: #888; margin-top: 8px; }
        .toggle-switch { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
        .toggle-switch input[type="checkbox"] { width: 20px; height: 20px; }
        .danger-zone {
            border: 1px solid #f5c6cb; border-radius: 10px; padding: 15px;
            margin-top: 25px; background: #fff5f5;
        }
        .danger-zone h4 { color: #721c24; margin: 0 0 10px 0; font-size: 0.95rem; }
    </style>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar { position: fixed; top: 0; left: -100%; height: 100%; z-index: 1000; transition: left 0.3s ease; width: 260px !important; background-color: #000066; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .webhook-display .url-row { flex-direction: column; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Edit Integration</h2>
            <a href="/erp/crm/integrations" class="btn" style="background: #f8f9fa; color: #333; border: 1px solid #ddd;">
                <ion-icon name="arrow-back-outline" style="vertical-align: middle;"></ion-icon> Back
            </a>
        </div>

        <div class="form-card">
            <div class="webhook-display">
                <label>Your Webhook URL</label>
                <div class="url-row">
                    <input type="text" readonly value="<?= 'https://' . $_SERVER['HTTP_HOST'] . '/api/erp/crm/webhook/' . $integration['webhook_secret'] ?>" id="webhookUrl">
                    <button class="copy-btn" onclick="copyWebhook()">
                        <ion-icon name="copy-outline" style="vertical-align: middle; margin-right: 4px;"></ion-icon> Copy
                    </button>
                </div>
                <p class="hint">Paste this URL into your external service (Facebook Lead Ads, Zapier, etc).</p>
                <div style="margin-top: 15px;">
                    <a href="/erp/crm/integrations/embed?id=<?= $integration['id'] ?>" class="btn btn-brand" style="color: white; font-size: 0.9rem; width: 100%; display: inline-block; text-align: center;">
                        <ion-icon name="code-working-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Generate Form Embed Code
                    </a>
                </div>
            </div>

            <form action="/erp/crm/integrations/update" method="POST">
                <input type="hidden" name="id" value="<?= $integration['id'] ?>">

                <label>Integration Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($integration['name']) ?>" required>

                <div class="toggle-switch">
                    <input type="checkbox" name="is_active" id="isActive" <?= $integration['is_active'] ? 'checked' : '' ?>>
                    <label for="isActive" style="margin: 0; cursor: pointer;">Active</label>
                </div>

                <hr>
                <h3>Automation Rules</h3>

                <label>Assign New Leads To</label>
                <select name="assigned_to" class="form-control">
                    <option value="">-- Unassigned --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= ($config['assigned_to'] ?? '') == $user['id'] ? 'selected' : '' ?>><?= htmlspecialchars($user['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Default Pipeline Stage</label>
                <select name="stage_id" class="form-control">
                    <option value="">-- Default (New) --</option>
                    <?php foreach ($stages as $stage): ?>
                        <option value="<?= $stage['id'] ?>" <?= ($config['stage_id'] ?? '') == $stage['id'] ? 'selected' : '' ?>><?= htmlspecialchars($stage['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="form-footer">
                    <a href="/erp/crm/integrations" class="btn" style="background: #e9ecef; color: #495057;">Cancel</a>
                    <button type="submit" class="btn btn-brand" style="color: white;">Save Changes</button>
                </div>
            </form>

            <div class="danger-zone">
                <h4>Danger Zone</h4>
                <form action="/erp/crm/integrations/delete" method="POST" onsubmit="return confirm('Are you sure? This will permanently delete this integration and its webhook URL.');">
                    <input type="hidden" name="id" value="<?= $integration['id'] ?>">
                    <button type="submit" class="btn" style="background: #dc3545; color: white; font-size: 0.85rem;">Delete Integration</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function copyWebhook() {
    const input = document.getElementById('webhookUrl');
    input.select();
    document.execCommand('copy');

    const btn = input.closest('.url-row').querySelector('.copy-btn');
    const original = btn.innerHTML;
    btn.innerHTML = '<ion-icon name="checkmark-outline" style="vertical-align: middle; margin-right: 4px;"></ion-icon> Copied!';
    setTimeout(() => { btn.innerHTML = original; }, 2000);
}
</script>
</body>
</html>
