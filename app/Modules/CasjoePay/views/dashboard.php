<?php
$title = "Casjoe Pay | Dashboard";
$pageTitle = "Casjoe Pay";
$activeMenu = "dashboard";
require_once __DIR__ . '/partials/pay_header.php';

// Get primary card, phone and account
$card = !empty($cards) ? $cards[0] : null;
$phone = !empty($phones) ? $phones[0] : null;
$account = !empty($accounts) ? $accounts[0] : null;
$walletCurr = $activeWallet['currency'] ?? 'NGN';
$walletBal = $activeWallet['balance'] ?? 0.00;
?>

<style>
/* Dashboard-specific Light Theme Overrides */
body, .main-content {
    background-color: #f4f6f9 !important;
}
.top-bar h2 {
    color: #1a1a2e !important;
}

/* App Grid specific styles */
.app-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 40px;
}
.app-icon-btn {
    background: #000066;
    border: 1px solid rgba(255,166,0,0.2);
    border-radius: var(--pay-radius);
    padding: 20px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #ffffff;
    transition: all 0.3s;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,102,0.15);
}
.app-icon-btn:hover {
    transform: translateY(-5px);
    border-color: var(--pay-gold);
    box-shadow: 0 10px 25px rgba(255,166,0,0.15);
}
.app-icon-btn ion-icon {
    font-size: 2rem;
    color: var(--pay-gold);
    margin-bottom: 12px;
}
.app-icon-btn span {
    font-size: 0.85rem;
    font-weight: 600;
}
.wallet-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    overflow-x: auto;
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
.wallet-tabs::-webkit-scrollbar {
    display: none;
}
.wallet-tab-btn {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.08);
    color: #7a7a9a;
    padding: 10px 24px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s;
}
.wallet-tab-btn.active {
    background: #000066;
    color: #ffffff;
    border-color: #000066;
    box-shadow: 0 4px 12px rgba(0,0,102,0.2);
}
.wallet-hero {
    min-width: 100%;
    scroll-snap-align: center;
    background: linear-gradient(135deg, #000066 0%, #000033 100%);
    border: 1px solid rgba(255,166,0,0.2);
    border-radius: var(--pay-radius);
    padding: 35px 30px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,102,0.15);
    position: relative;
    overflow: hidden;
    box-sizing: border-box;
}
.wallet-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    left: -50px;
    width: 150px;
    height: 150px;
    background: rgba(255,166,0,0.15);
    filter: blur(40px);
    border-radius: 50%;
}
.wallet-balance {
    font-size: 3.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 15px 0;
    font-family: 'JetBrains Mono', monospace;
    letter-spacing: -1px;
}
.wallet-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 30px;
}
.wallet-action-btn {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.15);
    color: #ffffff;
    padding: 14px 28px;
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}
.wallet-action-btn:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,166,0,0.5);
    transform: translateY(-2px);
}
.wallet-action-btn.primary {
    background: var(--pay-gold);
    color: #000;
    border-color: var(--pay-gold);
    box-shadow: 0 8px 20px rgba(255,166,0,0.2);
}
.wallet-action-btn.primary:hover {
    background: #ffb52e;
    box-shadow: 0 10px 25px rgba(255,166,0,0.3);
}

.section-title {
    margin: 0 0 20px 0; 
    font-size: 1.25rem; 
    font-weight: 700; 
    color: #1a1a2e;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .app-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .wallet-balance {
        font-size: 2.5rem;
    }
    .wallet-actions {
        flex-wrap: wrap;
    }
    .wallet-action-btn {
        flex: 1 1 calc(50% - 15px);
        justify-content: center;
        padding: 12px 16px;
    }
    .wallet-hero {
        padding: 25px 20px;
    }
}
</style>

<!-- Wallet Tabs -->
<div class="wallet-tabs">
    <?php if (!empty($wallets)): ?>
        <?php foreach ($wallets as $index => $wallet): ?>
            <button class="wallet-tab-btn <?= $index === 0 ? 'active' : '' ?>" onclick="switchWallet(<?= $index ?>)">
                <?= htmlspecialchars($wallet['currency']) ?> Wallet
            </button>
        <?php endforeach; ?>
    <?php endif; ?>
    <button class="wallet-tab-btn" style="padding: 10px 15px; color: #000066; border-color: rgba(0,0,102,0.3); border-style: dashed;" onclick="document.getElementById('addWalletModal').style.display='flex'">
        <ion-icon name="add-outline" style="vertical-align: middle; font-size: 1.2rem;"></ion-icon>
    </button>
