<?php
$title = "Edit Payment Request | Casjoe Pay";
require __DIR__ . "/../partials/pay_header.php";
?>

<div class="top-bar">
    <h2>Edit Payment Request</h2>
    <div>
        <a href="/pay/requests" class="wallet-action-btn"><ion-icon name="arrow-back-outline"></ion-icon> Back to Requests</a>
    </div>
</div>

<div class="app-grid" style="grid-template-columns: 1fr; margin-bottom: 30px;">
    <div class="fintech-card" style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 30px;">
        <h3 style="margin-top:0;">Update Payment Request</h3>
        <p style="color: var(--pay-text-muted); font-size: 0.9rem;">Modify the details of your pending payment request. Note that this won't change the link, but it will update the invoice details they see when they click it.</p>
        
        <form method="POST" action="/pay/requests/update/<?= htmlspecialchars($request['id']) ?>" style="margin-top: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="pay-input-group">
                    <label class="pay-label">Client Email (Cannot change)</label>
                    <input type="email" class="pay-input" value="<?= htmlspecialchars($request['recipient_email']) ?>" disabled>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Description</label>
                    <input type="text" name="description" class="pay-input" value="<?= htmlspecialchars($request['description']) ?>" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 20px;">
                <div class="pay-input-group">
                    <label class="pay-label">Currency</label>
                    <select name="currency" class="pay-select" required>
                        <option value="NGN" <?= $request['currency'] == 'NGN' ? 'selected' : '' ?>>NGN</option>
                        <option value="USD" <?= $request['currency'] == 'USD' ? 'selected' : '' ?>>USD</option>
                        <option value="GBP" <?= $request['currency'] == 'GBP' ? 'selected' : '' ?>>GBP</option>
                        <option value="EUR" <?= $request['currency'] == 'EUR' ? 'selected' : '' ?>>EUR</option>
                    </select>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="pay-input" value="<?= htmlspecialchars($request['amount']) ?>" required>
                </div>
            </div>

            <button type="submit" class="pay-btn-primary" style="max-width: 200px; margin-top: 10px;">
                <ion-icon name="save-outline"></ion-icon> Save Changes
            </button>
        </form>
    </div>
</div>

<?php require __DIR__ . "/../partials/pay_footer.php"; ?>
