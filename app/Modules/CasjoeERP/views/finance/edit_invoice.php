<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1>Edit Invoice #<?= str_pad($invoice['id'], 5, '0', STR_PAD_LEFT) ?></h1>
    <a href="/erp/finance/invoices" class="btn btn-secondary" style="background: #64748b; color: #fff; text-decoration: none;">Back</a>
</div>

<div class="card">
    <form action="/erp/finance/invoices/update" method="POST" id="invoiceForm">
        <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label>Client / Guest / Debtor Name *</label>
                <input type="text" name="client_name" class="form-control" value="<?= htmlspecialchars($invoice['client_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Client Email <span style="font-size: 12px; color: #888; font-weight: normal;">(Optional)</span></label>
                <input type="email" name="client_email" class="form-control" value="<?= htmlspecialchars($invoice['client_email'] ?? '') ?>" placeholder="guest@example.com (Optional)">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control" value="<?= $invoice['issue_date'] ?>" required>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= $invoice['due_date'] ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" style="font-weight: 600;">
                    <option value="sent" <?= in_array($invoice['status'], ['sent', 'draft']) ? 'selected' : '' ?>>Unpaid (Pending)</option>
                    <option value="paid" <?= $invoice['status'] == 'paid' ? 'selected' : '' ?>>Paid (Payment Received)</option>
                    <option value="overdue" <?= $invoice['status'] == 'overdue' ? 'selected' : '' ?>>Overdue</option>
                    <option value="cancelled" <?= $invoice['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid rgba(0,0,0,0.06);">
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

        <div class="invoice-summary" style="text-align: right; margin-top: 20px;">
            <h4>Grand Total: <span id="currencyPrefix"><?= htmlspecialchars($invoice['currency'] ?? 'NGN') ?></span> <span id="grandTotal">0.00</span></h4>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($invoice['notes'] ?? '') ?></textarea>
        </div>

        <input type="hidden" name="items_json" id="itemsJson">
        <input type="hidden" name="send_email" id="sendEmail" value="0">

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success" style="flex: 1; padding: 12px; font-weight: 600;">Save Changes</button>
            <button type="button" onclick="submitAndSend()" class="btn btn-primary" style="flex: 1; padding: 12px;">Save & Send Email</button>
        </div>
    </form>
</div>

<script>
// Existing items from the database
const existingItems = <?= json_encode($items ?? []) ?>;

function addItem(desc = '', qty = 1, price = 0) {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control" oninput="calculateRow(this)" placeholder="Item description" value="${desc}"></td>
        <td><input type="number" class="form-control" value="${qty}" min="1" oninput="calculateRow(this)"></td>
        <td><input type="number" class="form-control" value="${price}" min="0" step="0.01" oninput="calculateRow(this)"></td>
        <td class="row-total" style="font-weight: 600; font-family: monospace;">0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">✕</button></td>
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
        grantTotal += parseFloat(el.textContent) || 0;
    });
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
}

function submitAndSend() {
    document.getElementById('sendEmail').value = '1';
    const form = document.getElementById('invoiceForm');
    if (form.reportValidity()) {
        const submitEvent = new Event('submit', { cancelable: true });
        form.dispatchEvent(submitEvent);
        if (!submitEvent.defaultPrevented) form.submit();
    }
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
        alert("Please add at least one item.");
        return;
    }
    document.getElementById('itemsJson').value = JSON.stringify(items);
});

// Pre-populate existing items
if (existingItems.length > 0) {
    existingItems.forEach(item => {
        addItem(item.description, item.quantity, item.unit_price);
    });
} else {
    addItem();
}
</script>

    </main>
</div>
</body>
</html>