</div>

<!-- Wallets Container -->
<div id="wallet-container">
    <?php if (empty($wallets)): ?>
        <div class="wallet-hero">
            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Total Balance</div>
            <div class="wallet-balance">
                <span style="font-size: 1.8rem; color: #FFA600; vertical-align: middle; margin-right: 5px;"><?= htmlspecialchars($walletCurr) ?></span><?= number_format($walletBal, 2) ?>
            </div>
            <div class="wallet-actions">
                <a href="/pay/fund" class="wallet-action-btn primary"><ion-icon name="add-outline"></ion-icon> Fund</a>
                <a href="/pay/transfer" class="wallet-action-btn"><ion-icon name="paper-plane-outline"></ion-icon> Transfer</a>
                <a href="/pay/convert" class="wallet-action-btn"><ion-icon name="swap-horizontal-outline"></ion-icon> Convert</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($wallets as $index => $wallet): ?>
            <div class="wallet-hero wallet-hero-item" id="wallet-hero-<?= $index ?>" style="<?= $index !== 0 ? 'display: none;' : '' ?>">
                <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;">Total Balance</div>
                <div class="wallet-balance">
                    <span style="font-size: 1.8rem; color: #FFA600; vertical-align: middle; margin-right: 5px;"><?= htmlspecialchars($wallet['currency']) ?></span><?= number_format($wallet['balance'], 2) ?>
                </div>
                <div class="wallet-actions">
                    <a href="/pay/fund?currency=<?= urlencode($wallet['currency']) ?>" class="wallet-action-btn primary"><ion-icon name="add-outline"></ion-icon> Fund</a>
                    <a href="/pay/transfer?currency=<?= urlencode($wallet['currency']) ?>" class="wallet-action-btn"><ion-icon name="paper-plane-outline"></ion-icon> Transfer</a>
                    <a href="/pay/convert?from=<?= urlencode($wallet['currency']) ?>" class="wallet-action-btn"><ion-icon name="swap-horizontal-outline"></ion-icon> Convert</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function switchWallet(index) {
    // Hide all heroes
    document.querySelectorAll('.wallet-hero-item').forEach(el => el.style.display = 'none');
    // Remove active class from all tabs
    document.querySelectorAll('.wallet-tab-btn').forEach(el => el.classList.remove('active'));
    
    // Show selected hero
    document.getElementById('wallet-hero-' + index).style.display = 'block';
    // Add active class to clicked tab
    event.currentTarget.classList.add('active');
}
</script>

<h3 class="section-title">Services</h3>
<!-- App Grid -->
<div class="app-grid">
    <a href="/pay/cards" class="app-icon-btn">
        <ion-icon name="card-outline"></ion-icon>
        <span>Virtual Card</span>
    </a>
    <a href="/pay/virtual-bank" class="app-icon-btn">
        <ion-icon name="business-outline"></ion-icon>
        <span>Virtual Bank</span>
    </a>
    <a href="/pay/links" class="app-icon-btn">
        <ion-icon name="link-outline"></ion-icon>
        <span>Payment Links</span>
    </a>
    <a href="/pay/requests" class="app-icon-btn">
        <ion-icon name="receipt-outline"></ion-icon>
        <span>Requests (Invoicing)</span>
    </a>
</div>

