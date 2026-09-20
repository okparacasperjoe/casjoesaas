<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Create Invoice / Bill | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h1>Create Invoice / Bill</h1>
        <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Record client bills, hotel guest tabs, or unpaid customer debts</p>
    </div>
    <a href="/erp/finance/invoices" class="btn btn-secondary" style="background: #64748b; color: #fff; text-decoration: none;">Back</a>
</div>

<div class="card">
    <form action="/erp/finance/invoices/store" method="POST" id="invoiceForm">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label>Client / Guest / Debtor Name *</label>
                <input type="text" name="client_name" class="form-control" placeholder="e.g. John Doe (Room 204 / Table 3)" required>
            </div>
            <div class="form-group">
                <label>Client Email <span style="font-size: 12px; color: #888; font-weight: normal;">(Optional - leave blank if guest has no email)</span></label>
                <input type="email" name="client_email" class="form-control" placeholder="e.g. client@example.com (Optional)">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
            </div>
            <div class="form-group">
                <label>Currency</label>
                <select name="currency" class="form-control" required>
                    <option value="NGN">NGN - Nigerian Naira (₦)</option>
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="KES">KES - Kenyan Shilling</option>
                    <option value="GHS">GHS - Ghanaian Cedi</option>
                    <option value="ZAR">ZAR - South African Rand</option>
                </select>
            </div>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid rgba(0,0,0,0.06);">
        <h3>Items / Services / Orders</h3>
        <table class="table" id="itemsTable">
            <thead>
                <tr>
                    <th>Description</th>
                    <th width="100">Qty</th>
                    <th width="150">Price</th>
                    <th width="150">Total</th>
                    <th width="50"></th>
                </tr>
            </thead>
            <tbody id="itemsBody">
                <!-- Rows will be added here via JS -->
            </tbody>
        </table>
        
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
            <ion-icon name="add-circle-outline"></ion-icon> Add Item
        </button>

        <div class="invoice-summary" style="text-align: right; margin-top: 20px;">
            <h4>Grand Total: <span id="currencyPrefix">NGN</span> <span id="grandTotal">0.00</span></h4>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label>Notes / Payment Instructions</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Unpaid food order for Room 204. Please pay at the front desk or via online transfer."></textarea>
        </div>

        <input type="hidden" name="items_json" id="itemsJson">

        <button type="submit" class="btn btn-success" style="margin-top: 20px; width: 100%; padding: 12px; font-weight: 600; font-size: 16px;">
            <ion-icon name="receipt-outline" style="vertical-align: middle;"></ion-icon> Create & Record Invoice
        </button>
    </form>
</div>

<script>
function addItem() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control" oninput="calculateRow(this)" placeholder="e.g. Jollof Rice & Fried Chicken / Room Service"></td>
        <td><input type="number" class="form-control" value="1" min="1" oninput="calculateRow(this)"></td>
        <td><input type="number" class="form-control" value="0.00" min="0" step="0.01" oninput="calculateRow(this)"></td>
        <td class="row-total" style="font-weight: 600; font-family: monospace;">0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calculateTotal();
}

function calculateRow(input) {
    const tr = input.closest('tr');
    const inputs = tr.querySelectorAll('input');
    const qty = parseFloat(inputs[1].value) || 0;
    const price = parseFloat(inputs[2].value) || 0;
    const total = qty * price;
    tr.querySelector('.row-total').textContent = total.toFixed(2);
    calculateTotal();
}

function calculateTotal() {
    let grantTotal = 0;
    document.querySelectorAll('.row-total').forEach(el => {
        grantTotal += parseFloat(el.textContent) || 0;
    });
    document.getElementById('grandTotal').textContent = grantTotal.toFixed(2);
}

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    const items = [];
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const inputs = tr.querySelectorAll('input');
        if (inputs[0].value.trim() !== '') {
            items.push({
                description: inputs[0].value.trim(),
                quantity: parseFloat(inputs[1].value) || 1,
                price: parseFloat(inputs[2].value) || 0
            });
        }
    });
    
    if (items.length === 0) {
        e.preventDefault();
        alert("Please add at least one item description and price.");
        return;
    }
    
    document.getElementById('itemsJson').value = JSON.stringify(items);
});

// Init with one row
addItem();
</script>

    </main>
</div>
</body>
</html>
