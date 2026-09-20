<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Bank Transfer | Billing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .transfer-container { max-width: 500px; margin: 50px auto; background: var(--glass-bg); padding: 40px; border-radius: 20px; border: 1px solid var(--glass-border); text-align: center; }
        .bank-details { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; margin: 30px 0; text-align: left; }
        .detail-row { margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
        .detail-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .label { color: var(--secondary); font-size: 0.9rem; display: block; margin-bottom: 5px; }
        .value { color: white; font-size: 1.1rem; font-weight: 600; font-family: monospace; display: flex; justify-content: space-between; align-items: center;}
        .instruction-box { background: rgba(255, 166, 0, 0.1); border: 1px solid rgba(255, 166, 0, 0.3); color: #fad390; padding: 15px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 30px; text-align: left; }
    </style>
</head>
<body>
<div class="app-container">
    <div class="transfer-container">
        <ion-icon name="business" style="font-size: 4rem; color: #FFA600; margin-bottom: 20px;"></ion-icon>
        <h2>Bank Transfer Payment</h2>
        <p style="color: #ccc;">Please transfer <strong><?= htmlspecialchars($currency . ' ' . number_format((float)$amount, 2)) ?></strong> to the account below.</p>

        <div class="bank-details">
            <div class="detail-row">
                <span class="label">Bank Name</span>
                <span class="value"><?= htmlspecialchars($settings['bank_name'] ?? 'Not Configured') ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Account Name</span>
                <span class="value"><?= htmlspecialchars($settings['account_name'] ?? 'Not Configured') ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Account Number</span>
                <span class="value">
                    <?= htmlspecialchars($settings['account_number'] ?? '0000000000') ?>
                    <ion-icon name="copy-outline" style="cursor: pointer; opacity: 0.7;" onclick="navigator.clipboard.writeText('<?= $settings['account_number'] ?? '' ?>'); alert('Copied!');"></ion-icon>
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Reference Code</span>
                <span class="value"><?= htmlspecialchars($ref) ?> <ion-icon name="copy-outline" style="cursor: pointer; opacity: 0.7;" onclick="navigator.clipboard.writeText('<?= $ref ?>'); alert('Copied!');"></ion-icon></span>
            </div>
        </div>

        <?php if (!empty($settings['bank_instructions'])): ?>
            <div class="instruction-box">
                <strong><ion-icon name="information-circle-outline" style="vertical-align: middle;"></ion-icon> Instructions:</strong><br>
                <?= nl2br(htmlspecialchars($settings['bank_instructions'])) ?>
                <br><br>
                IMPORTANT: Please include the <strong>Reference Code</strong> in your transfer description/narration.
            </div>
        <?php endif; ?>

        <a href="/billing?msg=We+have+received+your+payment+request.+Your+subscription%2Ftokens+will+be+credited+once+the+super+admin+approves+the+transfer." class="btn" style="display: block; width: 100%; text-decoration: none; box-sizing: border-box;">I have made the transfer</a>
        <br>
        <a href="/billing" style="color: #666; font-size: 0.9rem; text-decoration: none;">Cancel Payment</a>
    </div>
</div>
</body>
</html>