<h3 class="section-title">Assets & Accounts</h3>
<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-bottom: 40px;">
    <!-- Column 1: My Cards -->
    <div>
        <?php if ($card): ?>
            <div class="virtual-card-container" style="max-width: 380px;">
                <!-- Clean Simple Virtual Card -->
                <div class="virtual-card" style="background: linear-gradient(135deg, #000066 0%, #000033 100%); border: 1.5px solid rgba(255,166,0,0.4); border-radius: 20px; padding: 22px 25px; color: #ffffff; width: 100%; min-height: 220px; position: relative; box-shadow: 0 15px 35px rgba(0,0,102,0.25); display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                    
                    <!-- Row 1: Brand & Status -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <!-- Logo CASJOE -->
                        <div>
                            <span style="font-weight: 800; color: #ffffff; font-size: 1.25rem; letter-spacing: -0.5px;">casjoe</span><span style="font-weight: 800; color: #FFA600; font-size: 1.25rem; letter-spacing: 0.5px;">PAY</span>
                        </div>
                        <!-- Status Badge -->
                        <span style="border: 1px solid <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#e3342f' : '#FFA600' ?>; color: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#e3342f' : '#FFA600' ?>; font-size: 0.65rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'rgba(227,52,47,0.15)' : 'rgba(255,166,0,0.1)' ?>; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?= htmlspecialchars($card['status'] ?? 'ACTIVE') ?>
                        </span>
                    </div>
                    
                    <!-- Row 2: Chip & Visa Logo -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 15px;">
                        <!-- Chip -->
                        <div style="width: 44px; height: 32px; background: linear-gradient(135deg, #ffd166 0%, #ff9900 50%, #d47a00 100%); border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3);"></div>
                        <!-- Visa Logo -->
                        <div style="font-style: italic; font-weight: 900; font-size: 1.8rem; color: #fff; line-height: 1; letter-spacing: -1px;">VISA</div>
                    </div>

                    <!-- Row 3: Card Number -->
                    <div id="pan-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; font-size: 1.4rem; letter-spacing: 3px; color: #FFA600; margin-top: 15px; font-weight: 700; text-shadow: 0 2px 6px rgba(0,0,0,0.5);">
                        <?php 
                            $pan = $card['masked_pan'] ?? '4865****8571';
                            if (strpos($pan, '****') !== false) {
                                $pan = str_replace('****', ' •••• •••• ', $pan);
                            }
                            echo htmlspecialchars($pan);
                        ?>
                    </div>

                    <!-- Row 4: Footer Meta -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 10px; font-size: 0.75rem; color: rgba(255,255,255,0.5);">
                        <div>
                            <span style="font-size: 0.55rem; text-transform: uppercase; display: block; margin-bottom: 2px; letter-spacing: 0.5px; color: rgba(255,255,255,0.6);">Card Holder</span>
                            <span style="color: #fff; font-weight: 700; font-size: 0.88rem; text-transform: uppercase; letter-spacing: 1px; font-family: 'JetBrains Mono', monospace;"><?= htmlspecialchars($_SESSION['user_name'] ?? $card['card_holder'] ?? 'CASPER OKPARA') ?></span>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.55rem; text-transform: uppercase; display: block; margin-bottom: 2px; letter-spacing: 0.5px; color: rgba(255,255,255,0.6);">Expires</span>
                            <span id="exp-<?= $card['card_id'] ?>" style="color: #FFA600; font-weight: 700; font-size: 0.9rem; font-family: 'JetBrains Mono', monospace;">
                                <?= htmlspecialchars($card['start_month'] ?? '--') ?>/<?= htmlspecialchars($card['start_year'] ?? '--') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Bar & Reveal Details Section -->
                <div style="margin-top: 12px; background: #ffffff; border: 1px solid rgba(0,0,102,0.1); border-radius: 14px; padding: 15px; box-shadow: 0 4px 15px rgba(0,0,102,0.06);">
                    <div style="display: flex; gap: 8px;">
                        <button type="button" onclick="revealCardDetails('<?= $card['card_id'] ?>')" id="btn-reveal-<?= $card['card_id'] ?>" style="flex: 2; padding: 10px 12px; background: #000066; border: none; border-radius: 8px; color: #ffffff; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;">
                            <ion-icon name="eye-outline" style="font-size: 1rem; color: #FFA600;"></ion-icon> Reveal Details & Billing
                        </button>

                        <button type="button" onclick="toggleCardFreeze('<?= $card['card_id'] ?>')" id="btn-freeze-<?= $card['card_id'] ?>" style="flex: 1; padding: 10px 12px; background: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'rgba(0,214,143,0.1)' : 'rgba(227,52,47,0.1)' ?>; border: 1px solid <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#00d68f' : '#e3342f' ?>; border-radius: 8px; color: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#00d68f' : '#e3342f' ?>; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;">
                            <ion-icon name="snow-outline"></ion-icon>
                            <span><?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'Unfreeze' : 'Freeze' ?></span>
                        </button>

                        <button type="button" onclick="terminateCard('<?= $card['card_id'] ?>')" style="flex: 1; padding: 10px 12px; background: rgba(227,52,47,0.15); border: 1px solid #e3342f; border-radius: 8px; color: #e3342f; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px;">
                            <ion-icon name="trash-outline"></ion-icon>
                            <span>Terminate</span>
                        </button>
                    </div>

                    <!-- Interactive Details Box -->
                    <div id="details-<?= $card['card_id'] ?>" style="display: none; margin-top: 14px; border-top: 1px dashed rgba(0,0,102,0.15); padding-top: 14px;">
                        
                        <!-- Card Balance -->
                        <div style="background: rgba(0,0,102,0.03); border: 1px solid rgba(0,0,102,0.08); border-radius: 8px; padding: 12px; text-align: center; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="text-align: left;">
                                <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; color: #7a7a9a; display: block;">Card Balance</span>
                                <span style="font-size: 1.4rem; font-weight: 800; color: #000066;">$<span id="bal-<?= $card['card_id'] ?>">0.00</span> USD</span>
                            </div>
                            <a href="/pay/cards/fund?id=<?= $card['card_id'] ?>" style="padding: 7px 14px; font-size: 0.75rem; border-radius: 6px; background: #FFA600; color: #000066; font-weight: 700; text-decoration: none;">
                                + Fund Card
                            </a>
                        </div>

                        <!-- Card Info Grid with Copy Buttons -->
                        <div style="display: grid; gap: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f4f6f9; padding: 8px 10px; border-radius: 6px;">
                                <div>
                                    <span style="font-size: 0.6rem; color: #7a7a9a; display: block;">Card Number</span>
                                    <span id="full-pan-text-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; color: #000066; font-size: 0.85rem; font-weight: 700;">•••• •••• •••• ••••</span>
                                </div>
                                <button type="button" id="dash-copy-pan-<?= $card['card_id'] ?>" onclick="copyCardInfo('full-pan-text-<?= $card['card_id'] ?>', 'Card Number')" style="background: #000066; border: none; color: #FFA600; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer; font-weight: 600;">
                                    Copy
                                </button>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <div style="background: #f4f6f9; padding: 8px 10px; border-radius: 6px;">
                                    <span style="font-size: 0.6rem; color: #7a7a9a; display: block;">Expiry Date</span>
                                    <span id="full-exp-text-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; color: #000066; font-size: 0.85rem; font-weight: 700;">--/--</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; background: #f4f6f9; padding: 8px 10px; border-radius: 6px;">
                                    <div>
                                        <span style="font-size: 0.6rem; color: #7a7a9a; display: block;">CVV</span>
                                        <span id="full-cvv-text-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; color: #FFA600; font-size: 0.85rem; font-weight: 800;">***</span>
                                    </div>
                                    <button type="button" id="dash-copy-cvv-<?= $card['card_id'] ?>" onclick="copyCardInfo('full-cvv-text-<?= $card['card_id'] ?>', 'CVV')" style="background: #000066; border: none; color: #FFA600; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; cursor: pointer; font-weight: 600;">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div style="background: #f4f6f9; padding: 10px; border-radius: 6px; font-size: 0.78rem;">
                            <div style="margin-bottom: 4px;"><span style="color: #7a7a9a;">Address:</span> <strong style="color: #000066;" id="addr-<?= $card['card_id'] ?>">...</strong></div>
                            <div style="margin-bottom: 4px;"><span style="color: #7a7a9a;">City/State:</span> <strong style="color: #000066;"><span id="city-<?= $card['card_id'] ?>">...</span>, <span id="state-<?= $card['card_id'] ?>">...</span></strong></div>
                            <div style="margin-bottom: 4px;"><span style="color: #7a7a9a;">Zip Code:</span> <strong style="color: #000066;" id="zip-<?= $card['card_id'] ?>">23401</strong></div>
                            <div><span style="color: #7a7a9a;">Country:</span> <strong style="color: #000066;" id="country-<?= $card['card_id'] ?>">Nigeria</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: rgba(0,0,102,0.03); border: 1px dashed rgba(0,0,102,0.15); border-radius: 20px; max-width: 380px; height: 220px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-sizing: border-box; gap: 14px;">
                <ion-icon name="card-outline" style="font-size: 2.8rem; color: rgba(0,0,102,0.3);"></ion-icon>
                <p style="margin:0; color: #7a7a9a; font-size: 0.85rem; font-weight: 500;">No virtual card created yet</p>
                <a href="/pay/cards" style="background: #000066; color: #FFA600; font-weight: 700; font-size: 0.82rem; padding: 9px 22px; border-radius: 30px; text-decoration: none; letter-spacing: 0.3px;">Get Virtual Card</a>
            </div>
        <?php endif; ?>
    </div>



    <!-- Column 3: Virtual Bank Account -->
    <div>
        <?php if ($account): ?>
            <div class="virtual-account-card" style="background: linear-gradient(135deg, #000066 0%, #000033 100%); border: 1px solid rgba(255,166,0,0.2); border-radius: 20px; padding: 25px; color: #ffffff; max-width: 380px; height: 220px; position: relative; box-shadow: 0 15px 35px rgba(0,0,102,0.15); display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Bank Name -->
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="business" style="font-size: 1.3rem; color: #FFA600;"></ion-icon>
                        <span style="font-weight: 800; font-size: 1rem; color: #ffffff; letter-spacing: 0.5px;"><?= htmlspecialchars($account['bank_name'] ?? 'PAGA') ?></span>
                    </div>
                    <!-- Status Badge -->
                    <span style="border: 1px solid var(--pay-green); color: var(--pay-green); font-size: 0.65rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: rgba(0,214,143,0.1); text-transform: uppercase; letter-spacing: 0.5px;">
                        <?= htmlspecialchars($account['status'] ?? 'ACTIVE') ?>
                    </span>
                </div>

                <!-- Account Name -->
                <div style="margin-top: 15px;">
                    <span style="font-size: 0.6rem; text-transform: uppercase; display: block; margin-bottom: 2px; letter-spacing: 0.5px; color: var(--pay-text-muted);">Account Name</span>
                    <span style="color: #fff; font-weight: 700; font-size: 1.15rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        <?= htmlspecialchars($account['account_name'] ?? 'CASPER OKPARA') ?>
                    </span>
                </div>

                <!-- Account Number -->
                <div style="margin-top: 15px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="font-size: 0.6rem; text-transform: uppercase; display: block; margin-bottom: 2px; letter-spacing: 0.5px; color: var(--pay-text-muted);">Account Number</span>
                        <span style="color: var(--pay-gold); font-weight: 800; font-size: 1.35rem; letter-spacing: 1px; font-family: 'JetBrains Mono', monospace;">
                            <?= htmlspecialchars($account['account_number'] ?? '2161039965') ?>
                        </span>
                    </div>
                    <!-- Copy Icon -->
                    <div style="background: rgba(255,255,255,0.05); border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.05); cursor: pointer; transition: all 0.2s;" 
                         onmouseover="this.style.background='rgba(255,255,255,0.1)'" 
                         onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                         onclick="copyToClipboard('<?= htmlspecialchars($account['account_number'] ?? '2161039965') ?>', this.querySelector('ion-icon'))">
                        <ion-icon name="copy-outline" style="font-size: 1.15rem; color: var(--pay-gold); transition: all 0.2s;"></ion-icon>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="/pay/cards?tab=accounts" class="virtual-account-placeholder" style="background: rgba(12,12,30,0.4); border: 2px dashed rgba(255,255,255,0.1); border-radius: 20px; max-width: 380px; height: 220px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-decoration: none; color: var(--pay-text-muted); transition: all 0.3s; box-sizing: border-box;">
                <ion-icon name="business-outline" style="font-size: 3rem; color: var(--pay-gold); margin-bottom: 10px;"></ion-icon>
                <span style="font-weight: 600; color: var(--pay-text);">Generate Bank Account</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Transactions Section -->
