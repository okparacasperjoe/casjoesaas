<?php
$title = $title ?? 'Casjoe Pay PIN';
$pageTitle = 'Security';
$activeMenu = 'pay';
require_once __DIR__ . '/partials/pay_header.php';
?>

<style>
    .pin-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
    }
    .pin-card {
        background: var(--pay-surface);
        border: 1px solid var(--pay-border);
        border-radius: var(--pay-radius);
        padding: 40px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
    }
    .pin-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 166, 0, 0.05), transparent 60%);
        pointer-events: none;
    }
    .pin-icon {
        font-size: 3rem;
        color: var(--pay-gold);
        margin-bottom: 20px;
    }
    .pin-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
    }
    .pin-desc {
        font-size: 0.9rem;
        color: var(--pay-text-muted);
        margin-bottom: 30px;
        line-height: 1.5;
    }
    .pin-inputs {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 30px;
    }
    .pin-digit {
        width: 50px;
        height: 60px;
        border-radius: 12px;
        background: rgba(255,255,255,0.03);
        border: 2px solid var(--pay-border);
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        text-align: center;
        transition: all 0.3s;
        font-family: 'JetBrains Mono', monospace;
    }
    .pin-digit:focus {
        outline: none;
        border-color: var(--pay-gold);
        background: rgba(255,166,0,0.05);
        box-shadow: 0 0 15px rgba(255,166,0,0.15);
    }
    /* Hide the spin buttons on number inputs */
    .pin-digit::-webkit-outer-spin-button,
    .pin-digit::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .pin-digit[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="pin-container">
    
    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div style="background: rgba(255,77,106,0.1); border: 1px solid rgba(255,77,106,0.3); color: var(--pay-red); padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; width: 100%; max-width: 400px;">
            <ion-icon name="alert-circle" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="pin-card">
        <ion-icon name="<?= $mode === 'setup' ? 'key-outline' : 'lock-closed-outline' ?>" class="pin-icon"></ion-icon>
        
        <h2 class="pin-title"><?= $mode === 'setup' ? 'Create Your PIN' : 'Enter Your PIN' ?></h2>
        
        <p class="pin-desc">
            <?= $mode === 'setup' 
                ? 'Create a 4-digit PIN to secure your Casjoe Pay wallet and authorize transactions.' 
                : 'Enter your 4-digit Casjoe Pay PIN to access your wallet.' ?>
        </p>

        <?php if (!empty($isLocked)): ?>
            <div style="background: rgba(255,77,106,0.15); border: 1px solid rgba(255,77,106,0.4); color: var(--pay-red); padding: 16px; border-radius: 12px; margin-bottom: 24px; text-align: center;">
                <ion-icon name="lock-closed" style="font-size: 24px; margin-bottom: 6px;"></ion-icon>
                <div style="font-weight: 700; font-size: 1rem; margin-bottom: 4px;">PIN Temporarily Locked</div>
                <div style="font-size: 0.85rem; color: #fca5a5;">
                    Too many incorrect attempts. Please wait <strong><?= (int)$lockoutMinutes ?> minute(s)</strong> or reset your PIN below.
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="/pay/pin/process" id="pinForm">
            <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
            <input type="hidden" name="pin" id="fullPin" value="">
            
            <div class="pin-inputs" style="<?= !empty($isLocked) ? 'opacity: 0.3; pointer-events: none;' : '' ?>">
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autofocus autocomplete="off" <?= !empty($isLocked) ? 'disabled' : '' ?>>
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off" <?= !empty($isLocked) ? 'disabled' : '' ?>>
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off" <?= !empty($isLocked) ? 'disabled' : '' ?>>
                <input type="password" class="pin-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" required autocomplete="off" <?= !empty($isLocked) ? 'disabled' : '' ?>>
            </div>

            <button type="submit" class="pay-btn-primary" id="submitBtn" style="opacity: 0.5; pointer-events: none;">
                <?= $mode === 'setup' ? 'Set PIN' : 'Unlock Wallet' ?> <ion-icon name="arrow-forward-outline"></ion-icon>
            </button>
        </form>

        <?php if ($mode === 'verify'): ?>
            <div style="margin-top: 24px; font-size: 0.85rem;">
                <a href="/pay/pin/forgot" style="color: var(--pay-gold, #FFA600); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    <ion-icon name="help-circle-outline"></ion-icon> Forgot PIN? Reset with Password
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.pin-digit');
    const fullPinInput = document.getElementById('fullPin');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('pinForm');

    let step = 1;
    let initialPin = '';

    function checkComplete() {
        let pin = '';
        let allFilled = true;
        inputs.forEach(input => {
            if (!input.value) allFilled = false;
            pin += input.value;
        });

        fullPinInput.value = pin;

        if (allFilled && pin.length === 4) {
            submitBtn.style.opacity = '1';
            submitBtn.style.pointerEvents = 'auto';
            
            <?php if ($mode === 'setup'): ?>
            if (step === 1) {
                // Change UI to confirm mode
                document.querySelector('.pin-title').innerText = 'Confirm Your PIN';
                document.querySelector('.pin-desc').innerText = 'Please enter your 4-digit PIN again to confirm.';
                submitBtn.innerHTML = 'Confirm PIN <ion-icon name="checkmark-done-outline"></ion-icon>';
                
                // Store first pin and reset inputs for confirmation step
                initialPin = pin;
                step = 2;
                
                // Need to do this in a setTimeout so it doesn't instantly re-trigger
                setTimeout(() => {
                    inputs.forEach(input => input.value = '');
                    inputs[0].focus();
                    checkComplete(); // Reset button state
                }, 100);
                
                return; // Stop here, don't submit yet
            } else if (step === 2) {
                if (pin !== initialPin) {
                    // Pins do not match
                    alert("PINs do not match. Let's try again.");
                    step = 1;
                    document.querySelector('.pin-title').innerText = 'Create Your PIN';
                    document.querySelector('.pin-desc').innerText = 'Create a 4-digit PIN to secure your Casjoe Pay wallet and authorize transactions.';
                    submitBtn.innerHTML = 'Set PIN <ion-icon name="arrow-forward-outline"></ion-icon>';
                    
                    setTimeout(() => {
                        inputs.forEach(input => input.value = '');
                        inputs[0].focus();
                        checkComplete();
                    }, 100);
                    
                    return; // Stop here
                } else {
                    // Pins match, auto submit
                    form.submit();
                }
            }
            <?php else: ?>
            // Auto submit when verifying
            form.submit();
            <?php endif; ?>
        } else {
            submitBtn.style.opacity = '0.5';
            submitBtn.style.pointerEvents = 'none';
        }
    }

    inputs.forEach((input, index) => {
        // Auto focus next input
        input.addEventListener('input', (e) => {
            // Replace non-digits
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            checkComplete();
        });

        // Handle backspace to focus previous input
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
                inputs[index - 1].value = '';
                checkComplete();
            }
        });

        // Handle paste
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            if (pastedData) {
                for (let i = 0; i < inputs.length; i++) {
                    if (pastedData[i]) {
                        inputs[i].value = pastedData[i];
                    }
                }
                inputs[Math.min(pastedData.length, 4) - 1].focus();
                checkComplete();
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
