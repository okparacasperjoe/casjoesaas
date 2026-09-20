<?php
$title = "Virtual Phone Number | Casjoe Pay";
$pageTitle = "Virtual Phone Number";
$activeMenu = "phone";
require_once __DIR__ . '/partials/pay_header.php';
?>

<div style="margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 30px;">
    <?php if (!$activePhone): ?>
        <!-- Generating phone number section -->
        <div class="pay-form-card" style="max-width: 500px; margin: 0;">
            <h3 class="pay-form-header" style="text-align: left; margin-bottom: 20px;">Generate Virtual Phone Number</h3>
            <p style="font-size: 0.85rem; color: var(--pay-text-muted); margin-bottom: 25px; line-height: 1.5;">
                Get a dedicated virtual mobile phone number to receive incoming SMS verification codes and messages for your business services globally.
            </p>
            <form method="POST" action="/pay/phone/create">
                <div class="pay-input-group">
                    <label class="pay-label">Select Country</label>
                    <select name="country" class="pay-select" required>
                        <option value="USA">United States (+1)</option>
                        <option value="UK">United Kingdom (+44)</option>
                        <option value="Canada">Canada (+1)</option>
                    </select>
                </div>
                <button type="submit" class="pay-btn-primary" style="margin-top: 15px;">
                    <ion-icon name="add-outline" style="font-size: 1.2rem;"></ion-icon> Generate Phone Number
                </button>
            </form>
        </div>
    <?php else: ?>
        <!-- Display Active Phone Card -->
        <div class="virtual-phone-card" style="background: linear-gradient(135deg, #090e3d 0%, #030722 100%); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 25px; color: white; width: 330px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-top: 4px solid var(--pay-gold); display: flex; flex-direction: column; justify-content: space-between;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="font-size: 1.15rem; color: var(--pay-gold); font-weight: 700; display: flex; align-items: center; gap: 6px;">
                    <ion-icon name="call" style="font-size: 1.3rem;"></ion-icon>
                    <?= htmlspecialchars($activePhone['country']) ?> Number
                </div>
                <span style="border: 1px solid var(--pay-green); color: var(--pay-green); font-size: 0.65rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: rgba(0,214,143,0.1); text-transform: uppercase; letter-spacing: 0.5px;">
                    <?= htmlspecialchars($activePhone['status']) ?>
                </span>
            </div>
            
            <div style="font-size: 1.6rem; letter-spacing: 1px; margin-bottom: 20px; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #fff;">
                <?= htmlspecialchars($activePhone['phone_number']) ?>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: var(--pay-text-muted); border-top: 1px solid rgba(255,255,255,0.03); padding-top: 15px; margin-top: 10px;">
                <span>SMS INBOX: <strong>ACTIVE</strong></span>
                <!-- Copy Icon -->
                <div style="background: rgba(255,255,255,0.05); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.05); cursor: pointer; transition: all 0.2s;" 
                     onmouseover="this.style.background='rgba(255,255,255,0.1)'" 
                     onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                     onclick="copyToClipboard('<?= htmlspecialchars($activePhone['phone_number']) ?>', this.querySelector('ion-icon'))">
                    <ion-icon name="copy-outline" style="font-size: 1rem; color: var(--pay-gold); transition: all 0.2s;"></ion-icon>
                </div>
            </div>
        </div>

        <!-- SMS Inbox list -->
        <div class="fintech-card" style="flex: 1; min-width: 320px; background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <h3 style="margin: 0 0 20px 0; font-size: 1.25rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px;">
                <ion-icon name="mail-unread-outline" style="font-size: 1.4rem; color: var(--pay-gold);"></ion-icon>
                SMS Inbox
            </h3>
            <?php if (empty($smsList)): ?>
                <div style="text-align: center; padding: 40px 20px; color: var(--pay-text-muted);">
                    <ion-icon name="mail-outline" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3; color: var(--pay-gold);"></ion-icon>
                    <p style="margin: 0; font-size: 0.95rem;">Your inbox is empty. Waiting for SMS messages...</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column;">
                    <?php foreach ($smsList as $index => $sms): ?>
                        <div style="padding: 16px 0; <?= $index < count($smsList) - 1 ? 'border-bottom: 1px solid rgba(255,255,255,0.04);' : '' ?>">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-weight: 700; color: var(--pay-gold); font-size: 0.95rem;"><?= htmlspecialchars($sms['sender']) ?></span>
                                <span style="font-size: 0.75rem; color: var(--pay-text-muted);"><?= date('M d, H:i', strtotime($sms['created_at'])) ?></span>
                            </div>
                            <div style="font-size: 0.9rem; color: #fff; line-height: 1.5; background: rgba(255,255,255,0.01); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.02);">
                                <?= htmlspecialchars($sms['message']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function copyToClipboard(text, iconEl) {
    navigator.clipboard.writeText(text).then(() => {
        const originalName = iconEl.getAttribute('name');
        iconEl.setAttribute('name', 'checkmark-outline');
        iconEl.style.color = 'var(--pay-green)';
        setTimeout(() => {
            iconEl.setAttribute('name', originalName);
            iconEl.style.color = 'var(--pay-gold)';
        }, 1500);
    }).catch(err => {
        console.error('Could not copy text: ', err);
    });
}
</script>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