<div class="fintech-card" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: var(--pay-radius); padding: 35px 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <h3 style="margin: 0 0 25px 0; font-size: 1.35rem; font-weight: 700; color: #1a1a2e; letter-spacing: 0.5px;">Recent Transactions</h3>
    <?php if (empty($transactions)): ?>
        <div style="text-align: center; padding: 40px 20px; color: var(--pay-text-muted);">
            <ion-icon name="receipt-outline" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3; color: var(--pay-gold);"></ion-icon>
            <p style="margin: 0; font-size: 0.95rem;">No transactions recorded yet.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column;">
            <?php foreach ($transactions as $index => $txn): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0; <?= $index < count($transactions) - 1 ? 'border-bottom: 1px solid rgba(0,0,0,0.05);' : '' ?>">
                    <div>
                        <div style="font-weight: 600; color: #1a1a2e; font-size: 1.05rem;"><?= htmlspecialchars($txn['description'] ?? 'Transaction') ?></div>
                        <div style="font-size: 0.8rem; color: var(--pay-text-muted); margin-top: 6px;"><?= date('Y-m-d H:i:s', strtotime($txn['created_at'])) ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; font-size: 1.15rem; color: <?= $txn['type'] === 'credit' ? 'var(--pay-green)' : 'var(--pay-red)' ?>;">
                            <?= $txn['type'] === 'credit' ? '+' : '-' ?> <?= htmlspecialchars($txn['currency']) ?> <?= number_format($txn['amount'], 2) ?>
                        </div>
                        <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--pay-text-muted); margin-top: 6px; font-weight: 600;">
                            <?= htmlspecialchars($txn['status']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

function revealCardDetails(cardId) {
    const btn = document.getElementById('btn-reveal-' + cardId);
    const detailsDiv = document.getElementById('details-' + cardId);
    
    if (detailsDiv.style.display === 'block') {
        detailsDiv.style.display = 'none';
        btn.innerHTML = '<ion-icon name="eye-outline" style="font-size: 1rem; color: #FFA600;"></ion-icon> Reveal Details & Billing';
        return;
    }

    btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Fetching...';
    btn.disabled = true;

    fetch('/pay/cards/details?card_id=' + encodeURIComponent(cardId))
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<ion-icon name="eye-off-outline" style="font-size: 1rem; color: #FFA600;"></ion-icon> Hide Details';
            btn.disabled = false;
            if (data.success) {
                // Use masked_pan if available
                const displayPan = data.details.masked_pan
                    ? data.details.masked_pan.replace(/(.{4})/g, '$1 ').trim()
                    : null;
                
                if (displayPan && document.getElementById('pan-' + cardId)) {
                    document.getElementById('pan-' + cardId).innerText = displayPan;
                }
                if (data.details.expiry && document.getElementById('exp-' + cardId)) {
                    document.getElementById('exp-' + cardId).innerText = data.details.expiry;
                }
                if (data.details.cvv && document.getElementById('cvv-' + cardId)) {
                    document.getElementById('cvv-' + cardId).innerText = data.details.cvv;
                }
                
                if (data.details.card_number_url) {
                    const panEl = document.getElementById('full-pan-text-' + cardId);
                    const cvvEl = document.getElementById('full-cvv-text-' + cardId);
                    if (panEl) {
                        panEl.style.cssText = 'display:inline-block;width:100%;background:#fff;border-radius:6px;padding:2px 8px;min-height:36px;vertical-align:middle;';
                        panEl.innerHTML = `<iframe src="${data.details.card_number_url}" width="100%" height="32" frameborder="0" scrolling="no" style="border:none;display:block;vertical-align:middle;"></iframe>`;
                    }
                    if (cvvEl) {
                        cvvEl.style.cssText = 'display:inline-block;width:100%;background:#fff;border-radius:6px;padding:2px 8px;min-height:36px;vertical-align:middle;';
                        cvvEl.innerHTML = `<iframe src="${data.details.cvv_url}" width="100%" height="32" frameborder="0" scrolling="no" style="border:none;display:block;vertical-align:middle;"></iframe>`;
                    }
                    if (document.getElementById('dash-copy-pan-' + cardId)) document.getElementById('dash-copy-pan-' + cardId).style.display = 'none';
                    if (document.getElementById('dash-copy-cvv-' + cardId)) document.getElementById('dash-copy-cvv-' + cardId).style.display = 'none';
                } else {
                    if (data.details.card_number && document.getElementById('full-pan-text-' + cardId)) {
                        document.getElementById('full-pan-text-' + cardId).innerText = data.details.card_number;
                    }
                    if (data.details.cvv && document.getElementById('full-cvv-text-' + cardId)) {
                        document.getElementById('full-cvv-text-' + cardId).innerText = data.details.cvv;
                    }
                }

                if (data.details.expiry && document.getElementById('full-exp-text-' + cardId)) {
                    document.getElementById('full-exp-text-' + cardId).innerText = data.details.expiry;
                }

                if (document.getElementById('bal-' + cardId)) {
                    document.getElementById('bal-' + cardId).innerText = data.details.balance;
                }
                if (document.getElementById('addr-' + cardId)) {
                    document.getElementById('addr-' + cardId).innerText = data.details.address;
                }
                if (document.getElementById('city-' + cardId)) {
                    document.getElementById('city-' + cardId).innerText = data.details.city;
                }
                if (document.getElementById('state-' + cardId)) {
                    document.getElementById('state-' + cardId).innerText = data.details.state;
                }
                if (document.getElementById('zip-' + cardId)) {
                    document.getElementById('zip-' + cardId).innerText = data.details.zip;
                }
                if (document.getElementById('country-' + cardId)) {
                    document.getElementById('country-' + cardId).innerText = data.details.country;
                }
                
                detailsDiv.style.display = 'block';
            } else {
                Swal.fire({
                    title: "Notice",
                    text: data.message || "Could not fetch details. Card may still be processing.",
                    icon: "info",
                    background: "#000066",
                    color: "#fff",
                    confirmButtonColor: "#FFA600"
                });
            }
        })
        .catch(err => {
            btn.innerHTML = '<ion-icon name="eye-outline" style="font-size: 1rem; color: #FFA600;"></ion-icon> Reveal Details & Billing';
            btn.disabled = false;
            Swal.fire({
                title: "Error",
                text: "Network error occurred while fetching details.",
                icon: "error",
                background: "#000066",
                color: "#fff",
                confirmButtonColor: "#e3342f"
            });
        });
}

