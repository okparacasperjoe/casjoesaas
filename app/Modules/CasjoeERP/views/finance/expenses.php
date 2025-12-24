<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expenses | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Expenses</h1>
    <a href="/erp/finance/expenses/create" class="btn btn-primary">
        <ion-icon name="add-outline"></ion-icon> Record Expense
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Vendor</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $exp): ?>
                <tr>
                    <td><?= $exp['date'] ?></td>
                    <td><?= htmlspecialchars($exp['description']) ?></td>
                    <td><?= htmlspecialchars($exp['category']) ?></td>
                    <td><?= htmlspecialchars($exp['vendor_name'] ?? '-') ?></td>
                    <td>NGN <?= number_format($exp['amount'], 2) ?></td>
                    <td>
                        <span class="badge badge-success"><?= ucfirst($exp['status']) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($expenses)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 20px;">No expenses recorded.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>
