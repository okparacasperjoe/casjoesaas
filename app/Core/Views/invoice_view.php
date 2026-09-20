<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice | <?= htmlspecialchars($invoice['tenant_name']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body { background: #f8f9fa; color: #333; font-family: 'Inter', sans-serif; display: flex; justify-content: center; padding: 40px 20px; }
        .invoice-container { background: #fff; width: 100%; max-width: 700px; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .invoice-header { display: flex; justify-content: space-between; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .invoice-header h1 { margin: 0; color: #111; font-size: 2rem; }
        .invoice-meta { text-align: right; color: #666; }
        .invoice-amount { font-size: 2.5rem; font-weight: 700; color: #FFA600; margin: 20px 0; }
        .invoice-description { font-size: 1.1rem; color: #444; line-height: 1.6; margin-bottom: 40px; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-paid { background: #d4edda; color: #155724; }
        .pay-btn { background: #FFA600; color: #000; border: none; padding: 15px 30px; font-size: 1.1rem; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .pay-btn:hover { background: #ffb733; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,166,0,0.3); }
        .success-msg { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

<div class="invoice-container">
    <?php if(isset($_GET['success'])): ?>
        <div class="success-msg"><ion-icon name="checkmark-circle"></ion-icon> Payment processed successfully!</div>
    <?php endif; ?>

    <div class="invoice-header">
        <div>
            <h1>INVOICE</h1>
            <p style="margin: 5px 0 0 0; color: #666;">From: <strong><?= htmlspecialchars($invoice['tenant_name']) ?></strong></p>
        </div>
        <div class="invoice-meta">
            <p style="margin: 0;">Invoice #: INV-<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?></p>
            <p style="margin: 5px 0 0 0;">Date: <?= date('M j, Y', strtotime($invoice['created_at'])) ?></p>
            <p style="margin: 5px 0 0 0;">Due: <?= date('M j, Y', strtotime($invoice['due_date'])) ?></p>
        </div>
    </div>

    <div>
        <h3 style="color: #888; text-transform: uppercase; font-size: 0.9rem; margin-bottom: 5px;">Billed To</h3>
        <p style="font-size: 1.1rem; font-weight: 500; margin-top: 0;"><?= htmlspecialchars($invoice['client_email']) ?></p>
    </div>

    <div class="invoice-amount">
        <?= htmlspecialchars($invoice['currency']) ?> <?= number_format($invoice['amount'], 2) ?>
    </div>

    <div class="invoice-description">
        <strong>Description:</strong><br>
        <?= nl2br(htmlspecialchars($invoice['description'])) ?>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #eee; padding-top: 30px;">
        <div>
            <span class="status-badge status-<?= $invoice['status'] ?>">
                <?= ucfirst($invoice['status']) ?>
            </span>
        </div>
        <?php if($invoice['status'] === 'pending'): ?>
        <div style="width: 50%;">
            <form method="POST" action="/invoice/pay/<?= $invoice['token'] ?>">
                <button type="submit" class="pay-btn"><ion-icon name="card-outline"></ion-icon> Pay Securely</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
