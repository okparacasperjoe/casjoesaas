<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Edit Expense | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Edit Expense</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Update expense details, amount, category, status, or vendor</p>
            </div>
            <a href="/erp/finance/expenses" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Expenses
            </a>
        </div>
        <div class="card" style="max-width: 650px; margin: 20px auto;">
            <form action="/erp/finance/expenses/update" method="POST">
                <input type="hidden" name="id" value="<?= $expense['id'] ?>">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Description *</label>
                    <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($expense['description']) ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Amount *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($expense['amount']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Date *</label>
                        <input type="date" name="date" class="form-control" value="<?= $expense['date'] ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Category</label>
                        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($expense['category'] ?? 'General') ?>" placeholder="e.g. Utilities, Operations">
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px;">Status</label>
                        <select name="status" class="form-control">
                            <option value="approved" <?= ($expense['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved / Paid</option>
                            <option value="pending" <?= ($expense['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="rejected" <?= ($expense['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Vendor (Optional)</label>
                    <select name="vendor_id" class="form-control">
                        <option value="">-- No Vendor Assigned --</option>
                        <?php if (!empty($vendors)): ?>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= ($expense['vendor_id'] == $v['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="/erp/finance/expenses" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <ion-icon name="checkmark-outline"></ion-icon> Update Expense
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
