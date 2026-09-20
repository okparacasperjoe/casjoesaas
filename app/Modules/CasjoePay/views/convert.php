<?php
$title = "Convert Currency | Casjoe Pay";
$pageTitle = "Convert Currency";
$activeMenu = "convert";
require_once __DIR__ . '/partials/pay_header.php';
?>

<div class="pay-form-card" style="max-width: 500px; margin: 0 auto;">
    <h2 class="pay-form-header" style="text-align: center; margin-bottom: 24px;">Swap Currencies</h2>
    
    <?php if(isset($_GET['error'])): ?>
        <div style="background: rgba(255,0,0,0.15); color: #ff6b6b; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; border: 1px solid rgba(255,0,0,0.3); font-size: 0.9rem;">
            <ion-icon name="alert-circle-outline" style="vertical-align: middle; margin-right: 6px; font-size: 1.1rem;"></ion-icon>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="/pay/convert/process" method="POST" id="convertForm">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
        
        <div class="pay-input-group">
            <label class="pay-label">From (Source Wallet)</label>
            <select name="from_currency" class="pay-select" id="from_currency" onchange="updateRate()">
                <?php foreach($wallets as $w): ?>
                    <option value="<?= $w['currency'] ?>" data-balance="<?= $w['balance'] ?>">
                        <?= $w['currency'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div style="margin-top: 8px; font-size: 0.8rem; color: var(--pay-primary);">
                Available Balance: <strong id="avail-balance">---</strong>
            </div>
        </div>

        <div style="text-align: center; margin: -10px 0 10px 0;">
            <ion-icon name="swap-vertical-outline" style="font-size: 1.5rem; color: var(--pay-primary); background: rgba(255,166,0,0.1); padding: 8px; border-radius: 50%;"></ion-icon>
        </div>

        <div class="pay-input-group">
            <label class="pay-label">To (Target Currency)</label>
            <select name="to_currency" class="pay-select" id="to_currency" onchange="updateRate()">
                 <option value="NGN">NGN (Nigerian Naira)</option>
                 <option value="USD">USD (US Dollar)</option>
                 <option value="GBP">GBP (British Pound)</option>
                 <option value="EUR">EUR (Euro)</option>
                 <option value="CJC">CJC (Casjoe Token)</option>
            </select>
        </div>

        <div class="pay-input-group">
            <label class="pay-label">Amount to Convert</label>
            <input type="number" name="amount" id="amount" class="pay-input" placeholder="0.00" step="0.01" min="1" required oninput="calculateOutput()">
        </div>

        <div style="text-align: center; margin-bottom: 24px; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1);">
            <div style="color: var(--pay-text-muted); font-size: 0.85rem; margin-bottom: 8px;">Exchange Rate: <span id="rate" style="color: white; font-weight: 600;">Loading...</span></div>
            <div style="color: var(--pay-text-muted); font-size: 0.9rem;">
                You will receive approx:<br>
                <strong id="output" style="color: var(--pay-primary); font-size: 1.6rem; letter-spacing: 1px; line-height: 1.4;">0.00</strong> 
                <span id="target-curr" style="color: white; font-weight: 600;"></span>
            </div>
        </div>

        <button type="submit" class="pay-btn-primary">
            <ion-icon name="swap-horizontal-outline"></ion-icon> Convert Now
        </button>
    </form>
</div>

<script>
    async function updateRate() {
        const fromSel = document.getElementById('from_currency');
        if(!fromSel || fromSel.selectedIndex < 0) return;
        const from = fromSel.value;
        const to = document.getElementById('to_currency').value;
        
        // Update Balance Display
        const bal = parseFloat(fromSel.options[fromSel.selectedIndex].dataset.balance || 0);
        document.getElementById('avail-balance').innerText = bal.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + from;

        document.getElementById('target-curr').innerText = to;
        document.getElementById('rate').innerText = 'Fetching...';

        try {
            const res = await fetch(`/pay/api/rate?from=${from}&to=${to}`);
            const data = await res.json();
            
            if(data.rate) {
                window.currentRate = data.rate;
                document.getElementById('rate').innerText = `1 ${from} = ${data.rate} ${to}`;
                calculateOutput();
            } else {
                document.getElementById('rate').innerText = 'Unavailable';
            }
        } catch (e) {
            console.error(e);
            document.getElementById('rate').innerText = 'Error';
        }
    }

    function calculateOutput() {
        if(!window.currentRate) return;
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const final = amount * window.currentRate;
        document.getElementById('output').innerText = final.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    // Initial load
    updateRate();
</script>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>

