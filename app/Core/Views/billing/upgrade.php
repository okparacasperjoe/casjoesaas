<?php
$user = \App\Core\Auth::user();
$tenant = \App\Core\TenantContext::getTenant();
$currency = $_SESSION['currency'] ?? ($user['currency'] ?? ($tenant['currency'] ?? 'NGN'));
if (empty($currency)) $currency = 'NGN';
$isNaira = (strtoupper($currency) === 'NGN');
$currencySymbol = $isNaira ? '₦' : '$';

$planName = $selectedPlan['name'] ?? 'All Access Module';
$planDesc = $selectedPlan['description'] ?? 'Everything you need to scale and automate your operations.';
$rawPrice = $isNaira ? ($selectedPlan['price_ngn'] ?? 28000) : ($selectedPlan['price'] ?? 21);
$price = number_format($rawPrice, $isNaira ? 0 : 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Upgrade Plan | Casjoe Apps</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <style>
        :root {
            --brand-blue: #000066;
            --brand-gold: #FFA600;
            --brand-white: #FFFFFF;
            
            --bg-color: #F8FAFC;
            --card-bg: #FFFFFF;
            --border-color: #E2E8F0;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --radius: 16px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 20px;
        }

        /* --- Ambient Lighting Spheres --- */
        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.25;
        }
        .glow-1 { top: -10%; left: -10%; width: 350px; height: 350px; background: var(--brand-blue); }
        .glow-2 { bottom: -10%; right: -10%; width: 300px; height: 300px; background: var(--brand-gold); opacity: 0.1; }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .header-section h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 0 5px;
            color: #0F172A;
        }
        .header-section p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
        }

        .upgrade-container {
            max-width: 440px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .plan-card {
            background: linear-gradient(135deg, #000066 0%, #060E36 100%);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 102, 0.25), 0 0 20px rgba(255, 166, 0, 0.15);
            border: 2px solid var(--brand-gold);
            position: relative;
            text-align: center;
            color: #FFFFFF;
        }

        .pop-badge {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--brand-gold);
            color: #000033;
            padding: 3px 14px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 8px rgba(255, 166, 0, 0.3);
        }

        .plan-card h2 {
            margin: 5px 0 0;
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--brand-white);
        }

        .price {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--brand-gold);
            margin: 12px 0 6px;
            line-height: 1;
        }
        .price span {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .desc-text {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 16px;
        }

        .features {
            list-style: none;
            padding: 0;
            margin: 15px 0 25px;
            text-align: left;
            display: inline-block;
        }
        .features li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--brand-white);
        }
        .features ion-icon {
            color: var(--brand-gold);
            font-size: 1.1rem;
        }

        .gateway-selector {
            margin-bottom: 20px;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
            text-align: left;
        }
        .gateway-label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .gateway-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .gateway-card {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .gateway-card:hover {
            background: rgba(255, 255, 255, 0.06);
        }
        .gateway-card input {
            accent-color: var(--brand-gold);
            margin: 0;
        }

        .btn-pay {
            width: 100%;
            padding: 12px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 800;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            background: linear-gradient(135deg, var(--brand-gold) 0%, #ff9500 100%);
            color: #000033;
            box-shadow: 0 4px 10px rgba(255, 166, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-pay:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 166, 0, 0.35);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--text-muted);
            font-size: 0.85rem;
            transition: color 0.2s;
            position: relative;
            z-index: 1;
        }
        .back-link:hover {
            color: var(--brand-white);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Ambient Glow Backgrounds -->
<div class="ambient-glow glow-1"></div>
<div class="ambient-glow glow-2"></div>

<div class="header-section">
    <h1>Unlock the Full Power of Casjoe</h1>
    <p>One subscription. Unlimited possibilities.</p>
</div>

<div class="upgrade-container">
    <div class="plan-card">
        <div class="pop-badge">SELECTED MODULE</div>
        <h2><?= htmlspecialchars($planName) ?></h2>
        <div class="price"><?= $currencySymbol ?><?= $price ?><span>/mo</span></div>
        <p class="desc-text"><?= htmlspecialchars($planDesc) ?></p>
        
        <ul class="features">
            <li><ion-icon name="checkmark-circle"></ion-icon> <span><strong>Casjoe ERP</strong> (Finance, HR, CRM)</span></li>
            <li><ion-icon name="checkmark-circle"></ion-icon> <span><strong>Casjoe Pay</strong> (Transfers & Links)</span></li>
            <li><ion-icon name="checkmark-circle"></ion-icon> <span><strong>Casjoe Academy</strong> (LMS)</span></li>
            <li><ion-icon name="checkmark-circle"></ion-icon> <span>Priority 24/7 Support</span></li>
        </ul>

        <form method="POST" action="/billing/pay">
            <!-- Gateway Selection -->
            <div class="gateway-selector">
                <span class="gateway-label">Payment Method</span>
                <div class="gateway-options">
                    <label class="gateway-card">
                        <input type="radio" name="gateway" value="paystack" checked>
                        <span>Paystack (Cards/USSD)</span>
                    </label>
                    <label class="gateway-card">
                        <input type="radio" name="gateway" value="flutterwave">
                        <span>Flutterwave</span>
                    </label>
                    <label class="gateway-card">
                        <input type="radio" name="gateway" value="bank_transfer">
                        <span>Bank Transfer</span>
                    </label>
                    <label class="gateway-card">
                        <input type="radio" name="gateway" value="coupon">
                        <span>Coupon Voucher</span>
                    </label>
                </div>
            </div>

            <div id="couponUpgradeArea" style="display: none; margin-bottom: 18px; padding: 14px; background: rgba(0, 0, 102, 0.4); border: 1px solid #FFA600; border-radius: 12px;">
                <label style="display: block; font-size: 0.75rem; color: #FFA600; font-weight: 800; text-transform: uppercase; margin-bottom: 6px;">Enter Promotional Coupon Code</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="upgCouponInput" name="coupon_code" placeholder="ENTER COUPON CODE" style="flex: 1; padding: 10px 14px; border-radius: 8px; border: 1px solid #FFA600; background: rgba(255,255,255,0.1); color: #fff; text-transform: uppercase; font-weight: 700; outline: none;">
                    <button type="button" onclick="previewUpgradeCoupon()" class="btn-pay" style="width: auto; padding: 10px 18px; font-size: 0.85rem;">Preview</button>
                </div>

                <div id="upgBreakdown" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed rgba(255,166,0,0.4); font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #94A3B8;">Regular Price:</span>
                        <span style="text-decoration: line-through; color: #fca5a5;" id="upgOrig"><?= $currencySymbol ?><?= $price ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #FFA600;">Coupon Discount:</span>
                        <span style="color: #34d399; font-weight: 800;" id="upgDisc">-$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.05rem; font-weight: 800; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px;">
                        <span style="color: #fff;">New Total Due:</span>
                        <span style="color: #34d399;" id="upgFinal"><?= $currencySymbol ?>0.00</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-pay" id="upgSubmitBtn">
                <ion-icon name="card-outline"></ion-icon> Subscribe Now
            </button>
            <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 10px; margin-bottom: 0;">
                <ion-icon name="lock-closed" style="vertical-align: middle; margin-right: 4px;"></ion-icon> Secure 128-bit SSL Encrypted Payment
            </p>
        </form>
    </div>
</div>

<div style="text-align: center;">
    <a href="/billing" class="back-link">Cancel and return to billing dashboard</a>
</div>

<script>
    document.querySelectorAll('input[name=gateway]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'coupon') {
                document.getElementById('couponUpgradeArea').style.display = 'block';
                document.getElementById('upgSubmitBtn').innerHTML = '<ion-icon name="checkmark-done-circle"></ion-icon> Activate With Coupon';
                document.querySelector('form').action = '/billing/redeem-coupon';
            } else {
                document.getElementById('couponUpgradeArea').style.display = 'none';
                document.getElementById('upgSubmitBtn').innerHTML = '<ion-icon name="card-outline"></ion-icon> Subscribe Now';
                document.querySelector('form').action = '/billing/pay';
            }
        });
    });

    async function previewUpgradeCoupon() {
        const code = document.getElementById('upgCouponInput').value.trim();
        if (!code) return alert('Please enter a coupon code.');
        const res = await fetch('/billing/validate-coupon?code=' + encodeURIComponent(code) + '&base_usd=<?= $rawPrice ?>&base_ngn=<?= $rawPrice ?>');
        const data = await res.json();
        if (!data.valid) {
            alert(data.error || 'Invalid coupon code.');
            return;
        }
        const isNaira = <?= $isNaira ? 'true' : 'false' ?>;
        const sym = '<?= $currencySymbol ?>';
        const orig = isNaira ? data.base_ngn : data.base_usd;
        const disc = isNaira ? data.discount_ngn : data.discount_usd;
        const finalP = isNaira ? data.final_ngn : data.final_usd;

        document.getElementById('upgOrig').textContent = sym + orig.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('upgDisc').textContent = '-' + sym + disc.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + data.description + ')';
        document.getElementById('upgFinal').textContent = sym + finalP.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('upgBreakdown').style.display = 'block';
        document.getElementById('upgSubmitBtn').innerHTML = '<ion-icon name="checkmark-done-circle"></ion-icon> Activate Module (' + sym + finalP.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ')';
    }
</script>

</body>
</html>
