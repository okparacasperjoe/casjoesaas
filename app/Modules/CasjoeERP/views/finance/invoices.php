<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoices | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Invoices</h1>
    <a href="/erp/finance/invoices/create" class="btn btn-primary">
        <ion-icon name="add-outline"></ion-icon> Create Invoice
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td>#<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($invoice['client_name']) ?></td>
                    <td><?= $invoice['issue_date'] ?></td>
                    <td><?= $invoice['due_date'] ?></td>
                    <td>NGN <?= number_format($invoice['total_amount'], 2) ?></td>
                    <td>
                        <span class="badge badge-<?= $invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'overdue' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($invoice['status']) ?>
                        </span>
                    </td>
                    <td>
                        <!-- View Internal -->
                        <a href="/erp/finance/invoice/view?uuid=<?= $invoice['uuid'] ?>" class="btn btn-sm btn-secondary">View</a>
                        
                        <!-- Open Public Link -->
                         <a href="/invoice/<?= $invoice['uuid'] ?>" target="_blank" class="btn btn-sm btn-outline">
                            <ion-icon name="link-outline"></ion-icon> Client Link
                         </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">No invoices found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>
