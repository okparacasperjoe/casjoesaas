<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <title>Verify 2FA | Casjoe Apps</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center;">
<div class="card" style="width: 100%; max-width: 400px; text-align: center;">
    <h2>Two-Factor Auth</h2>
    <p style="color: #aaa; margin-bottom: 20px;">Please enter the code from your authenticator app.</p>
    
    <?php if(isset($error)): ?>
        <div style="background: rgba(255, 0, 0, 0.1); color: #ff4444; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/2fa/verify">
        <div class="form-group">
            <input type="text" name="code" class="form-control" style="text-align: center; font-size: 2rem; letter-spacing: 10px;" placeholder="000000" maxlength="6" required autofocus>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Verify</button>
    </form>
</div>
</body>
</html>
