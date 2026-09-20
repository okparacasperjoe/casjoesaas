<?php
$title = "Fund Wallet | Casjoe Pay";
$pageTitle = "Fund Wallet";
$activeMenu = "fund";
require_once __DIR__ . '/partials/pay_header.php';
?>

<div class="pay-form-card" style="max-width: 500px; margin: 0 auto;">
    <h2 class="pay-form-header" style="text-align: center; margin-bottom: 24px;">Fund Wallet</h2>
    <form method="POST" action="/pay/fund">
        <div style="margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="pay-input-group" style="margin-bottom: 0;">
                <label class="pay-label">Currency</label>
                <select name="currency" class="pay-select">
                    <!-- Core Supported -->
                    <option value="NGN">NGN (Nigerian Naira)</option>
                    <option value="USD">USD (US Dollar)</option>
                    <option value="GHS">GHS (Ghanaian Cedi)</option>
                    <option value="KES">KES (Kenyan Shilling)</option>
                    <option value="ZAR">ZAR (South African Rand)</option>
                    <!-- Expanded Africa / Global -->
                    <option value="UGX">UGX (Ugandan Shilling)</option>
                    <option value="TZS">TZS (Tanzanian Shilling)</option>
                    <option value="RWF">RWF (Rwandan Franc)</option>
                    <option value="XOF">XOF (West African CFA Franc)</option>
                    <option value="XAF">XAF (Central African CFA Franc)</option>
                    <option value="ZMW">ZMW (Zambian Kwacha)</option>
                    <option value="MWK">MWK (Malawian Kwacha)</option>
                    <option value="EGP">EGP (Egyptian Pound)</option>
                    <option value="EUR">EUR (Euro)</option>
                    <option value="GBP">GBP (British Pound)</option>
                </select>
            </div>
            <div class="pay-input-group" style="margin-bottom: 0;">
                <label class="pay-label">Amount</label>
                <?php
                $db = \App\Core\Database::getInstance()->getConnection();
                $minFunding = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'min_funding_amount'")->fetchColumn();
                $minFunding = $minFunding ?: 100;
                ?>
                <input type="number" name="amount" placeholder="0.00" class="pay-input" min="<?= htmlspecialchars($minFunding) ?>" required step="0.01">
            </div>
        </div>

        <button type="submit" class="pay-btn-primary" style="margin-top: 15px;">
            <ion-icon name="shield-checkmark-outline" style="font-size: 1.2rem;"></ion-icon> Proceed to Secure Payment
        </button>
        <p style="text-align: center; font-size: 0.8rem; color: var(--pay-text-muted); margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 4px;">
            <ion-icon name="lock-closed-outline"></ion-icon> Secured by Paystack & Flutterwave
        </p>
    </form>
</div>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
