<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Invoices & Unpaid Bills | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="top-bar">
    <div>
        <h2>Invoices & Bills</h2>
        <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Manage customer bills, track unpaid debts, and share payment links via WhatsApp / SMS</p>
    </div>
    <a href="/erp/finance/invoices/create" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
        <ion-icon name="add-outline"></ion-icon> Create Invoice / Bill
    </a>
</div>

<div class="card">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #eee;">
                <th style="padding: 10px;">Invoice #</th>
                <th style="padding: 10px;">Client / Debtor</th>
                <th style="padding: 10px;">Issue Date</th>
                <th style="padding: 10px;">Due Date</th>
                <th style="padding: 10px;">Amount</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoices as $invoice): 
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'app.casjoe.com';
                $publicUrl = $scheme . '://' . $host . '/invoice/' . $invoice['uuid'];
                $waText = urlencode("Hello " . $invoice['client_name'] . ", here is your invoice/bill #" . $invoice['id'] . " for " . $currencySymbol . number_format($invoice['total_amount'], 2) . " due on " . $invoice['due_date'] . ". You can view and pay online here: " . $publicUrl);
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-family: monospace; font-weight: 600;">#<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td style="padding: 10px;">
                        <div style="font-weight: 600;"><?= htmlspecialchars($invoice['client_name']) ?></div>
                        <?php if (!empty($invoice['client_email'])): ?>
                            <div style="font-size: 11px; color: #64748b;"><?= htmlspecialchars($invoice['client_email']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px; color: #666; font-size: 13px;"><?= $invoice['issue_date'] ?></td>
                    <td style="padding: 10px; color: #666; font-size: 13px;"><?= $invoice['due_date'] ?></td>
                    <td style="padding: 10px; font-family: monospace; font-weight: 600;"><?= $currencySymbol ?> <?= number_format($invoice['total_amount'], 2) ?></td>
                    <td style="padding: 10px;">
                        <span class="badge badge-<?= $invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'overdue' ? 'danger' : 'warning') ?>" style="padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                            <?= ucfirst($invoice['status']) ?>
                        </span>
                    </td>
                    <td style="padding: 10px; text-align: right; white-space: nowrap;">
                        <!-- WhatsApp Share -->
                        <a href="https://api.whatsapp.com/send?text=<?= $waText ?>" target="_blank" class="btn btn-sm btn-outline" style="color: #25D366; border-color: #25D366; padding: 4px 8px; font-size: 12px; margin-right: 4px;" title="Send via WhatsApp">
                            <ion-icon name="logo-whatsapp" style="vertical-align: middle;"></ion-icon> WhatsApp
                        </a>

                        <!-- Copy Link -->
                        <button type="button" class="btn btn-sm btn-outline" style="padding: 4px 8px; font-size: 12px; margin-right: 4px;" onclick="copyInvoiceLink('<?= $publicUrl ?>', this)" title="Copy Payment Link">
                            <ion-icon name="copy-outline" style="vertical-align: middle;"></ion-icon> Copy Link
                        </button>

                        <!-- View Internal / Print -->
                        <a href="/erp/finance/invoice/view?uuid=<?= $invoice['uuid'] ?>" class="btn btn-sm btn-secondary" style="padding: 4px 8px; font-size: 12px; margin-right: 4px;">
                            <ion-icon name="print-outline" style="vertical-align: middle;"></ion-icon> View
                        </a>

                        <!-- Edit -->
                        <a href="/erp/finance/invoices/edit?id=<?= $invoice['id'] ?>" class="btn btn-sm btn-outline" style="padding: 4px 8px; font-size: 12px; margin-right: 4px;">Edit</a>

                        <!-- Delete -->
                        <form action="/erp/finance/invoices/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                            <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" style="padding: 4px 8px; font-size: 12px;">Del</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color: #94a3b8;">
                        No invoices recorded yet. <br>
                        <a href="/erp/finance/invoices/create" style="color: #0284c7; text-decoration: underline; margin-top: 6px; display: inline-block;">Create an invoice or bill</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function copyInvoiceLink(url, btn) {
    navigator.clipboard.writeText(url).then(function() {
        var origText = btn.innerHTML;
        btn.innerHTML = '<span style="color: #10b981;">✓ Copied!</span>';
        setTimeout(function() {
            btn.innerHTML = origText;
        }, 2000);
    }).catch(function() {
        prompt("Copy this invoice link:", url);
    });
}
</script>

    </main>
</div>
</body>
</html>