function terminateCard(cardId) {
    Swal.fire({
        title: 'Terminate Virtual Card?',
        text: 'This will close the card and instantly refund any remaining USD balance back to your NGN wallet.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#7a7a9a',
        confirmButtonText: 'Yes, Terminate & Refund'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/pay/cards/terminate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'card_id=' + encodeURIComponent(cardId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Terminated!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Could not terminate card.', 'error');
                }
            });
        }
    });
}

function copyCardInfo(elementId, label) {
    const text = document.getElementById(elementId).innerText.trim();
    if (!text || text.includes('•') || text.includes('*')) {
        Swal.fire({
            title: "Notice",
            text: "Please click 'Reveal Details' first to copy.",
            icon: "info",
            background: "#000066",
            color: "#fff",
            confirmButtonColor: "#FFA600"
        });
        return;
    }

    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            title: "Copied!",
            text: label + " copied to clipboard.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false,
            background: "#000066",
            color: "#fff"
        });
    });
}

function toggleCardFreeze(cardId) {
    Swal.fire({
        title: "Freeze / Unfreeze Card?",
        text: "Are you sure you want to change the active status of this card?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FFA600",
        cancelButtonColor: "#333",
        confirmButtonText: "Yes, Proceed",
        background: "#000066",
        color: "#fff"
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('card_id', cardId);

            fetch('/pay/cards/freeze', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Updated!",
                        text: data.message,
                        icon: "success",
                        background: "#000066",
                        color: "#fff",
                        confirmButtonColor: "#FFA600"
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.message || "Failed to update card status.",
                        icon: "error",
                        background: "#000066",
                        color: "#fff",
                        confirmButtonColor: "#e3342f"
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    title: "Error",
                    text: "Failed to connect to server.",
                    icon: "error",
                    background: "#000066",
                    color: "#fff",
                    confirmButtonColor: "#e3342f"
                });
            });
        }
    });
}
</script>

