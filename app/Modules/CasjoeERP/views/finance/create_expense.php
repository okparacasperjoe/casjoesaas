<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Record Expense | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1>Record Expense</h1>
    <a href="/erp/finance/expenses" class="btn btn-secondary" style="background: #64748b; color: #fff; text-decoration: none;">Back</a>
</div>

<div class="card" style="max-width: 600px; margin: auto;">
    <form action="/erp/finance/expenses/store" method="POST">
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Description</label>
            <input type="text" name="description" class="form-control" placeholder="e.g. Office Supplies, Cloud Hosting" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label>Amount (<?= htmlspecialchars($cCode ?? 'NGN') ?> - <?= htmlspecialchars($currencySymbol ?? '₦') ?>)</label>
                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option value="Operational">Operational</option>
                    <option value="Travel">Travel</option>
                    <option value="Meals">Meals</option>
                    <option value="Software">Software</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="approved">Approved / Paid</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Vendor (Optional)</label>
            <select name="vendor_id" class="form-control">
                <option value="">-- Select Vendor --</option>
                <?php foreach ($vendors as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Save Expense</button>
    </form>
</div>

    </main>
</div>
</body>
</html>
