<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Sales Orders</h2>
            <button onclick="document.getElementById('newSaleModal').showModal()" class="btn">New Sale</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td>#<?= $sale['id'] ?></td>
                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                        <td>$<?= number_format($sale['total_amount'], 2) ?></td>
                        <td><?= ucfirst($sale['status']) ?></td>
                        <td><?= $sale['sale_date'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="newSaleModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/crm/sales/store" method="POST">
            <h3>Record New Sale</h3>
            
            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Product (Inventory)</label>
                <select name="inventory_item_id" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    <?php foreach ($inventory as $i): ?>
                        <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['name']) ?> ($<?= $i['unit_price'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group"><label>Quantity</label><input type="number" name="quantity" class="form-control" min="1" value="1" required></div>

            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newSaleModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Save & Complete</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>