<!-- Add Wallet Modal -->
<div id="addWalletModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: #ffffff; width: 400px; border-radius: var(--pay-radius); padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative;">
        <button onclick="document.getElementById('addWalletModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #1a1a2e;">&times;</button>
        <h3 style="margin: 0 0 20px 0; color: #1a1a2e; font-weight: 700;">Add New Wallet</h3>
        <p style="color: #7a7a9a; font-size: 0.9rem; margin-bottom: 25px;">Instantly open a new currency wallet to receive and hold funds globally.</p>
        <form method="POST" action="/pay/wallet/create">
            <div class="pay-input-group">
                <label class="pay-label" style="color: #1a1a2e;">Select Currency</label>
                <select name="currency" class="pay-select" style="background: #f4f6f9; color: #1a1a2e; border: 1px solid rgba(0,0,0,0.1);" required>
                    <?php 
                        $existingCurrencies = array_column($wallets ?? [], 'currency');
                        $available = ['USD' => 'US Dollar', 'GBP' => 'British Pound', 'EUR' => 'Euro', 'GHS' => 'Ghana Cedis', 'KES' => 'Kenyan Shilling', 'ZAR' => 'South African Rand'];
                        foreach ($available as $code => $name):
                            if (!in_array($code, $existingCurrencies)):
                    ?>
                    <option value="<?= $code ?>"><?= $code ?> - <?= $name ?></option>
                    <?php 
                            endif;
                        endforeach; 
                    ?>
                </select>
            </div>
            <button type="submit" class="pay-btn-primary" style="margin-top: 25px;">Create Wallet</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
