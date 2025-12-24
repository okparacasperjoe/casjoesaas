<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?> - <?= htmlspecialchars($companyName) ?></title>
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
        
        .client-info { margin-bottom: 40px; border-top: 1px solid #eee; padding-top: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { text-align: left; background: #f8f9fa; padding: 12px; border-bottom: 2px solid #eee; font-weight: 600; color: #555; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        
        .totals { float: right; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 1.1rem; }
        .grand-total { font-weight: bold; font-size: 1.4rem; color: #000066; border-top: 2px solid #eee; padding-top: 15px; margin-top: 10px; }
        
        .actions { clear: both; margin-top: 60px; text-align: center; }
        .btn-pay { background: #000066; color: white; padding: 15px 40px; border: none; border-radius: 8px; font-size: 1.2rem; cursor: pointer; text-decoration: none; display: inline-block; transition: background 0.2s; }
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
            Payment Successful! Thank you for your business.
        </div>
    <?php endif; ?>

    <div class="header">
        <div class="company-info">
            <h2><?= htmlspecialchars($companyName) ?></h2>
            <p>Generated via Casjoe ERP</p>
        </div>
        <div class="invoice-details">
            <h1>INVOICE</h1>
            <p>#<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?></p>
            <p>Date: <?= date('M d, Y', strtotime($invoice['issue_date'])) ?></p>
            <p>Due: <?= date('M d, Y', strtotime($invoice['due_date'])) ?></p>
            <span class="status-badge <?= $invoice['status'] ?>">
                <?= $invoice['status'] ?>
            </span>
        </div>
    </div>

    <div class="client-info">
        <strong>Bill To:</strong><br>
        <?= htmlspecialchars($invoice['client_name']) ?><br>
        <?= htmlspecialchars($invoice['client_email']) ?>
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
            <span>NGN <?= number_format($invoice['total_amount'], 2) ?></span>
        </div>
    </div>

    <?php if ($invoice['status'] !== 'paid'): ?>
        <div class="actions">
            <!-- Form for simulation of payment -->
            <form action="/invoice/<?= $invoice['uuid'] ?>/pay" method="POST">
                <button type="submit" class="btn-pay">Pay Invoice Now</button>
            </form>
            <p style="margin-top: 15px; font-size: 0.9rem; color: #777;">Secured by Casjoe Pay</p>
        </div>
    <?php else: ?>
        <div class="actions">
            <h3 style="color: green;">This invoice has been paid.</h3>
            <button onclick="window.print()" style="background:none; border: 1px solid #ccc; padding: 10px 20px; cursor: pointer; border-radius: 5px;">Print Receipt</button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($invoice['notes'])): ?>
        <div style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; color: #777; font-size: 0.9rem;">
            <strong>Notes:</strong><br>
            <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
