<?php
$title = $title ?? 'Reset Casjoe Pay PIN';
$pageTitle = 'Security';
$activeMenu = 'pay';
require_once __DIR__ . '/partials/pay_header.php';
?>

<style>
    .forgot-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
        padding: 20px;
    }
    .forgot-card {
        background: var(--pay-surface, #111827);
        border: 1px solid var(--pay-border, rgba(255,255,255,0.1));
        border-radius: var(--pay-radius, 16px);
        padding: 40px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        position: relative;
        color: #fff;
    }
    .forgot-icon {
        font-size: 3rem;
        color: var(--pay-gold, #FFA600);
        margin-bottom: 16px;
    }
    .forgot-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .forgot-desc {
        font-size: 0.9rem;
        color: var(--pay-text-muted, #94a3b8);
        margin-bottom: 24px;
        line-height: 1.5;
    }
    .form-control-pay {
        width: 100%;
        padding: 12px 16px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--pay-border, rgba(255,255,255,0.15));
        color: #fff;
        font-size: 1rem;
        box-sizing: border-box;
        outline: none;
        transition: 0.3s;
        margin-bottom: 20px;
    }
    .form-control-pay:focus {
        border-color: var(--pay-gold, #FFA600);
        box-shadow: 0 0 10px rgba(255,166,0,0.2);
    }
    .otp-input {
        letter-spacing: 8px;
        font-size: 1.8rem;
        text-align: center;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
    }
</style>

<div class="forgot-container">
    <!-- Flash Alerts -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div style="background: rgba(255,77,106,0.1); border: 1px solid rgba(255,77,106,0.3); color: var(--pay-red, #ff4d6a); padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; width: 100%; max-width: 440px;">
            <ion-icon name="alert-circle" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div style="background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.3); color: #34d399; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; width: 100%; max-width: 440px;">
            <ion-icon name="checkmark-circle" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="forgot-card">
        <?php if ($step === 'password'): ?>
            <!-- STEP 1: Confirm Account Password -->
            <ion-icon name="shield-checkmark-outline" class="forgot-icon"></ion-icon>
            <h2 class="forgot-title">Identity Verification</h2>
            <p class="forgot-desc">
                To reset your PIN, please verify ownership of this wallet by entering your account login password.
            </p>

            <form method="POST" action="/pay/pin/send-otp">
                <div style="text-align: left; margin-bottom: 6px;">
                    <label style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">Account Password</label>
                </div>
                <input type="password" name="password" class="form-control-pay" placeholder="Enter account password" required autofocus>

                <button type="submit" class="pay-btn-primary" style="width: 100%; padding: 12px; font-weight: 700;">
                    Verify Password & Send Code <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
            </form>

            <div style="margin-top: 20px; font-size: 0.85rem;">
                <a href="/pay/pin/verify" style="color: #94a3b8; text-decoration: none;">
                    &larr; Back to PIN verification
                </a>
            </div>

        <?php elseif ($step === 'otp'): ?>
            <!-- STEP 2: Enter Email OTP -->
            <ion-icon name="mail-unread-outline" class="forgot-icon"></ion-icon>
            <h2 class="forgot-title">Enter Verification Code</h2>
            <p class="forgot-desc">
                We sent a 6-digit security code to your registered email address. Please enter it below to authorize your PIN reset.
            </p>

            <form method="POST" action="/pay/pin/confirm-otp">
                <div style="margin-bottom: 6px; text-align: left;">
                    <label style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">6-Digit Security Code</label>
                </div>
                <input type="text" name="otp" class="form-control-pay otp-input" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="123456" required autofocus autocomplete="off">

                <button type="submit" class="pay-btn-primary" style="width: 100%; padding: 12px; font-weight: 700;">
                    Authorize PIN Reset <ion-icon name="checkmark-done-outline"></ion-icon>
                </button>
            </form>

            <div style="margin-top: 20px; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                <a href="/pay/pin/forgot" style="color: #94a3b8; text-decoration: none;">
                    Resend Code
                </a>
                <a href="/pay/pin/verify" style="color: #94a3b8; text-decoration: none;">
                    Cancel
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
