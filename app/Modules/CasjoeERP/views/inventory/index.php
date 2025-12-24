<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Inventory</h2>
            <button onclick="document.getElementById('newItemModal').showModal()" class="btn">Add Item</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['sku']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>$<?= number_format($item['unit_price'], 2) ?></td>
                        <td><?= $item['stock_quantity'] ?></td>
                        <td><?= ucfirst($item['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="newItemModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/inventory/store" method="POST">
            <h3>New Product</h3>
            <div class="form-group"><label>SKU</label><input type="text" name="sku" class="form-control" required></div>
            <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label>Unit Price</label><input type="number" step="0.01" name="unit_price" class="form-control" required></div>
            <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock_quantity" class="form-control" required></div>
            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newItemModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Save</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>
