<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Add Inventory Item | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Add New Stock Item</h2>
            <a href="/erp/inventory" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: auto;">
            <form method="POST" action="/erp/inventory/store">
                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" name="item_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="e.g. LAP-001">
                </div>

                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="0" required>
                </div>

                <div class="form-group">
                    <label>Unit Price ($)</label>
                    <input type="number" step="0.01" name="unit_price" class="form-control" value="0.00" required>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Add Item</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
