<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Payroll History</h2>
            <a href="/erp/payroll/create" class="btn"><ion-icon name="cash-outline"></ion-icon> Run Payroll</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Period</th>
                        <th>Gross Pay</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payrolls as $p): ?>
                    <tr>
                        <td><?= $p['payment_date'] ?></td>
                        <td><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td><?= $p['pay_period_start'] ?> - <?= $p['pay_period_end'] ?></td>
                        <td><?= number_format($p['gross_pay'], 2) ?></td>
                        <td><strong><?= number_format($p['net_pay'], 2) ?></strong></td>
                        <td><?= ucfirst($p['status']) ?></td>
                        <td>
                            <a href="/erp/payroll/show?id=<?= $p['id'] ?>" class="btn btn-sm">Payslip</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
