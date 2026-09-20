<?php
$title = $title ?? 'Card History | Casjoe Pay';
$pageTitle = $pageTitle ?? 'Card Transaction History';
$activeMenu = 'naira-cards';
require_once __DIR__ . '/partials/pay_header.php';
?>

<div style="margin-bottom: 20px;">
    <a href="/pay/naira-cards" style="color: var(--pay-gold); font-size: 0.9rem; font-weight: 600;">
        <ion-icon name="arrow-back-outline" style="vertical-align: middle;"></ion-icon> Back to Naira Cards
    </a>
</div>

<?php if (!empty($card)): ?>
<div style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 20px; margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--pay-text-muted); letter-spacing: 0.5px;">Card</span>
            <div style="font-family: 'JetBrains Mono', monospace; font-size: 1.1rem; color: #fff; margin-top: 4px;">
                <?= htmlspecialchars($card['masked_pan'] ?? '****') ?>
            </div>
        </div>
        <div>
            <span style="font-size: 0.7rem; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; font-weight: 700;
                background: <?= $card['card_type'] === 'physical' ? 'rgba(67,97,238,0.15)' : 'rgba(0,214,143,0.12)' ?>;
                color: <?= $card['card_type'] === 'physical' ? 'var(--pay-blue)' : 'var(--pay-green)' ?>;">
                <?= ucfirst($card['card_type'] ?? 'virtual') ?> · <?= htmlspecialchars($card['brand'] ?? 'AfriGo') ?>
            </span>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (empty($transactions)): ?>
    <div style="background: var(--pay-surface); border: 1px dashed var(--pay-border); padding: 50px 30px; border-radius: var(--pay-radius); text-align: center; color: var(--pay-text-muted);">
        <ion-icon name="receipt-outline" style="font-size: 3.5rem; opacity: 0.3; margin-bottom: 15px; color: var(--pay-gold);"></ion-icon>
        <p style="margin: 0; font-size: 0.95rem;">No transactions found for this card.</p>
    </div>
<?php else: ?>
    <div style="background: var(--pay-surface); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--pay-border);">
                    <th style="padding: 14px 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: var(--pay-text-muted); font-weight: 600; letter-spacing: 0.5px;">Date</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: var(--pay-text-muted); font-weight: 600; letter-spacing: 0.5px;">Merchant</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: var(--pay-text-muted); font-weight: 600; letter-spacing: 0.5px;">Channel</th>
                    <th style="padding: 14px 16px; text-align: right; font-size: 0.75rem; text-transform: uppercase; color: var(--pay-text-muted); font-weight: 600; letter-spacing: 0.5px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $txn): ?>
                <tr style="border-bottom: 1px solid var(--pay-border);">
                    <td style="padding: 14px 16px; font-size: 0.85rem; color: var(--pay-text-muted);">
                        <?= htmlspecialchars($txn['date'] ?? $txn['created_at'] ?? '-') ?>
                    </td>
                    <td style="padding: 14px 16px; font-size: 0.9rem; color: #fff; font-weight: 500;">
                        <?= htmlspecialchars($txn['merchant']['name'] ?? $txn['description'] ?? '-') ?>
                    </td>
                    <td style="padding: 14px 16px; font-size: 0.85rem; color: var(--pay-text-muted); text-transform: uppercase;">
                        <?= htmlspecialchars($txn['channel'] ?? $txn['type'] ?? '-') ?>
                    </td>
                    <td style="padding: 14px 16px; text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: var(--pay-red);">
                        ₦<?= number_format((float)($txn['merchantAmount'] ?? $txn['amount'] ?? 0), 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
