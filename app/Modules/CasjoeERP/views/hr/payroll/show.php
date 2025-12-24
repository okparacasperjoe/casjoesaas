<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip #<?= $payroll['id'] ?> | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .payslip-header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .payslip-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .payslip-table th, .payslip-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .payslip-table th {
            background: #f8f9fa;
        }
        .payslip-total {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Payslip</h2>
            <button onclick="window.print()" class="btn btn-sm" style="background: #333;">Print</button>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto; background: #fff;">
            <div class="payslip-header">
                <h1>Casjoe ERP</h1>
                <p>Payslip for Period: <?= $payroll['pay_period_start'] ?> to <?= $payroll['pay_period_end'] ?></p>
            </div>

            <div class="payslip-details">
                <div>
                    <strong>Employee:</strong><br>
                    <?= htmlspecialchars($payroll['first_name'] . ' ' . $payroll['last_name']) ?><br>
                    <?= htmlspecialchars($payroll['job_title']) ?><br>
                    <?= htmlspecialchars($payroll['email']) ?>
                </div>
                <div style="text-align: right;">
                    <strong>Payment Date:</strong> <?= $payroll['payment_date'] ?><br>
                    <strong>Status:</strong> <?= ucfirst($payroll['status']) ?>
                </div>
            </div>

            <table class="payslip-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="2"><strong>Earnings</strong></td></tr>
                    <?php foreach ($items as $item): ?>
                        <?php if ($item['type'] === 'earning'): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td style="text-align: right;"><?= number_format($item['amount'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <tr><td colspan="2"><strong>Deductions</strong></td></tr>
                    <?php foreach ($items as $item): ?>
                        <?php if ($item['type'] === 'deduction'): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td style="text-align: right;">-<?= number_format($item['amount'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="payslip-total">
                Net Pay: <?= number_format($payroll['net_pay'], 2) ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
