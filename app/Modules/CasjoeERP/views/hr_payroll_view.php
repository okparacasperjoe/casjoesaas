<?php require __DIR__ . '/../../../Core/Views/layout/header.php'; ?>

<div class="erp-container">
    <div class="erp-header">
        <div class="header-left">
            <h1><ion-icon name="document-text-outline"></ion-icon> Payroll: <?= htmlspecialchars($payroll['month'] . ' ' . $payroll['year']) ?></h1>
            <p>Total Gross: ₦<?= number_format($payroll['total_gross'], 2) ?> | Total Net: ₦<?= number_format($payroll['total_net'], 2) ?></p>
        </div>
        <div class="header-actions">
            <a href="/erp/hr/payroll" class="btn btn-secondary"><ion-icon name="arrow-back"></ion-icon> Back to Payroll</a>
        </div>
    </div>

    <div class="erp-card">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Gross Pay</th>
                    <th>Pension (8%)</th>
                    <th>Taxable Income</th>
                    <th>PAYE Tax (FIRS)</th>
                    <th>Net Pay</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($slips as $slip): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($slip['first_name'] . ' ' . $slip['last_name']) ?></strong><br>
                            <small style="color: var(--text-secondary);"><?= htmlspecialchars($slip['job_title']) ?></small>
                        </td>
                        <td>₦<?= number_format($slip['gross_pay'], 2) ?></td>
                        <td style="color: #dc2626;">-₦<?= number_format($slip['pension_deduction'], 2) ?></td>
                        <td>₦<?= number_format($slip['taxable_income'], 2) ?></td>
                        <td style="color: #dc2626;">-₦<?= number_format($slip['paye_tax'], 2) ?></td>
                        <td style="color: #10b981; font-weight: bold;">₦<?= number_format($slip['net_pay'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../../Core/Views/layout/footer.php'; ?>
