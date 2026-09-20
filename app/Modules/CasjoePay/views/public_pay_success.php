<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .success-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .icon-circle {
            width: 80px; height: 80px; background: #e0f2f1; color: #00897b;
            border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;
            font-size: 40px;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-circle">
            <ion-icon name="checkmark-outline"></ion-icon>
        </div>
        <h2 style="color: #333; margin-bottom: 10px;">Payment Successful!</h2>
        <p style="color: #666; margin-bottom: 30px;">
            You have successfully paid <strong><?= htmlspecialchars($currency ?? ($link['currency'] ?? '')) ?> <?= number_format($amount, 2) ?></strong>
            to <?= htmlspecialchars($title ?? ($link['title'] ?? 'Merchant')) ?>.
        </p>
        <?php if (!empty($link['slug'])): ?>
            <a href="/pay/link/<?= htmlspecialchars($link['slug']) ?>" class="btn">Return to Link</a>
        <?php elseif (!empty($returnUrl)): ?>
            <a href="<?= htmlspecialchars($returnUrl) ?>" class="btn">Continue</a>
        <?php else: ?>
            <a href="/" class="btn">Return Home</a>
        <?php endif; ?>
    </div>
</body>
</html>

