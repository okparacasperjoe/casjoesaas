<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Invoice | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Create Invoice</h1>
    <a href="/erp/finance/invoices" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <form action="/erp/finance/invoices/store" method="POST" id="invoiceForm">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Client Name</label>
                <input type="text" name="client_name" class="form-control" required>
            </div>
            <div class="col-md-6 form-group">
                <label>Client Email</label>
                <input type="email" name="client_email" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6 form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" required>
            </div>
        </div>

        <hr>
        <h3>Items</h3>
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
            <h4>Grand Total: NGN <span id="grandTotal">0.00</span></h4>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>

        <input type="hidden" name="items_json" id="itemsJson">

        <button type="submit" class="btn btn-success" style="margin-top: 20px; width: 100%;">Create Invoice</button>
    </form>
</div>

<script>
function addItem() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control" oninput="calculateRow(this)" placeholder="Item description"></td>
        <td><input type="number" class="form-control" value="1" min="1" oninput="calculateRow(this)"></td>
        <td><input type="number" class="form-control" value="0.00" min="0" step="0.01" oninput="calculateRow(this)"></td>
        <td class="row-total">0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
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
        items.push({
            description: inputs[0].value,
            quantity: inputs[1].value,
            price: inputs[2].value
        });
    });
    
    if (items.length === 0) {
        e.preventDefault();
        alert("Please add at least one item.");
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
