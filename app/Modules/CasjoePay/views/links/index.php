<?php
$title = "Payment Links | Casjoe Pay";
$pageTitle = "Payment Links";
$activeMenu = "links";
require_once __DIR__ . '/../partials/pay_header.php';
?>

<style>
.pay-links-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
    align-items: stretch;
}

@media (min-width: 992px) {
    .pay-links-container {
        flex-direction: row;
        align-items: flex-start;
    }
    .pay-form-section {
        flex: 0 0 40%;
        max-width: 450px;
    }
    .pay-list-section {
        flex: 1;
    }
}

.pay-link-item {
    padding: 20px; 
    background: rgba(255,255,255,0.02); 
    border: 1px solid var(--pay-border); 
    border-radius: 16px; 
    display: flex; 
    flex-direction: column; 
    gap: 15px;
    transition: all 0.3s ease;
}

.pay-link-item:hover {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,166,0,0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

@media (min-width: 768px) {
    .pay-link-item {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.pay-link-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    width: 100%;
}

@media (min-width: 768px) {
    .pay-link-actions {
        width: auto;
        justify-content: flex-end;
    }
}

.pay-action-btn {
    flex: 1;
    min-width: calc(50% - 5px);
    padding: 10px 14px; 
    font-size: 0.85rem; 
    text-align: center;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

@media (min-width: 768px) {
    .pay-action-btn {
        flex: none;
        min-width: 0;
    }
}

/* Modal styling for mobile */
.qr-modal-content {
    background: #ffffff; 
    width: 90%; 
    max-width: 350px; 
    border-radius: 20px; 
    padding: 40px 30px; 
    box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
    position: relative; 
    text-align: center;
}
</style>

<div class="pay-links-container">
    <!-- Create Form -->
    <div class="pay-form-section">
        <div class="pay-form-card" style="margin: 0; width: 100%; max-width: 100%;">
            <h3 class="pay-form-header" style="text-align: left; margin-bottom: 20px;">Create New Link</h3>
            <form action="/pay/links/create" method="POST">
                <div class="pay-input-group">
                    <label class="pay-label">Link Title</label>
                    <input type="text" name="title" class="pay-input" placeholder="e.g. Wedding Contribution, Consultation Fee" required>
                </div>

                <div class="pay-input-group">
                    <label class="pay-label">Custom URL Slug (Optional)</label>
                    <input type="text" name="slug" class="pay-input" placeholder="e.g. wedding-john-mary" pattern="[A-Za-z0-9-]+" title="Only letters, numbers, and hyphens allowed">
                    <small style="color: var(--pay-text-muted); font-size: 0.75rem; display: block; margin-top: 5px;">Your link will be: app.casjoe.com/pay/link/<b>your-slug</b></small>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; margin-bottom: 25px;">
                    <div class="pay-input-group" style="margin-bottom: 0;">
                        <label class="pay-label">Currency</label>
                        <select name="currency" class="pay-select">
                            <option value="NGN">NGN (Naira)</option>
                            <option value="USD">USD (US Dollar)</option>
                            <option value="GBP">GBP (British Pound)</option>
                            <option value="EUR">EUR (Euro)</option>
                            <option value="GHS">GHS (Ghana Cedis)</option>
                            <option value="KES">KES (Kenyan Shilling)</option>
                            <option value="ZAR">ZAR (South African Rand)</option>
                        </select>
                    </div>
                    <div class="pay-input-group" style="margin-bottom: 0;">
                        <label class="pay-label">Amount (Optional)</label>
                        <input type="number" name="amount" class="pay-input" step="0.01" placeholder="Leave empty for open amount">
                    </div>
                </div>

                <!-- Recurring Subscription Option -->
                <div style="background: rgba(255,166,0,0.05); border: 1px solid rgba(255,166,0,0.2); padding: 15px; border-radius: 10px; margin-bottom: 25px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--pay-text);">
                        <input type="checkbox" name="is_recurring" value="1" onchange="document.getElementById('recurring_options').style.display = this.checked ? 'block' : 'none';" style="width: 18px; height: 18px; accent-color: var(--pay-gold);">
                        <span style="font-weight: 600;">Make this a recurring subscription</span>
                    </label>
                    <div id="recurring_options" style="display: none; margin-top: 15px;">
                        <label class="pay-label">Billing Interval</label>
                        <select name="billing_interval" class="pay-select">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="pay-btn-primary">
                    <ion-icon name="add-outline" style="font-size: 1.2rem;"></ion-icon> Create Link
                </button>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="pay-list-section">
        <div class="fintech-card" style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 30px;">
            <h3 style="margin: 0 0 20px 0; font-size: 1.25rem; font-weight: 700; color: #fff;">Your Active Links</h3>
            <?php if (empty($links)): ?>
                <div style="text-align: center; padding: 40px 20px; color: var(--pay-text-muted); background: rgba(255,255,255,0.01); border-radius: 16px; border: 1px dashed var(--pay-border);">
                    <ion-icon name="link-outline" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px; color: var(--pay-gold);"></ion-icon>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500;">No links created yet.</p>
                </div>
            <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($links as $link): ?>
                    <li class="pay-link-item">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 1.1rem; color: #fff; line-height: 1.3; margin-bottom: 5px;"><?= htmlspecialchars($link['title']) ?></div>
                            <div style="font-size: 0.9rem; color: var(--pay-gold); font-weight: 600; margin-bottom: 8px;">
                                <?= $link['amount'] > 0 ? htmlspecialchars($link['currency']) . ' ' . number_format($link['amount'], 2) : 'Open Amount (' . htmlspecialchars($link['currency']) . ')' ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--pay-text-muted); display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 6px;">
                                    <ion-icon name="eye-outline"></ion-icon> <?= $link['views'] ?> Views
                                </span>
                                <?php if (!empty($link['is_recurring'])): ?>
                                    <span style="background: rgba(0, 214, 143, 0.1); color: #00d68f; padding: 4px 8px; border-radius: 6px; font-weight: 600; text-transform: uppercase;">
                                        <ion-icon name="sync-outline" style="vertical-align: middle; margin-top: -2px;"></ion-icon> <?= htmlspecialchars($link['billing_interval']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="pay-link-actions">
                            <button onclick="showQRCode('<?= $link['slug'] ?>')" class="fintech-btn pay-action-btn" style="background: var(--pay-gold); border: 1px solid var(--pay-gold); color: #000; font-weight: 600; text-decoration: none;">
                                <ion-icon name="qr-code-outline" style="font-size: 1.1rem; margin-right: 4px;"></ion-icon> QR
                            </button>
                            <a href="/pay/link/<?= $link['slug'] ?>" target="_blank" class="fintech-btn pay-action-btn" style="background: rgba(255,166,0,0.15); border: 1px solid var(--pay-gold); color: var(--pay-gold); font-weight: 600; text-decoration: none;">
                                <ion-icon name="open-outline" style="font-size: 1.1rem; margin-right: 4px;"></ion-icon> Open
                            </a>
                            <button onclick="copyLink('<?= $link['slug'] ?>')" class="fintech-btn pay-action-btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--pay-border);">
                                <ion-icon name="copy-outline" style="font-size: 1.1rem; margin-right: 4px;"></ion-icon> Copy
                            </button>
                            <form method="POST" action="/pay/links/delete/<?= $link['id'] ?>" onsubmit="return confirm('Are you sure you want to delete this payment link?');" style="margin: 0; flex: 1; min-width: calc(50% - 5px);">
                                <button type="submit" class="fintech-btn pay-action-btn" style="width: 100%; background: rgba(255,50,50,0.15); border: 1px solid #ff4444; color: #ff4444;">
                                    <ion-icon name="trash-outline" style="font-size: 1.1rem; margin-right: 4px;"></ion-icon> Delete
                                </button>
                            </form>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function copyLink(slug) {
        const url = window.location.origin + '/pay/link/' + slug;
        navigator.clipboard.writeText(url);
        alert('Link copied to clipboard!');
    }

    function showQRCode(slug) {
        const url = window.location.origin + '/pay/link/' + slug;
        const qrContainer = document.getElementById("qrCodeContainer");
        qrContainer.innerHTML = ""; // clear previous
        
        // Generate new QR code using qrcode.js
        new QRCode(qrContainer, {
            text: url,
            width: 200,
            height: 200,
            colorDark : "#1a1a2e",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
        
        document.getElementById("qrModalLink").innerText = url;
        document.getElementById("qrModalLink").href = url;
        document.getElementById("qrModal").style.display = "flex";
    }
</script>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- QR Modal -->
<div id="qrModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="qr-modal-content">
        <button onclick="document.getElementById('qrModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #1a1a2e;">&times;</button>
        <h3 style="margin: 0 0 10px 0; color: #1a1a2e; font-weight: 700;">Scan to Pay</h3>
        <p style="color: #7a7a9a; font-size: 0.85rem; margin-bottom: 25px;">Customers can scan this QR code with their camera to instantly open the payment link.</p>
        
        <div id="qrCodeContainer" style="display: flex; justify-content: center; padding: 15px; background: #fff; border: 2px solid #f4f6f9; border-radius: 12px; margin-bottom: 20px;"></div>
        
        <a id="qrModalLink" href="#" target="_blank" style="color: #000066; font-size: 0.85rem; font-weight: 600; text-decoration: none; word-break: break-all; background: #f4f6f9; padding: 10px; border-radius: 8px; display: block;"></a>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/pay_footer.php'; ?>
