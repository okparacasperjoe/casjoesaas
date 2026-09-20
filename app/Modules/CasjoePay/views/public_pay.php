<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($link['title']) ?> | Secure Payment</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .checkout-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .checkout-header {
            margin-bottom: 30px;
        }
        .amount-display {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 10px 0;
        }
        .pay-btn {
            background: var(--primary);
            color: white;
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            border: none;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .pay-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9rem;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .powered-by {
            margin-top: 30px;
            font-size: 0.8rem;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="checkout-header">
        <div style="width: 60px; height: 60px; background: #eee; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; color: var(--primary);">
            <?= strtoupper(substr($link['title'], 0, 1)) ?>
        </div>
        <h2 style="margin: 0; color: #333;"><?= htmlspecialchars($link['title']) ?></h2>
        <p style="color: #666; font-size: 0.9rem;">Pay securely via Casjoe Pay</p>
    </div>

    <form method="POST" action="/pay/process/<?= $link['slug'] ?>">
        <?php if ($link['amount'] > 0): ?>
            <div class="amount-display"><?= $link['currency'] ?> <?= number_format($link['amount'], 2) ?></div>
            <input type="hidden" name="amount" value="<?= $link['amount'] ?>">
        <?php else: ?>
            <div class="input-group">
                <label>Amount (<?= $link['currency'] ?>)</label>
                <input type="number" name="amount" placeholder="Enter amount" required min="1" step="0.01">
            </div>
        <?php endif; ?>

        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="John Doe" required>
        </div>
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="john@example.com" required>
        </div>

        <button type="submit" class="pay-btn">Pay Now</button>
    </form>

    <div class="powered-by">
        Powered by <strong style="color: var(--primary);">Casjoe Pay</strong>
    </div>
</div>

</body>
</html>
