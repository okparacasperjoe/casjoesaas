<?php
$title = "Fund Virtual Card | Casjoe Pay";
$pageTitle = "Fund Virtual Card";
$activeMenu = "cards";
require_once __DIR__ . '/partials/pay_header.php';
?>

<div style="max-width: 520px; margin: 30px auto;">
    <div style="margin-bottom: 20px;">
        <a href="/pay/cards" style="color: #FFA600; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            <ion-icon name="arrow-back-outline"></ion-icon> Back to Virtual Cards
        </a>
    </div>

    <div style="background: #02052e; border: 1.5px solid rgba(255,166,0,0.35); border-radius: 18px; padding: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
        
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(255,166,0,0.1); border: 1px solid #FFA600; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; color: #FFA600; font-size: 1.6rem;">
                <ion-icon name="card-outline"></ion-icon>
            </div>
            <h2 style="color: #ffffff; font-size: 1.4rem; font-weight: 800; margin: 0 0 6px;">Fund Virtual Card</h2>
            <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; margin: 0;">Add funds to your USD virtual card directly from your NGN wallet.</p>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div style="background: rgba(227,52,47,0.15); border: 1px solid #e3342f; color: #ff6b6b; padding: 12px 16px; border-radius: 10px; font-size: 0.85rem; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cards)): ?>
            <div style="background: rgba(255,166,0,0.1); border: 1px solid rgba(255,166,0,0.3); border-radius: 12px; padding: 25px; text-align: center; color: #FFA600; margin-bottom: 20px;">
                <ion-icon name="information-circle-outline" style="font-size: 2.2rem; margin-bottom: 8px;"></ion-icon>
                <h4 style="margin: 0 0 8px; color: #fff; font-size: 1rem;">No Reloadable Cards Found</h4>
                <p style="margin: 0 0 15px; font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.5;">Instant Lite cards are non-reloadable and cannot be topped up. To use recurring card funding, please create a Reloadable Business Card.</p>
                <a href="/pay/cards" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #FFA600; color: #000066; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 0.88rem;">
                    <ion-icon name="add-circle-outline"></ion-icon> Create Reloadable Card
                </a>
            </div>
        <?php else: ?>
        <form method="POST" action="/pay/cards/fund">
            
            <!-- Card Selection -->
            <div class="pay-input-group" style="margin-bottom: 20px;">
                <label class="pay-label" style="color: #FFA600; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Select Reloadable Card</label>
                <select name="card_id" class="pay-select" style="background: rgba(255,255,255,0.05); color: #ffffff; border: 1px solid rgba(255,166,0,0.3); padding: 12px 16px; border-radius: 10px; width: 100%; font-family: 'JetBrains Mono', monospace;" required>
                    <?php foreach ($cards as $c): ?>
                        <option value="<?= htmlspecialchars($c['card_id']) ?>" <?= ($selectedCardId === $c['card_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['masked_pan'] ?? $c['card_id']) ?> (Balance: $<?= number_format((float)($c['balance'] ?? 0), 2) ?> USD)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Funding Amount in USD -->
            <div class="pay-input-group" style="margin-bottom: 20px;">
                <label class="pay-label" style="color: #FFA600; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Deposit Amount (USD)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #FFA600; font-weight: 800; font-size: 1.1rem;">$</span>
                    <input type="number" name="amount" id="fundAmount" placeholder="<?= number_format($minFunding ?? 3, 2) ?>" min="<?= htmlspecialchars($minFunding ?? 3) ?>" step="any" class="pay-input" style="padding-left: 35px; background: rgba(255,255,255,0.05); color: #ffffff; border: 1px solid rgba(255,166,0,0.3); border-radius: 10px; font-family: 'JetBrains Mono', monospace; font-size: 1.1rem; font-weight: 700; width: 100%; box-sizing: border-box;" required oninput="calcNgnEquivalent(this.value)">
                </div>
                <span style="font-size: 0.72rem; color: rgba(255,255,255,0.5); margin-top: 6px; display: block;">Minimum funding amount: $<?= number_format($minFunding ?? 3, 2) ?> USD</span>
            </div>

            <!-- Rate & Wallet Balance Box -->
            <div style="background: rgba(255,166,0,0.06); border: 1px solid rgba(255,166,0,0.2); border-radius: 12px; padding: 16px; margin-bottom: 25px; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: rgba(255,255,255,0.6);">Exchange Rate:</span>
                    <strong style="color: #ffffff;">$1.00 USD = ₦<?= number_format($rate ?? 1600, 2) ?> NGN</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: rgba(255,255,255,0.6);">Available NGN Wallet:</span>
                    <strong style="color: #00d68f; font-family: 'JetBrains Mono', monospace;">₦<?= number_format((float)($ngnBalance ?? 0), 2) ?> NGN</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed rgba(255,166,0,0.2); padding-top: 8px; margin-top: 8px;">
                    <span style="color: #FFA600; font-weight: 700;">Total NGN to Deduct:</span>
                    <strong id="totalNgnText" style="color: #FFA600; font-size: 1rem; font-family: 'JetBrains Mono', monospace;">₦0.00 NGN</strong>
                </div>
            </div>

            <button type="submit" class="pay-btn-primary" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #FFA600 0%, #ff8c00 100%); border: none; border-radius: 12px; color: #000066; font-weight: 800; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 25px rgba(255,166,0,0.3);">
                <ion-icon name="wallet-outline" style="font-size: 1.3rem;"></ion-icon> Fund Virtual Card Now
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
function calcNgnEquivalent(val) {
    const usd = parseFloat(val) || 0;
    const rate = <?= json_encode((float)($rate ?? 1600)) ?>;
    const totalNgn = usd * rate;
    document.getElementById('totalNgnText').innerText = '₦' + totalNgn.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' NGN';
}
</script>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
