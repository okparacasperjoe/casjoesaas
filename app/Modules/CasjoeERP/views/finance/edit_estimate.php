<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Estimate | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed; top: 0; left: -100%; height: 100%; z-index: 1000;
                transition: left 0.3s ease; width: 260px !important; background-color: #000066;
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .card, .table-container { overflow-x: auto; }
            table, .data-table { min-width: 600px; }
            .row { flex-direction: column; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Edit Estimate #<?= str_pad($estimate['id'], 5, '0', STR_PAD_LEFT) ?></h1>
    <a href="/erp/finance/estimates" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <form action="/erp/finance/estimates/update" method="POST" id="estimateForm">
        <input type="hidden" name="estimate_id" value="<?= $estimate['id'] ?>">

        <div class="row">
            <div class="col-md-6 form-group">
                <label>Client Name</label>
                <input type="text" name="client_name" class="form-control" value="<?= htmlspecialchars($estimate['client_name']) ?>" required>
            </div>
            <div class="col-md-6 form-group">
                <label>Client Email</label>
                <input type="email" name="client_email" class="form-control" value="<?= htmlspecialchars($estimate['client_email']) ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control" value="<?= $estimate['issue_date'] ?>" required>
            </div>
            <div class="col-md-6 form-group">
                <label>Valid Until</label>
                <input type="date" name="valid_until" class="form-control" value="<?= $estimate['valid_until'] ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="sent" <?= $estimate['status'] == 'sent' ? 'selected' : '' ?>>Unpaid</option>
                <option value="paid" <?= $estimate['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="overdue" <?= $estimate['status'] == 'overdue' ? 'selected' : '' ?>>Overdue</option>
                <option value="draft" <?= $estimate['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="cancelled" <?= $estimate['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
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
                <!-- Pre-populated by JS from existing items -->
            </tbody>
        </table>
        
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
            <ion-icon name="add-circle-outline"></ion-icon> Add Item
        </button>

        <div class="estimate-summary" style="text-align: right; margin-top: 20px;">
            <h4>Grand Total: NGN <span id="grandTotal">0.00</span></h4>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($estimate['notes'] ?? '') ?></textarea>
        </div>

        <input type="hidden" name="items_json" id="itemsJson">
        <input type="hidden" name="send_email" id="sendEmail" value="0">

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success" style="flex: 1;">Save Changes</button>
            <button type="button" onclick="submitAndSend()" class="btn btn-primary" style="flex: 1;">Save & Send Email</button>
        </div>
    </form>
</div>

<script>
// Existing items from the database
const existingItems = <?= json_encode($items) ?>;

function addItem(desc = '', qty = 1, price = 0) {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control" oninput="calculateRow(this)" placeholder="Item description" value="${desc}"></td>
        <td><input type="number" class="form-control" value="${qty}" min="1" oninput="calculateRow(this)"></td>
        <td><input type="number" class="form-control" value="${price}" min="0" step="0.01" oninput="calculateRow(this)"></td>
        <td class="row-total">0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
    `;
    tbody.appendChild(tr);
    const inputs = tr.querySelectorAll('input');
    calculateRow(inputs[0]);
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
    tr.querySelector('.row-total').textContent = (qty * price).toFixed(2);
    calculateTotal();
}

function calculateTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.row-total').forEach(el => {
        grandTotal += parseFloat(el.textContent) || 0;
    });
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
}

function submitAndSend() {
    document.getElementById('sendEmail').value = '1';
    const form = document.getElementById('estimateForm');
    if (form.reportValidity()) {
        const submitEvent = new Event('submit', { cancelable: true });
        form.dispatchEvent(submitEvent);
        if (!submitEvent.defaultPrevented) form.submit();
    }
}

document.getElementById('estimateForm').addEventListener('submit', function(e) {
    const items = [];
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const inputs = tr.querySelectorAll('input');
        if (inputs[0].value.trim() !== '') {
            items.push({
                description: inputs[0].value,
                quantity: inputs[1].value,
                price: inputs[2].value
            });
        }
    });
    if (items.length === 0) {
        e.preventDefault();
        alert("Please add at least one item.");
        return;
    }
    document.getElementById('itemsJson').value = JSON.stringify(items);
});

// Pre-populate existing items
existingItems.forEach(item => {
    addItem(item.description, item.quantity, item.unit_price);
});
</script>

    </main>
</div>
</body>
</html>
