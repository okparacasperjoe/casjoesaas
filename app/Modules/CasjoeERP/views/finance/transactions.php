<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transactions | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Financial Transactions</h2>
            <a href="/erp/transactions/create" class="btn"><ion-icon name="swap-horizontal-outline"></ion-icon> New Transaction</a>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Description</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $txn): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; color: #666;"><?= $txn['date'] ?></td>
                            <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($txn['description']) ?></td>
                            <td style="padding: 10px;">
                                <span style="background: <?= $txn['type'] == 'income' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $txn['type'] == 'income' ? '#15803d' : '#991b1b' ?>; padding: 2px 8px; border-radius: 4px;">
                                    <?= ucfirst($txn['type']) ?>
                                </span>
                            </td>
                            <td style="padding: 10px; font-family: monospace;">$<?= number_format($txn['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
