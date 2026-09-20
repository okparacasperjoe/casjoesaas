<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Edit Transaction | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>Edit Transaction</h2>
            <a href="/erp/finance/transactions" class="btn btn-outline">Back</a>
        </div>
        <div class="card" style="max-width: 600px; margin-top: 20px;">
            <form action="/erp/finance/transactions/update" method="POST">
                <input type="hidden" name="id" value="<?= $transaction['id'] ?>">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($transaction['description']) ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Type</label>
                    <select name="type" class="form-control" required>
                        <option value="income" <?= $transaction['type'] == 'income' ? 'selected' : '' ?>>Income</option>
                        <option value="expense" <?= $transaction['type'] == 'expense' ? 'selected' : '' ?>>Expense</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($transaction['category']) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="<?= $transaction['date'] ?>" required>
                </div>
                <div style="text-align: right; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Update Transaction</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
