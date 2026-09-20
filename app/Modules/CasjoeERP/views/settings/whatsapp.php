<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Automation | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .wa-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
        }
        .qr-placeholder {
            width: 250px;
            height: 250px;
            background: #f1f5f9;
            border-radius: 12px;
            margin: 30px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #cbd5e1;
            padding: 10px;
        }
        .qr-placeholder img { width: 100%; height: 100%; object-fit: contain; }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .status-connected { background: #d1fae5; color: #065f46; }
        .status-disconnected { background: #fee2e2; color: #991b1b; }
        .status-connecting { background: #fef3c7; color: #b45309; }
        
        .steps { text-align: left; margin-top: 30px; font-size: 0.95rem; color: #475569; }
        .steps li { margin-bottom: 10px; }
        
        .btn-danger { background: #ef4444; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-refresh { background: #000066; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed; top: 0; left: -260px; height: 100%; z-index: 1000; transition: 0.3s; width: 260px; background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .wa-card { margin: 20px; padding: 20px; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>WhatsApp Automation</h2>
        </div>

        <div style="background: linear-gradient(135deg, #25D366, #128C7E); color: white; padding: 20px 30px; margin: 20px auto; max-width: 800px; border-radius: 12px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);">
            <ion-icon name="rocket-outline" style="font-size: 2.5rem; flex-shrink: 0;"></ion-icon>
            <div style="text-align: left;">
                <h3 style="margin: 0 0 5px 0; font-size: 1.3rem;">Coming Soon</h3>
                <p style="margin: 0; opacity: 0.95; font-size: 1rem;">WhatsApp Automation is currently undergoing final testing and will be fully available in our next update!</p>
            </div>
        </div>

        <div class="wa-card">
            <ion-icon name="logo-whatsapp" style="font-size: 4rem; color: #25D366;"></ion-icon>
            <h2 style="margin: 10px 0 5px 0; color: #000066;">Connect WhatsApp</h2>
            <p style="color: #64748b; margin-bottom: 20px;">Link your business WhatsApp to automatically send invoices and alerts.</p>

            <?php if ($connectionStatus === 'connected'): ?>
                <div class="status-badge status-connected">
                    <ion-icon name="checkmark-circle"></ion-icon> Connected Active
                </div>
                <p>Your WhatsApp is successfully linked to Casjoe!</p>
                <form action="/erp/whatsapp/logout" method="POST" style="margin-top: 30px;">
                    <button type="submit" class="btn-danger">Disconnect WhatsApp</button>
                </form>
            <?php else: ?>
                <div class="status-badge <?= $connectionStatus === 'connecting' ? 'status-connecting' : 'status-disconnected' ?>">
                    <?= $connectionStatus === 'connecting' ? 'Waiting for Scan...' : 'Disconnected' ?>
                </div>

                <div class="qr-placeholder">
                    <?php if ($qrCodeBase64): ?>
                        <img src="<?= $qrCodeBase64 ?>" alt="QR Code">
                    <?php else: ?>
                        <p style="color: #94a3b8;">QR Code not available. Check API connection.</p>
                    <?php endif; ?>
                </div>

                <ul class="steps">
                    <li>1. Open WhatsApp on your phone.</li>
                    <li>2. Tap <strong>Menu</strong> (⋮) or <strong>Settings</strong> (⚙️) and select <strong>Linked Devices</strong>.</li>
                    <li>3. Tap on <strong>Link a Device</strong>.</li>
                    <li>4. Point your phone to this screen to capture the code.</li>
                </ul>

                <div style="margin-top: 30px;">
                    <a href="/erp/whatsapp" class="btn-refresh"><ion-icon name="refresh-outline"></ion-icon> Refresh Status</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
