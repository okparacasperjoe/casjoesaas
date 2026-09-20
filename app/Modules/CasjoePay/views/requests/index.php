<?php
$title = "Payment Requests | Casjoe Pay";
require __DIR__ . "/../partials/pay_header.php";
?>

<div class="top-bar">
    <h2>Payment Requests (Invoicing)</h2>
    <div>
        <a href="/pay" class="wallet-action-btn"><ion-icon name="arrow-back-outline"></ion-icon> Dashboard</a>
    </div>
</div>

<div class="app-grid" style="grid-template-columns: 1fr; margin-bottom: 30px;">
    <div class="fintech-card" style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 30px;">
        <h3 style="margin-top:0;">Send a Payment Request</h3>
        <p style="color: var(--pay-text-muted); font-size: 0.9rem;">Instantly email an invoice to a client. They will receive a secure link to pay you via card or bank transfer.</p>
        
        <form method="POST" action="/pay/requests/create" style="margin-top: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="pay-input-group">
                    <label class="pay-label">Client Email</label>
                    <input type="email" name="recipient_email" class="pay-input" placeholder="client@example.com" required>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Description (What is this for?)</label>
                    <input type="text" name="description" class="pay-input" placeholder="e.g. Website Design Services" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 20px;">
                <div class="pay-input-group">
                    <label class="pay-label">Currency</label>
                    <select name="currency" class="pay-select" required>
                        <option value="NGN">NGN</option>
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
                <div class="pay-input-group">
                    <label class="pay-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="pay-input" placeholder="0.00" required>
                </div>
            </div>

            <button type="submit" class="pay-btn-primary" style="max-width: 200px; margin-top: 10px;">
                <ion-icon name="send-outline"></ion-icon> Send Request
            </button>
        </form>
    </div>
</div>

<h3 class="section-title">Sent Requests</h3>
<div class="fintech-card" style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 30px;">
    <?php if (empty($requests)): ?>
        <p style="color: var(--pay-text-muted); text-align: center;">You have not sent any payment requests yet.</p>
    <?php else: ?>
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--pay-border); color: var(--pay-text-muted); font-size: 0.85rem;">
                    <th style="padding: 10px 0;">Reference</th>
                    <th>Sent To</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px 0; font-family: monospace; color: var(--pay-gold);"><?= $req["reference"] ?></td>
                        <td><?= htmlspecialchars($req["recipient_email"]) ?></td>
                        <td><?= htmlspecialchars($req["description"]) ?></td>
                        <td style="font-weight: 600;"><?= $req["currency"] ?> <?= number_format($req["amount"], 2) ?></td>
                        <td>
                            <?php if($req["status"] == "paid"): ?>
                                <span style="background: rgba(0,214,143,0.1); color: #00d68f; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">PAID</span>
                            <?php else: ?>
                                <span style="background: rgba(255,166,0,0.1); color: #FFA600; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">PENDING</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="navigator.clipboard.writeText('https://<?= $_SERVER['HTTP_HOST'] ?>/pay/request/<?= $req['reference'] ?>'); alert('Link copied!');" class="wallet-action-btn" style="padding: 6px 12px; font-size: 0.8rem;">Copy Link</button>
                                <?php if($req['status'] != 'paid'): ?>
                                    <a href="/pay/requests/edit/<?= $req['id'] ?>" class="wallet-action-btn" style="padding: 6px 12px; font-size: 0.8rem; text-decoration: none;">Edit</a>
                                    <form method="POST" action="/pay/requests/delete/<?= $req['id'] ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                        <button type="submit" class="wallet-action-btn" style="padding: 6px 12px; font-size: 0.8rem; background: rgba(255,59,48,0.1); color: #ff3b30; border-color: rgba(255,59,48,0.2);">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . "/../partials/pay_footer.php"; ?>
