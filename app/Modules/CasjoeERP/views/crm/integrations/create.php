<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Integration | Casjoe BOS</title>
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
        .form-card .form-text { font-size: 0.8rem; color: #888; margin-top: -12px; margin-bottom: 18px; }
        .form-card hr { border: none; border-top: 1px solid #eee; margin: 25px 0; }
        .form-card .form-footer { display: flex; justify-content: space-between; margin-top: 20px; }

        .provider-cards { display: flex; gap: 12px; margin-bottom: 18px; }
        .provider-option { flex: 1; text-align: center; padding: 18px 10px; border-radius: 12px; border: 2px solid #eee; cursor: pointer; transition: all 0.2s; }
        .provider-option:hover { border-color: #4e73df; }
        .provider-option.selected { border-color: #4e73df; background: #f0f4ff; }
        .provider-option ion-icon { font-size: 2rem; display: block; margin: 0 auto 8px; }
        .provider-option span { font-size: 0.85rem; font-weight: 600; color: #333; }
    </style>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar { position: fixed; top: 0; left: -100%; height: 100%; z-index: 1000; transition: left 0.3s ease; width: 260px !important; background-color: #000066; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .provider-cards { flex-direction: column; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Connect New Integration</h2>
            <a href="/erp/crm/integrations" class="btn" style="background: #f8f9fa; color: #333; border: 1px solid #ddd;">
                <ion-icon name="arrow-back-outline" style="vertical-align: middle;"></ion-icon> Back
            </a>
        </div>

        <div class="form-card">
            <form action="/erp/crm/integrations/store" method="POST">
                <h3>Integration Details</h3>

                <label>Integration Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. My Facebook Page" required>

                <label>Provider</label>
                <input type="hidden" name="provider" id="providerInput" value="generic">
                <div class="provider-cards">
                    <div class="provider-option selected" onclick="selectProvider('generic', this)">
                        <ion-icon name="link-outline"></ion-icon>
                        <span>Generic</span>
                    </div>
                    <div class="provider-option" onclick="selectProvider('facebook', this)">
                        <ion-icon name="logo-facebook" style="color: #1877f2;"></ion-icon>
                        <span>Facebook</span>
                    </div>
                    <div class="provider-option" onclick="selectProvider('whatsapp', this)">
                        <ion-icon name="logo-whatsapp" style="color: #25d366;"></ion-icon>
                        <span>WhatsApp</span>
                    </div>
                </div>

                <hr>
                <h3>Automation Rules</h3>

                <label>Assign New Leads To</label>
                <select name="assigned_to" class="form-control">
                    <option value="">-- Unassigned --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Default Pipeline Stage</label>
                <select name="stage_id" class="form-control">
                    <option value="">-- Default (New) --</option>
                    <?php foreach ($stages as $stage): ?>
                        <option value="<?= $stage['id'] ?>"><?= htmlspecialchars($stage['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="form-footer">
                    <a href="/erp/crm/integrations" class="btn" style="background: #e9ecef; color: #495057;">Cancel</a>
                    <button type="submit" class="btn btn-brand" style="color: white;">Create Integration</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function selectProvider(value, el) {
    document.getElementById('providerInput').value = value;
    document.querySelectorAll('.provider-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
}
</script>
</body>
</html>
