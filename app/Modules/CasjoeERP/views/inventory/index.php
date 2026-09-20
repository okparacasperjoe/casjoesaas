<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between;">
            <h2 style="margin: 0;">Inventory</h2>
            <button onclick="document.getElementById('newItemModal').showModal()" class="btn">Add Item</button>
        </div>

        <div class="card">
            <div style="overflow-x: auto; width: 100%;">
                <table style="width: 100%; min-width: 500px;">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['sku']) ?></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $currencySymbol ?><?= $item['unit_price'] ?></td>
                            <td><?= $item['stock_quantity'] ?></td>
                            <td><?= ucfirst($item['status']) ?></td>
                            <td>
                                <a href="/erp/inventory/edit?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                                <form action="/erp/inventory/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <dialog id="newItemModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px; width: 90%; max-width: 400px; box-sizing: border-box;">
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
