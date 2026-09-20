<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title>Expenses | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="top-bar">
    <div>
        <h2>Expenses</h2>
        <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Manage company expenses, outgoing payments, and vendor disbursements</p>
    </div>
    <a href="/erp/finance/expenses/create" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
        <ion-icon name="add-outline"></ion-icon> Record Expense
    </a>
</div>

<?php if (!empty($errorMsg)): ?>
    <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="alert-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
        <div><?= htmlspecialchars($errorMsg) ?></div>
    </div>
<?php endif; ?>

<?php if (!empty($successMsg)): ?>
    <div style="background: #ecfdf5; border: 1px solid #34d399; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <ion-icon name="checkmark-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
        <div><?= htmlspecialchars($successMsg) ?></div>
    </div>
<?php endif; ?>

<div class="card">
    <table class="data-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #eee;">
                <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Description</th>
                <th style="padding: 10px;">Category</th>
                <th style="padding: 10px;">Vendor</th>
                <th style="padding: 10px;">Amount</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $exp): 
                $st = strtolower($exp['status'] ?? 'approved');
                $badgeBg = '#dcfce7'; $badgeColor = '#15803d';
                if ($st === 'pending') { $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; }
                elseif ($st === 'rejected') { $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; }
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; color: #666; white-space: nowrap;"><?= htmlspecialchars($exp['date']) ?></td>
                    <td style="padding: 10px; font-weight: 600;"><?= htmlspecialchars($exp['description']) ?></td>
                    <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($exp['category'] ?? 'General') ?></td>
                    <td style="padding: 10px; color: #64748b; font-size: 13px;"><?= htmlspecialchars($exp['vendor_name'] ?? '-') ?></td>
                    <td style="padding: 10px; font-family: monospace; font-weight: 600; color: #dc2626;">
                        <?= $currencySymbol ?><?= number_format($exp['amount'], 2) ?>
                    </td>
                    <td style="padding: 10px;">
                        <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                            <?= ucfirst($st) ?>
                        </span>
                    </td>
                    <td style="padding: 10px; text-align: right; white-space: nowrap;">
                        <a href="/erp/finance/expenses/edit?id=<?= $exp['id'] ?>" class="btn btn-sm btn-outline" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; font-size: 12px; margin-right: 4px;">
                            <ion-icon name="create-outline"></ion-icon> Edit
                        </a>
                        <form method="POST" action="/erp/finance/expenses/delete" onsubmit="return confirm('Are you sure you want to delete this expense record?');" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                            <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); padding: 4px 8px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                <ion-icon name="trash-outline"></ion-icon> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($expenses)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color: #94a3b8;">
                        No expenses recorded yet.<br>
                        <a href="/erp/finance/expenses/create" style="color: #0284c7; text-decoration: underline; margin-top: 6px; display: inline-block;">Record your first expense</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </main>
</div>
</body>
</html>
