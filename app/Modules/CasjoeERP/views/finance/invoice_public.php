<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?> - <?= htmlspecialchars($companyName) ?></title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f6f8; margin: 0; padding: 40px 20px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .company-info h2 { margin: 0; color: #000066; }
        .invoice-details { text-align: right; }
        .invoice-details h1 { margin: 0 0 10px; font-size: 2rem; color: #555; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; background: #eee; color: #555; }
        .paid { background: #d4edda; color: #155724; }
        .draft { background: #fff3cd; color: #856404; }
        .overdue { background: #fee2e2; color: #991b1b; }
        
        .client-info { margin-bottom: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { text-align: left; background: #f8f9fa; padding: 12px; border-bottom: 2px solid #eee; font-weight: 600; color: #555; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        
        .totals { float: right; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 1.1rem; }
        .grand-total { font-weight: bold; font-size: 1.4rem; color: #000066; border-top: 2px solid #eee; padding-top: 15px; margin-top: 10px; }
        
        .actions { clear: both; margin-top: 50px; text-align: center; }
        .btn-pay { background: #000066; color: white; padding: 14px 36px; border: none; border-radius: 8px; font-size: 1.1rem; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.2s; font-weight: 600; }
        .btn-pay:hover { background: #000044; }
        
        @media print {
            body { background: white; padding: 0; }
            .invoice-box { box-shadow: none; border: none; padding: 20px; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <?php if (isset($_GET['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            Payment Successful! Thank you for your payment.
        </div>
    <?php endif; ?>

    <div class="header">
        <div class="company-info">
            <?php if (!empty($companyLogo)): ?>
                <div style="margin-bottom: 12px;">
                    <img src="<?= htmlspecialchars($companyLogo) ?>" alt="Company Logo" style="max-height: 65px; max-width: 180px; object-fit: contain;">
                </div>
            <?php endif; ?>
            <h2><?= htmlspecialchars($companyName) ?></h2>
            <?php if (!empty($tenantEmail)): ?>
                <p style="margin: 5px 0; color: #666; font-size: 0.9rem;">
                    <ion-icon name="mail-outline" style="vertical-align: middle;"></ion-icon> <?= htmlspecialchars($tenantEmail) ?>
                </p>
            <?php endif; ?>
            <?php if (!empty($tenantPhone)): ?>
                <p style="margin: 5px 0; color: #666; font-size: 0.9rem;">
                    <ion-icon name="call-outline" style="vertical-align: middle;"></ion-icon> <?= htmlspecialchars($tenantPhone) ?>
                </p>
            <?php endif; ?>
            <?php if (!empty($tenantAddress)): ?>
                <p style="margin: 5px 0; color: #666; font-size: 0.9rem;">
                    <ion-icon name="location-outline" style="vertical-align: middle;"></ion-icon> <?= htmlspecialchars($tenantAddress) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="invoice-details">
            <h1>INVOICE</h1>
            <p><strong>Invoice #:</strong> <?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?></p>
            <p><strong>Date:</strong> <?= $invoice['issue_date'] ?></p>
            <p><strong>Due Date:</strong> <?= $invoice['due_date'] ?></p>
            <span class="status-badge <?= $invoice['status'] ?>">
                <?= ucfirst($invoice['status']) ?>
            </span>
        </div>
    </div>

    <div class="client-info">
        <strong>Bill To / Debtor:</strong><br>
        <span style="font-size: 1.1rem; font-weight: 600;"><?= htmlspecialchars($invoice['client_name']) ?></span><br>
        <?php if (!empty($invoice['client_email'])): ?>
            <span style="color: #666;"><?= htmlspecialchars($invoice['client_email']) ?></span><br>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td class="text-right"><?= $item['quantity'] ?></td>
                <td class="text-right"><?= number_format($item['unit_price'], 2) ?></td>
                <td class="text-right"><?= number_format($item['amount'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row grand-total">
            <span>Total:</span>
            <span><?= htmlspecialchars($invoice['currency'] ?? 'NGN') ?> <?= number_format($invoice['total_amount'], 2) ?></span>
        </div>
    </div>

    <?php if (!empty($invoice['notes'])): ?>
        <div style="clear: both; margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; color: #64748b; font-size: 0.9rem;">
            <strong>Notes / Instructions:</strong><br>
            <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
        </div>
    <?php endif; ?>

    <?php if ($invoice['status'] !== 'paid'): ?>
        <div class="actions">
            <!-- Form for simulation of payment -->
            <form action="/invoice/<?= $invoice['uuid'] ?>/pay" method="POST" style="display: inline-block;">
                <button type="submit" class="btn-pay">Pay Invoice Now</button>
            </form>
            <div style="margin-top: 12px;">
                <button type="button" onclick="window.print()" style="background: none; border: 1px solid #cbd5e1; padding: 8px 18px; cursor: pointer; border-radius: 6px; font-size: 14px; color: #475569;">
                    <ion-icon name="print-outline" style="vertical-align: middle;"></ion-icon> Print Bill / Receipt
                </button>
            </div>
            <p style="margin-top: 12px; font-size: 0.85rem; color: #777;">Secured by Casjoe Pay</p>
        </div>
    <?php else: ?>
        <div class="actions">
            <h3 style="color: #15803d; margin-bottom: 10px;">✓ This invoice has been paid.</h3>
            <button onclick="window.print()" style="background: none; border: 1px solid #ccc; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-size: 14px;">
                <ion-icon name="print-outline" style="vertical-align: middle;"></ion-icon> Print Receipt
            </button>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
