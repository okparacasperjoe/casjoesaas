<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Transaction | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Record Transaction</h2>
            <a href="/erp/transactions" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: auto;">
            <form method="POST" action="/erp/transactions/store">
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amount ($)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Record Transaction</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
