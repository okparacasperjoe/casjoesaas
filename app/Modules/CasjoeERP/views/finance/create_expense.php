<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Expense | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Record Expense</h1>
    <a href="/erp/finance/expenses" class="btn btn-secondary">Back</a>
</div>

<div class="card" style="max-width: 600px; margin: auto;">
    <form action="/erp/finance/expenses/store" method="POST">
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" class="form-control" placeholder="e.g. Office Supplies" required>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label>Amount (NGN)</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="col-md-6 form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control">
                <option value="Operational">Operational</option>
                <option value="Travel">Travel</option>
                <option value="Meals">Meals</option>
                <option value="Software">Software</option>
                <option value="Equipment">Equipment</option>
                <option value="Others">Others</option>
            </select>
        </div>

        <div class="form-group">
            <label>Vendor (Optional)</label>
            <select name="vendor_id" class="form-control">
                <option value="">-- Select Vendor --</option>
                <?php foreach ($vendors as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <small><a href="/erp/finance/vendors">Manage Vendors</a></small>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Save Expense</button>
    </form>
</div>

    </main>
</div>
</body>
</html>
