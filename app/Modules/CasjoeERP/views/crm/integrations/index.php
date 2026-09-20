<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Integrations | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .integration-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #eee;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        .integration-info { display: flex; align-items: center; gap: 15px; flex: 1; }
        .integration-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        .integration-icon.facebook { background: #e7f0ff; color: #1877f2; }
        .integration-icon.whatsapp { background: #e8f5e9; color: #25d366; }
        .integration-icon.generic { background: #f3f4f6; color: #6b7280; }
        .integration-meta h4 { margin: 0 0 4px 0; font-size: 1rem; color: #333; }
        .integration-meta p { margin: 0; font-size: 0.85rem; color: #888; }
        .webhook-url-box {
            display: flex; align-items: center; gap: 8px;
            background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;
            padding: 6px 12px; max-width: 350px; flex-shrink: 0;
        }
        .webhook-url-box input {
            background: transparent; border: none; outline: none;
            font-size: 0.8rem; color: #555; width: 100%; font-family: monospace;
        }
        .copy-btn {
            background: none; border: none; cursor: pointer; color: #4e73df;
            font-size: 1.1rem; padding: 4px;
        }
        .copy-btn:hover { color: #224abe; }
        .badge-active { background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-inactive { background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .empty-state {
            text-align: center; padding: 60px 20px; color: #999;
        }
        .empty-state ion-icon { font-size: 4rem; margin-bottom: 15px; color: #ddd; }
    </style>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed; top: 0; left: -100%; height: 100%;
                z-index: 1000; transition: left 0.3s ease;
                width: 260px !important; background-color: #000066;
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .integration-card { flex-direction: column; align-items: flex-start; }
            .webhook-url-box { max-width: 100%; width: 100%; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Integrations</h2>
            <a href="/erp/crm/integrations/create" class="btn btn-brand" style="color: white; font-weight: 600;">
                <ion-icon name="add-circle-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>
                New Integration
            </a>
        </div>

        <?php if (empty($integrations)): ?>
            <div class="card empty-state">
                <ion-icon name="link-outline"></ion-icon>
                <h3>No Integrations Yet</h3>
                <p>Connect Facebook, WhatsApp, or any external tool to automatically capture leads.</p>
                <a href="/erp/crm/integrations/create" class="btn btn-brand" style="color: white; margin-top: 15px;">Connect First Integration</a>
            </div>
        <?php else: ?>
            <?php foreach ($integrations as $integration): ?>
                <div class="integration-card">
                    <div class="integration-info">
                        <div class="integration-icon <?= $integration['provider'] ?>">
                            <?php if ($integration['provider'] == 'facebook'): ?>
                                <ion-icon name="logo-facebook"></ion-icon>
                            <?php elseif ($integration['provider'] == 'whatsapp'): ?>
                                <ion-icon name="logo-whatsapp"></ion-icon>
                            <?php else: ?>
                                <ion-icon name="link-outline"></ion-icon>
                            <?php endif; ?>
                        </div>
                        <div class="integration-meta">
                            <h4><?= htmlspecialchars($integration['name']) ?></h4>
                            <p><?= ucfirst($integration['provider']) ?> &middot;
                                <?php if ($integration['is_active']): ?>
                                    <span class="badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="webhook-url-box">
                        <input type="text" readonly value="<?= 'https://' . $_SERVER['HTTP_HOST'] . '/api/erp/crm/webhook/' . $integration['webhook_secret'] ?>" id="url-<?= $integration['id'] ?>">
                        <button class="copy-btn" onclick="copyUrl('url-<?= $integration['id'] ?>')" title="Copy URL">
                            <ion-icon name="copy-outline"></ion-icon>
                        </button>
                    </div>
                    <a href="/erp/crm/integrations/embed?id=<?= $integration['id'] ?>" class="btn btn-brand" style="color: white; font-size: 0.85rem;">
                        <ion-icon name="code-working-outline" style="vertical-align: middle;"></ion-icon> Get Embed Code
                    </a>
                    <a href="/erp/crm/integrations/edit?id=<?= $integration['id'] ?>" class="btn" style="background: #f8f9fa; color: #333; border: 1px solid #ddd;">
                        <ion-icon name="settings-outline" style="vertical-align: middle;"></ion-icon> Edit
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>

<script>
function copyUrl(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    document.execCommand('copy');
    
    const btn = input.nextElementSibling;
    const original = btn.innerHTML;
    btn.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon>';
    setTimeout(() => { btn.innerHTML = original; }, 2000);
}
</script>
</body>
</html>
