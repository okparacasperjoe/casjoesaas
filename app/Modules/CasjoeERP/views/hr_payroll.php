<?php require __DIR__ . '/../../../Core/Views/layout/header.php'; ?>

<div class="erp-container">
    <div class="erp-header">
        <div class="header-left">
            <h1><ion-icon name="cash-outline"></ion-icon> Payroll Management</h1>
            <p>Automated FIRS standard tax calculations</p>
        </div>
        <div class="header-actions">
            <a href="/erp/hr" class="btn btn-secondary" style="margin-right: 12px;"><ion-icon name="arrow-back"></ion-icon> Back to HR</a>
            <?php if($employeeCount > 0): ?>
            <button class="btn btn-primary" onclick="document.getElementById('payrollModal').style.display='flex'">
                <ion-icon name="play-outline"></ion-icon> Run Payroll
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger" style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <div class="erp-card">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Month/Year</th>
                    <th>Gross Pay</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($payrolls)): ?>
                    <tr><td colspan="5" class="text-center">No payroll runs found.</td></tr>
                <?php else: ?>
                    <?php foreach($payrolls as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['month']) ?> <?= htmlspecialchars($p['year']) ?></strong></td>
                            <td>₦<?= number_format($p['total_gross'], 2) ?></td>
                            <td style="color: #10b981; font-weight: bold;">₦<?= number_format($p['total_net'], 2) ?></td>
                            <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td>
                                <a href="/erp/hr/payroll/view?id=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">View Slips</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Run Payroll Modal -->
<div id="payrollModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:9999;">
    <div class="modal-content" style="background:#fff; padding:32px; border-radius:16px; width:100%; max-width:400px;">
        <h2 style="margin-bottom: 20px; color: var(--brand-blue);">Run Payroll</h2>
        <form action="/erp/hr/payroll/run" method="POST">
            <div class="form-group">
                <label>Month</label>
                <select name="month" class="form-control" required>
                    <?php
                        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                        foreach($months as $m) {
                            $sel = (date('F') == $m) ? 'selected' : '';
                            echo "<option value=\"$m\" $sel>$m</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group" style="margin-top: 16px;">
                <label>Year</label>
                <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" required>
            </div>
            <div class="form-group" style="margin-top: 16px;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Generate</button>
                <button type="button" class="btn btn-secondary" style="width:100%; margin-top: 8px;" onclick="document.getElementById('payrollModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../../Core/Views/layout/footer.php'; ?>
