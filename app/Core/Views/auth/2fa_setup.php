<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Setup 2FA | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center;">
<div class="card" style="width: 100%; max-width: 450px; text-align: center;">
    <h2>Setup 2FA</h2>
    <p style="color: #aaa; margin-bottom: 20px;">Scan this QR code with your authenticator app.</p>
    
    <div style="background: white; padding: 10px; display: inline-block; border-radius: 10px; margin-bottom: 20px;">
        <img src="<?= $qrCodeUrl ?>" alt="QR Code">
    </div>

    <p style="font-family: monospace; color: var(--secondary); margin-bottom: 20px;">Secret: <?= $secret ?></p>

    <form method="POST" action="/2fa/setup/verify">
        <input type="hidden" name="secret" value="<?= $secret ?>">
        <div class="form-group">
            <label>Enter Code to Confirm</label>
            <input type="text" name="code" class="form-control" style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;" placeholder="000000" maxlength="6" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Verify & Enable</button>
    </form>
    
    <a href="/dashboard" style="display: block; margin-top: 15px; color: #666; text-decoration: none;">Skip for now</a>
</div>
</body>
</html>
