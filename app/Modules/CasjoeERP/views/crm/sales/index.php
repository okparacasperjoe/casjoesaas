<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
                        <th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td>#<?= $sale['id'] ?></td>
                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                        <td><?= $currencySymbol ?><?= number_format($sale['total_amount'], 2) ?></td>
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
                        <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['name']) ?> (<?= $currencySymbol ?><?= $i['unit_price'] ?>)</option>
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
    <dialog id="editSaleModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/crm/sales/update" method="POST">
            <input type="hidden" name="id" id="edit_sale_id">
            <h3 style="color:#000066; margin-top:0;">Edit Sale</h3>
            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id" id="edit_sale_customer_id" class="form-control" required>
                    <option value="">-- Select --</option>
                    <?php foreach($customers as $cu): ?><option value="<?=$cu['id']?>"><?=$cu['name']?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Amount</label><input type="number" step="0.01" name="amount" id="edit_sale_amount" class="form-control" required></div>
            <div class="form-group"><label>Sale Date</label><input type="date" name="sale_date" id="edit_sale_date" class="form-control" required></div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_sale_status" class="form-control">
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('editSaleModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn" style="background:#000066; color:#fff;">Update Sale</button>
            </div>
        </form>
    </dialog>
    <script>
    function editSale(s) {
        document.getElementById('edit_sale_id').value = s.id;
        document.getElementById('edit_sale_customer_id').value = s.customer_id;
        document.getElementById('edit_sale_amount').value = s.amount;
        document.getElementById('edit_sale_date').value = s.sale_date;
        document.getElementById('edit_sale_status').value = s.status;
        document.getElementById('editSaleModal').showModal();
    }
    </script>
</body>
</html>
