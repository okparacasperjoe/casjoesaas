<?php
$title = "Cards & Accounts | Casjoe Pay";
$pageTitle = "Cards & Accounts";
$defaultTab = $_GET['tab'] ?? 'cards';
$activeMenu = $defaultTab === 'accounts' ? 'virtual-bank' : 'cards';
require_once __DIR__ . '/partials/pay_header.php';

$defaultTab = $_GET['tab'] ?? 'cards';
if (!in_array($defaultTab, ['cards', 'accounts'])) {
    $defaultTab = 'cards';
}
?>

<div class="pay-tabs">
    <button type="button" class="pay-tab-btn <?= $defaultTab === 'cards' ? 'active' : '' ?>" onclick="switchSection('cards')" id="tab-btn-cards">Virtual Cards</button>
    <button type="button" class="pay-tab-btn <?= $defaultTab === 'accounts' ? 'active' : '' ?>" onclick="switchSection('accounts')" id="tab-btn-accounts">Virtual Accounts</button>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Success",
                text: <?= json_encode($_SESSION['success']) ?>,
                icon: "success",
                background: "var(--pay-surface)",
                color: "#fff",
                confirmButtonColor: "var(--pay-gold)"
            });
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error",
                text: <?= json_encode($_SESSION['error']) ?>,
                icon: "error",
                background: "var(--pay-surface)",
                color: "#fff",
                confirmButtonColor: "var(--pay-red)"
            });
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- SECTION 1: VIRTUAL CARDS -->
<div id="section-cards" class="pay-section-block" style="<?= $defaultTab === 'cards' ? '' : 'display: none;' ?>">
    <div style="margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 20px;">
        <?php if (empty($cards)): ?>
            <div style="background: var(--pay-surface); border: 1px dashed var(--pay-border); padding: 40px; border-radius: var(--pay-radius); text-align: center; width: 100%; color: var(--pay-text-muted);">
                <ion-icon name="card-outline" style="font-size: 3.5rem; opacity: 0.3; margin-bottom: 15px; color: var(--pay-gold);"></ion-icon>
                <p style="margin: 0; font-size: 0.95rem;">No virtual cards created yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($cards as $card): ?>
                <?php 
                    $isReloadable = !isset($card['is_reloadable']) || (int)$card['is_reloadable'] === 1;
                    $tierName = $isReloadable ? 'Reloadable' : 'Instant Lite';
                    $brandName = strtoupper($card['brand'] ?? $card['card_type'] ?? 'VISA');
                ?>
                <div class="virtual-card-container" style="position: relative; width: 380px; max-width: 100%; margin-bottom: 25px;">
                    <!-- CLEAN SIMPLE CARD FRONT -->
                    <div class="virtual-card" style="background: linear-gradient(135deg, #000066 0%, #000033 100%); border: 1.5px solid rgba(255,166,0,0.4); border-radius: 20px; padding: 22px 25px; color: #ffffff; width: 100%; min-height: 220px; position: relative; box-shadow: 0 15px 35px rgba(0,0,102,0.25); display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
                        
                        <!-- Row 1: Brand & Status -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-weight: 800; color: #ffffff; font-size: 1.25rem; letter-spacing: -0.5px;">casjoe</span><span style="font-weight: 800; color: #FFA600; font-size: 1.25rem; letter-spacing: 0.5px;">PAY</span>
                            </div>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <span style="border: 1px solid <?= $isReloadable ? 'rgba(0,214,143,0.4)' : 'rgba(255,166,0,0.4)' ?>; color: <?= $isReloadable ? '#00d68f' : '#FFA600' ?>; font-size: 0.6rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: <?= $isReloadable ? 'rgba(0,214,143,0.12)' : 'rgba(255,166,0,0.12)' ?>; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= $tierName ?>
                                </span>
                                <span style="border: 1px solid <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#e3342f' : '#FFA600' ?>; color: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#e3342f' : '#FFA600' ?>; font-size: 0.65rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'rgba(227,52,47,0.15)' : 'rgba(255,166,0,0.1)' ?>; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($card['status'] ?? 'ACTIVE') ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Row 2: Chip & Brand Logo -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 15px;">
                            <div style="width: 44px; height: 32px; background: linear-gradient(135deg, #ffd166 0%, #ff9900 50%, #d47a00 100%); border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3);"></div>
                            <div style="font-style: italic; font-weight: 900; font-size: 1.5rem; color: #fff; line-height: 1; letter-spacing: -1px; text-transform: uppercase;">
                                <?= htmlspecialchars($brandName) ?>
                            </div>
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

                    <!-- CARD BACK (CVV Strip) -->
                    <div style="margin-top: 10px; background: #000066; border: 1px solid rgba(255,166,0,0.3); border-radius: 12px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #FFA600, #FFB826, #FFA600);"></div>
                        <div style="display: flex; align-items: center; gap: 6px; z-index: 1;">
                            <span style="font-weight: 800; color: #FFA600; font-size: 0.95rem;">casjoe<span style="color:#fff">PAY</span></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; background: rgba(255,166,0,0.1); padding: 6px 14px; border-radius: 8px; border: 1px solid rgba(255,166,0,0.2); cursor: pointer; z-index: 1;" onclick="revealCardDetails('<?= $card['card_id'] ?>')" id="btn-cvv-<?= $card['card_id'] ?>">
                            <span style="font-size: 0.8rem; color: #FFA600; font-weight: 600;">CVV: <span id="cvv-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace;">***</span></span>
                            <ion-icon name="finger-print-outline" style="font-size: 1.2rem; color: #FFA600;"></ion-icon>
                        </div>
                    </div>

                    <!-- Feature Icons Row (With Working Freeze Action) -->
                    <div style="margin-top: 10px; display: flex; justify-content: space-between; background: rgba(0,0,102,0.8); border: 1px solid rgba(255,166,0,0.15); border-radius: 10px; padding: 10px 8px;">
                        <div style="text-align: center; flex: 1;">
                            <ion-icon name="wifi-outline" style="font-size: 1.1rem; color: #FFA600; transform: rotate(90deg);"></ion-icon>
                            <div style="font-size: 0.5rem; color: rgba(255,166,0,0.6); margin-top: 2px;">Contactless</div>
                        </div>
                        <div style="text-align: center; flex: 1;">
                            <ion-icon name="shield-checkmark-outline" style="font-size: 1.1rem; color: #FFA600;"></ion-icon>
                            <div style="font-size: 0.5rem; color: rgba(255,166,0,0.6); margin-top: 2px;">3D Secure</div>
                        </div>
                        <div style="text-align: center; flex: 1;">
                            <ion-icon name="globe-outline" style="font-size: 1.1rem; color: #FFA600;"></ion-icon>
                            <div style="font-size: 0.5rem; color: rgba(255,166,0,0.6); margin-top: 2px;">Global</div>
                        </div>
                        <div style="text-align: center; flex: 1;">
                            <ion-icon name="cash-outline" style="font-size: 1.1rem; color: #FFA600;"></ion-icon>
                            <div style="font-size: 0.5rem; color: rgba(255,166,0,0.6); margin-top: 2px;">Multi Currency</div>
                        </div>
                        <div style="text-align: center; flex: 1; cursor: pointer;" onclick="toggleCardFreeze('<?= $card['card_id'] ?>')">
                            <ion-icon name="snow-outline" style="font-size: 1.1rem; color: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#e3342f' : '#FFA600' ?>;"></ion-icon>
                            <div id="freeze-label-<?= $card['card_id'] ?>" style="font-size: 0.5rem; color: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#e3342f' : 'rgba(255,166,0,0.6)' ?>; margin-top: 2px; font-weight: 700;">
                                <?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'Unfreeze' : 'Freeze' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Redesigned Actions & Details Panel -->
                    <div style="margin-top: 12px; background: #02052e; border: 1px solid rgba(255,153,0,0.3); border-radius: 14px; padding: 18px; box-shadow: 0 8px 25px rgba(0,0,0,0.5);">
                        
                        <!-- Header Buttons: Reveal & Freeze -->
                        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                            <button type="button" onclick="revealCardDetails('<?= $card['card_id'] ?>')" id="btn-reveal-<?= $card['card_id'] ?>" style="flex: 2; padding: 10px 14px; background: linear-gradient(135deg, #ff9900 0%, #d47a00 100%); border: none; border-radius: 8px; color: #02052e; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 4px 12px rgba(255,153,0,0.25);">
                                <ion-icon name="eye-outline" style="font-size: 1.1rem;"></ion-icon> Reveal Details & Billing
                            </button>

                            <button type="button" onclick="toggleCardFreeze('<?= $card['card_id'] ?>')" id="btn-freeze-<?= $card['card_id'] ?>" style="flex: 1; padding: 10px 14px; background: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'rgba(0,214,143,0.15)' : 'rgba(227,52,47,0.15)' ?>; border: 1px solid <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#00d68f' : '#e3342f' ?>; border-radius: 8px; color: <?= strtolower($card['status'] ?? 'active') === 'frozen' ? '#00d68f' : '#e3342f' ?>; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <ion-icon name="snow-outline"></ion-icon>
                                <span id="btn-freeze-text-<?= $card['card_id'] ?>"><?= strtolower($card['status'] ?? 'active') === 'frozen' ? 'Unfreeze' : 'Freeze' ?></span>
                            </button>

                            <button type="button" onclick="terminateCard('<?= $card['card_id'] ?>')" style="flex: 1; padding: 10px 14px; background: rgba(227,52,47,0.15); border: 1px solid #e3342f; border-radius: 8px; color: #e3342f; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;">
                                <ion-icon name="trash-outline"></ion-icon>
                                <span>Terminate</span>
                            </button>
                        </div>

                        <!-- Reveal Details Box -->
                        <div id="details-<?= $card['card_id'] ?>" style="display: none; margin-top: 15px; border-top: 1px dashed rgba(255,153,0,0.2); padding-top: 15px;">
                            
                            <!-- Balance Display -->
                            <div style="background: rgba(255,153,0,0.06); border: 1px solid rgba(255,153,0,0.2); border-radius: 10px; padding: 14px; text-align: center; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                                <div style="text-align: left;">
                                    <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.6); display: block;">Card Balance</span>
                                    <span style="font-size: 1.6rem; font-weight: 800; color: #ff9900;">$<span id="bal-<?= $card['card_id'] ?>"><?= number_format((float)($card['balance'] ?? 0), 2) ?></span> USD</span>
                                </div>
                                <?php if ($isReloadable): ?>
                                    <button class="pay-btn-primary" style="padding: 8px 16px; font-size: 0.78rem; border-radius: 8px; background: #ff9900; color: #02052e; font-weight: 700; border: none; cursor: pointer;" onclick="window.location.href='/pay/cards/fund?id=<?= $card['card_id'] ?>'">
                                        <ion-icon name="add-circle-outline" style="vertical-align: middle; margin-right: 3px;"></ion-icon> Fund Card
                                    </button>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.15); display: inline-flex; align-items: center; gap: 4px;">
                                        <ion-icon name="lock-closed-outline"></ion-icon> Non-Reloadable
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Card Details Table with Copy Buttons -->
                            <h4 style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1.5px; color: #ff9900; margin-bottom: 12px; border-bottom: 1px solid rgba(255,153,0,0.15); padding-bottom: 5px;">
                                Card Information
                            </h4>

                            <div style="display: grid; gap: 10px; margin-bottom: 15px;">
                                <!-- Full PAN -->
                                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(255,153,0,0.1);">
                                    <div>
                                        <span style="font-size: 0.65rem; color: rgba(255,255,255,0.5); display: block;">Card Number</span>
                                        <span id="full-pan-text-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; color: #fff; font-size: 0.9rem; font-weight: 600;">•••• •••• •••• ••••</span>
                                    </div>
                                    <button type="button" id="copy-pan-<?= $card['card_id'] ?>" onclick="copyCardInfo('full-pan-text-<?= $card['card_id'] ?>', 'Card Number')" style="background: rgba(255,153,0,0.15); border: none; color: #ff9900; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">
                                        <ion-icon name="copy-outline"></ion-icon> Copy
                                    </button>
                                </div>

                                <!-- Expiry & CVV -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <div style="background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(255,153,0,0.1);">
                                        <span style="font-size: 0.65rem; color: rgba(255,255,255,0.5); display: block;">Expiry Date</span>
                                        <span id="full-exp-text-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; color: #fff; font-size: 0.9rem; font-weight: 600;">--/--</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(255,153,0,0.1);">
                                        <div>
                                            <span style="font-size: 0.65rem; color: rgba(255,255,255,0.5); display: block;">CVV</span>
                                            <span id="full-cvv-text-<?= $card['card_id'] ?>" style="font-family: 'JetBrains Mono', monospace; color: #ff9900; font-size: 0.9rem; font-weight: 700;">***</span>
                                        </div>
                                        <button type="button" id="copy-cvv-<?= $card['card_id'] ?>" onclick="copyCardInfo('full-cvv-text-<?= $card['card_id'] ?>', 'CVV')" style="background: rgba(255,153,0,0.15); border: none; color: #ff9900; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">
                                            <ion-icon name="copy-outline"></ion-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Address Grid -->
                            <h4 style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1.5px; color: #ff9900; margin-bottom: 10px; border-bottom: 1px solid rgba(255,153,0,0.15); padding-bottom: 5px;">
                                Billing Address
                            </h4>

                            <div style="background: rgba(255,255,255,0.03); padding: 10px 12px; border-radius: 6px; border: 1px solid rgba(255,153,0,0.1); font-size: 0.82rem; margin-bottom: 10px;">
                                <div style="margin-bottom: 6px;"><span style="color: rgba(255,255,255,0.5);">Address:</span> <strong style="color: #fff;" id="addr-<?= $card['card_id'] ?>">...</strong></div>
                                <div style="margin-bottom: 6px;"><span style="color: rgba(255,255,255,0.5);">City/State:</span> <strong style="color: #fff;"><span id="city-<?= $card['card_id'] ?>">...</span>, <span id="state-<?= $card['card_id'] ?>">...</span></strong></div>
                                <div style="margin-bottom: 6px;"><span style="color: rgba(255,255,255,0.5);">Zip Code:</span> <strong style="color: #fff;" id="zip-<?= $card['card_id'] ?>">23401</strong></div>
                                <div><span style="color: rgba(255,255,255,0.5);">Country:</span> <strong style="color: #fff;" id="country-<?= $card['card_id'] ?>">Nigeria</strong></div>
                            </div>
                        </div>

                        <?php if (strpos($card['card_id'] ?? '', 'card_mock_') !== false): ?>
                            <form method="POST" action="/pay/cards/delete-mock" style="margin-top: 12px;">
                                <button type="submit" class="pay-btn-secondary" style="width: 100%; background: rgba(227, 52, 47, 0.1); color: #e3342f; border: 1px solid rgba(227, 52, 47, 0.3);">
                                    Delete Mock Card
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Create Card Form -->
    <div class="pay-form-card" style="max-width: 600px; margin: 0;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <h3 class="pay-form-header" style="text-align: left; margin: 0; font-size: 1.3rem;">Issue Virtual Dollar Card</h3>
            <span style="font-size: 0.75rem; color: #FFA600; background: rgba(255,166,0,0.12); padding: 4px 10px; border-radius: 20px; font-weight: 700;">ZiiroPay Global USD</span>
        </div>
        <?php
        $dbCon = \App\Core\Database::getInstance()->getConnection();
        $stmtRate = $dbCon->query("SELECT setting_value FROM system_settings WHERE setting_key = 'usd_exchange_rate'");
        $rate = (float)($stmtRate->fetchColumn() ?: 1600);
        $stmtFee = $dbCon->query("SELECT setting_value FROM system_settings WHERE setting_key = 'virtual_card_creation_fee'");
        $creationFee = (float)($stmtFee->fetchColumn() ?: 4);
        $stmtMin = $dbCon->query("SELECT setting_value FROM system_settings WHERE setting_key = 'virtual_card_min_deposit'");
        $minDepositConfig = (float)($stmtMin->fetchColumn() ?: 3);

        $stmtGhs = $dbCon->query("SELECT setting_value FROM system_settings WHERE setting_key = 'ghs_exchange_rate'");
        $ghsRate = (float)($stmtGhs ? $stmtGhs->fetchColumn() : 0) ?: 15.0;

        $stmtCjp = $dbCon->query("SELECT setting_value FROM system_settings WHERE setting_key = 'cjp_exchange_rate'");
        $cjpRate = (float)($stmtCjp ? $stmtCjp->fetchColumn() : 0) ?: 10.0;

        $ratesConfig = [
            'USD' => 1.0,
            'NGN' => (float)$rate,
            'GHS' => $ghsRate,
            'GBP' => 0.78,
            'EUR' => 0.92,
            'KES' => 130.0,
            'ZAR' => 18.0,
            'CJC' => $cjpRate,
            'CJP' => $cjpRate
        ];

        $symbolsConfig = [
            'USD' => '$',
            'NGN' => '₦',
            'GHS' => '₵',
            'GBP' => '£',
            'EUR' => '€',
            'KES' => 'KSh',
            'ZAR' => 'R',
            'CJC' => 'CJC ',
            'CJP' => 'CJP '
        ];

        $currencyNames = [
            'NGN' => 'Nigerian Naira',
            'USD' => 'US Dollar',
            'GHS' => 'Ghana Cedi',
            'GBP' => 'British Pound',
            'EUR' => 'Euro',
            'CJC' => 'Casjoe Token (CJP)',
            'CJP' => 'Casjoe Token (CJP)',
            'KES' => 'Kenyan Shilling',
            'ZAR' => 'South African Rand'
        ];

        $userObj = \App\Core\Auth::user();
        if (!isset($userWallets) || empty($userWallets)) {
            $stmtW = $dbCon->prepare("SELECT currency, balance FROM cp_wallets WHERE user_id = ? ORDER BY CASE WHEN currency = 'NGN' THEN 1 WHEN currency = 'USD' THEN 2 ELSE 3 END, currency ASC");
            $stmtW->execute([$userObj['id'] ?? 0]);
            $userWallets = $stmtW->fetchAll(\PDO::FETCH_ASSOC);
        }
        $hasNgnWallet = false;
        foreach ($userWallets as $w) {
            if ($w['currency'] === 'NGN') {
                $hasNgnWallet = true;
                break;
            }
        }
        if (!$hasNgnWallet) {
            array_unshift($userWallets, ['currency' => 'NGN', 'balance' => 0.00]);
        }

        $stmtKycCheck = $dbCon->prepare("SELECT * FROM cp_naira_card_users WHERE user_id = ? OR tenant_id = ? LIMIT 1");
        $stmtKycCheck->execute([$userObj['id'] ?? 0, $userObj['tenant_id'] ?? 0]);
        $userKyc = $stmtKycCheck->fetch(\PDO::FETCH_ASSOC);
        $hasKyc = !empty($userKyc);
        $prefilledName = trim(($userObj['first_name'] ?? '') . ' ' . ($userObj['last_name'] ?? ''));
        if (empty($prefilledName)) {
            $prefilledName = $userObj['name'] ?? ($_SESSION['user_name'] ?? '');
        }
        $prefilledIdNumber = $userKyc['nin'] ?? '';
        ?>
        <form method="POST" action="/pay/cards/create" id="createCardForm">
            <!-- Tier Selection Cards -->
            <label class="pay-label" style="margin-bottom: 10px; display: block; font-weight: 700; color: #FFA600;">Select Card Tier</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                
                <!-- Tier 1: Instant Lite Card -->
                <div id="tier-card-lite" onclick="selectCardTier('lite')" style="cursor: pointer; border: 2px solid #FFA600; background: rgba(255,166,0,0.08); border-radius: 12px; padding: 16px; position: relative; transition: all 0.2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-weight: 800; font-size: 0.95rem; color: #fff;">Instant Lite</span>
                        <span style="font-size: 0.62rem; font-weight: 700; background: #FFA600; color: #000066; padding: 2px 7px; border-radius: 10px; text-transform: uppercase;">Instant</span>
                    </div>
                    <div style="font-size: 0.74rem; color: rgba(255,255,255,0.7); line-height: 1.4; margin-bottom: 10px;">
                        Zero KYC required. Instant issuance for one-off online purchases, trials, and payments.
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #FFA600; border-top: 1px dashed rgba(255,166,0,0.2); padding-top: 8px;">
                        <span>Non-Reloadable</span>
                        <span>Min $3.00</span>
                    </div>
                    <input type="radio" name="card_tier" value="lite" id="tier-input-lite" checked style="position: absolute; opacity: 0; pointer-events: none;">
                </div>

                <!-- Tier 2: Reloadable Business Card -->
                <div id="tier-card-reloadable" onclick="selectCardTier('reloadable')" style="cursor: pointer; border: 2px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.02); border-radius: 12px; padding: 16px; position: relative; transition: all 0.2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-weight: 800; font-size: 0.95rem; color: #fff;">Reloadable</span>
                        <span style="font-size: 0.62rem; font-weight: 700; background: rgba(0,214,143,0.2); color: #00d68f; border: 1px solid #00d68f; padding: 2px 7px; border-radius: 10px; text-transform: uppercase;">Business</span>
                    </div>
                    <div style="font-size: 0.74rem; color: rgba(255,255,255,0.7); line-height: 1.4; margin-bottom: 10px;">
                        Supports wallet top-ups anytime. Ideal for SaaS subscriptions & operational spend.
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #00d68f; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 8px;">
                        <span>Reloadable</span>
                        <span>Min $5.00</span>
                    </div>
                    <input type="radio" name="card_tier" value="reloadable" id="tier-input-reloadable" style="position: absolute; opacity: 0; pointer-events: none;">
                </div>
            </div>

            <!-- KYC Warning for Reloadable if not verified -->
            <?php if (!$hasKyc): ?>
                <div id="reloadable-kyc-alert" style="display: none; background: rgba(227,52,47,0.12); border: 1px solid #e3342f; border-radius: 10px; padding: 12px 14px; margin-bottom: 20px; font-size: 0.8rem; color: #ff8b8b;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <ion-icon name="warning-outline" style="font-size: 1.3rem; color: #e3342f; flex-shrink: 0;"></ion-icon>
                        <div>
                            <strong>KYC Verification Required:</strong> Reloadable cards require identity verification. 
                            <a href="/pay/naira-cards" style="color: #FFA600; text-decoration: underline; font-weight: 700;">Complete Business KYC here</a> or select <strong>Instant Lite Card</strong> for immediate zero-KYC issuance.
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div id="reloadable-kyc-alert" style="display: none; background: rgba(0,214,143,0.1); border: 1px solid #00d68f; border-radius: 10px; padding: 10px 14px; margin-bottom: 20px; font-size: 0.8rem; color: #00d68f;">
                    <ion-icon name="checkmark-circle-outline" style="vertical-align: middle; margin-right: 4px;"></ion-icon>
                    Your business identity is verified. You can issue up to 3 reloadable business cards.
                </div>
            <?php endif; ?>

            <!-- Brand Selection -->
            <div class="pay-input-group" style="margin-bottom: 18px;">
                <label class="pay-label" style="font-weight: 700; color: #FFA600; margin-bottom: 8px;">Card Brand</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div id="brand-opt-visa" onclick="selectBrand('VISA')" style="cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 12px; background: rgba(255,166,0,0.08); border: 1.5px solid #FFA600; border-radius: 10px; color: #fff; font-weight: 700; font-size: 0.9rem;">
                        <input type="radio" name="brand" value="VISA" checked style="margin: 0; accent-color: #FFA600;">
                        <span>VISA</span>
                    </div>
                    <div id="brand-opt-mastercard" onclick="selectBrand('MASTERCARD')" style="cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 12px; background: rgba(255,255,255,0.03); border: 1.5px solid rgba(255,255,255,0.15); border-radius: 10px; color: #fff; font-weight: 700; font-size: 0.9rem;">
                        <input type="radio" name="brand" value="MASTERCARD" style="margin: 0; accent-color: #FFA600;">
                        <span>MASTERCARD</span>
                    </div>
                </div>
            </div>

            <!-- Name on Card -->
            <div class="pay-input-group" style="margin-bottom: 18px;">
                <label class="pay-label" style="font-weight: 700; color: #FFA600;">Name on Card</label>
                <input type="text" name="name_on_card" id="inputNameOnCard" value="<?= htmlspecialchars($prefilledName) ?>" placeholder="e.g. John Doe" class="pay-input" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,166,0,0.3); padding: 12px 16px; border-radius: 10px; width: 100%; box-sizing: border-box; font-size: 0.92rem;" required>
            </div>

            <!-- ID Info for Lite Card -->
            <div id="lite-id-group" style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 18px;">
                <div class="pay-input-group" style="margin: 0;">
                    <label class="pay-label" style="font-weight: 700; color: #FFA600; font-size: 0.75rem;">ID Type</label>
                    <select name="id_type" class="pay-select" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,166,0,0.3); padding: 12px; border-radius: 10px; width: 100%; font-size: 0.85rem;">
                        <option value="nin" selected>NIN</option>
                        <option value="bvn">BVN</option>
                        <option value="passport">Passport</option>
                        <option value="voter_id">Voter ID</option>
                        <option value="drivers_license">Driver's License</option>
                    </select>
                </div>
                <div class="pay-input-group" style="margin: 0;">
                    <label class="pay-label" style="font-weight: 700; color: #FFA600; font-size: 0.75rem;">ID Number (Optional)</label>
                    <input type="text" name="id_number" value="<?= htmlspecialchars($prefilledIdNumber) ?>" placeholder="e.g. 12345678901" class="pay-input" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,166,0,0.3); padding: 12px; border-radius: 10px; width: 100%; box-sizing: border-box; font-size: 0.85rem;">
                </div>
            </div>

            <!-- Source Payment Wallet -->
            <div class="pay-input-group" style="margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label class="pay-label" style="font-weight: 700; color: #FFA600; margin: 0;">Pay With (Source Wallet)</label>
                    <span id="selectedWalletBalanceBadge" style="font-size: 0.78rem; font-weight: 700; color: #00d68f; font-family: 'JetBrains Mono', monospace;"></span>
                </div>
                <select name="payment_currency" id="selectPaymentCurrency" class="pay-select" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,166,0,0.3); padding: 12px 14px; border-radius: 10px; width: 100%; box-sizing: border-box; font-size: 0.92rem; font-weight: 600;" onchange="updateCardCostBreakdown(document.getElementById('cardAmountInput').value)">
                    <?php foreach ($userWallets as $w): 
                        $cCode = strtoupper($w['currency']);
                        $cName = $currencyNames[$cCode] ?? $cCode;
                        $cSym = $symbolsConfig[$cCode] ?? ($cCode . ' ');
                        $bal = (float)$w['balance'];
                    ?>
                        <option value="<?= htmlspecialchars($cCode) ?>" data-balance="<?= $bal ?>" data-symbol="<?= htmlspecialchars($cSym) ?>" <?= $cCode === 'NGN' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cCode) ?> - <?= htmlspecialchars($cName) ?> (Available: <?= htmlspecialchars($cSym) . number_format($bal, 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: rgba(255,255,255,0.45); font-size: 0.72rem; margin-top: 4px; display: block;">Choose which wallet balance to debit for this card issuance.</small>
            </div>

            <!-- Initial Funding Amount -->
            <div class="pay-input-group" style="margin-bottom: 18px;">
                <label class="pay-label" style="font-weight: 700; color: #FFA600;">Initial Funding Amount (USD)</label>
                <div class="pay-input-with-addon" style="position: relative;">
                    <span style="padding: 12px 16px; font-weight: 800; border-right: 1px solid rgba(255,166,0,0.3); background: rgba(255,166,0,0.1); display: flex; align-items: center; color: #FFA600; font-size: 1rem;">USD</span>
                    <input type="number" name="amount" id="cardAmountInput" min="<?= htmlspecialchars($minDepositConfig) ?>" step="0.01" max="5000" placeholder="Min $<?= number_format($minDepositConfig, 2) ?> USD" required class="pay-addon-input" style="flex: 1; padding: 12px 16px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,166,0,0.3); border-left: none; border-radius: 0 10px 10px 0; font-family: 'JetBrains Mono', monospace; font-size: 1.05rem; font-weight: 700;" oninput="updateCardCostBreakdown(this.value)">
                </div>
                <span id="minAmountHelper" style="font-size: 0.72rem; color: rgba(255,255,255,0.5); margin-top: 5px; display: block;">
                    Minimum initial deposit is <strong>$<?= number_format($minDepositConfig, 2) ?> USD</strong>.
                </span>
            </div>

            <!-- Cost Calculation Box -->
            <div style="background: rgba(255,166,0,0.06); border: 1px solid rgba(255,166,0,0.2); border-radius: 12px; padding: 16px; margin-bottom: 22px; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: rgba(255,255,255,0.6);">Initial Deposit:</span>
                    <strong style="color: #fff;" id="breakdownDeposit">$0.00 USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: rgba(255,255,255,0.6);">Issuance Fee:</span>
                    <strong style="color: #fff;" id="breakdownFee">$<?= number_format($creationFee, 2) ?> USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: rgba(255,255,255,0.6);">Total Card Cost (USD):</span>
                    <strong style="color: #FFA600;" id="breakdownTotalUsd">$0.00 USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="color: rgba(255,255,255,0.6);" id="breakdownRateLabel">Exchange Rate:</span>
                    <strong style="color: #fff;" id="breakdownRateVal">₦<?= number_format($rate, 2) ?>/USD</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed rgba(255,166,0,0.2); padding-top: 8px; margin-top: 8px;">
                    <span style="color: #FFA600; font-weight: 700;" id="breakdownDeductLabel">Total Deducted:</span>
                    <strong id="breakdownTotalDeduct" style="color: #FFA600; font-size: 1.05rem; font-family: 'JetBrains Mono', monospace;">₦0.00 NGN</strong>
                </div>
                <div id="breakdownBalanceAlert" style="margin-top: 10px; font-size: 0.78rem; display: none;"></div>
            </div>

            <button type="submit" id="btnSubmitCreateCard" class="pay-btn-primary" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #FFA600 0%, #ff8c00 100%); border: none; border-radius: 12px; color: #000066; font-weight: 800; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 25px rgba(255,166,0,0.3);">
                <ion-icon name="card-outline" style="font-size: 1.3rem;"></ion-icon>
                <span id="btnSubmitCardText">Issue Instant Lite Card Now</span>
            </button>
        </form>
    </div>
</div>

<!-- SECTION 2: VIRTUAL BANK ACCOUNTS -->
<div id="section-accounts" class="pay-section-block" style="<?= $defaultTab === 'accounts' ? '' : 'display: none;' ?>">
    <div style="margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 20px;">
        <?php if (empty($accounts)): ?>
            <div style="background: var(--pay-surface); border: 1px dashed var(--pay-border); padding: 40px; border-radius: var(--pay-radius); text-align: center; width: 100%; color: var(--pay-text-muted);">
                <ion-icon name="business-outline" style="font-size: 3.5rem; opacity: 0.3; margin-bottom: 15px; color: var(--pay-gold);"></ion-icon>
                <p style="margin: 0; font-size: 0.95rem;">No virtual bank accounts generated yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($accounts as $account): ?>
                <div class="virtual-account" style="background: linear-gradient(135deg, var(--pay-surface-2) 0%, var(--pay-surface) 100%); border: 1px solid var(--pay-border); border-radius: var(--pay-radius); padding: 25px; color: white; width: 330px; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-top: 4px solid var(--pay-gold);">
                    <div class="account-bank" style="font-size: 1.15rem; color: var(--pay-gold); margin-bottom: 12px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="business" style="font-size: 1.3rem;"></ion-icon>
                        <?= htmlspecialchars($account['bank_name'] ?? 'Bank') ?>
                    </div>
                    <div class="account-number" style="font-size: 1.6rem; letter-spacing: 1px; margin-bottom: 6px; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #fff;">
                        <?= htmlspecialchars($account['account_number'] ?? 'N/A') ?>
                    </div>
                    <div class="account-name" style="font-size: 0.85rem; color: var(--pay-text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?= htmlspecialchars($account['account_name'] ?? 'Casjoe User') ?>
                    </div>
                    <div class="account-meta" style="display: flex; justify-content: space-between; font-size: 0.78rem; color: var(--pay-text-muted); margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.03); padding-top: 12px;">
                        <span>CURRENCY: <strong><?= htmlspecialchars($account['currency'] ?? 'NGN') ?></strong></span>
                        <span style="text-transform: uppercase; color: var(--pay-green); font-weight: bold;"><?= htmlspecialchars($account['status'] ?? 'Active') ?></span>
                    </div>
                    <?php if (strpos($account['customer_id'] ?? '', 'cust_mock_') !== false || ($account['bank_name'] ?? '') === 'Strowallet Bank'): ?>
                        <div style="margin-top: 15px; text-align: right;">
                            <form method="POST" action="/pay/virtual-bank/delete-mock" style="display:inline;">
                                <button type="submit" class="pay-btn-secondary" style="padding: 6px 12px; font-size: 0.75rem; background: #e3342f; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                                    Delete Mock Account
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Create Account Section -->
    <?php if (empty($accounts)): ?>
        <div class="pay-form-card" style="max-width: 500px; margin: 0;">
            <h3 class="pay-form-header" style="text-align: left; margin-bottom: 20px;">Generate Bank Account</h3>
            <p style="font-size: 0.85rem; color: var(--pay-text-muted); margin-bottom: 25px; line-height: 1.5;">
                Generate a dedicated virtual bank account number to easily receive funds into your wallet via bank transfer. Any funds received will be automatically credited to your NGN wallet balance.
            </p>
            <button type="button" id="create-account-btn" class="pay-btn-primary">
                <ion-icon name="rocket-outline" style="font-size: 1.2rem;"></ion-icon> Generate Account
            </button>
            <div id="create-status" style="margin-top: 15px; font-size: 0.9rem; text-align: center; font-weight: 600;"></div>
        </div>
        
        <script>
        document.getElementById('create-account-btn').addEventListener('click', function() {
            const btn = this;
            const status = document.getElementById('create-status');
            
            btn.disabled = true;
            btn.innerText = 'Generating account...';
            status.innerText = '';
            
            fetch('/pay/virtual-bank/create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    status.style.color = 'var(--pay-green)';
                    status.innerText = 'Success! Reloading...';
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    status.style.color = 'var(--pay-red)';
                    status.innerText = data.message || 'Failed to create account.';
                    btn.disabled = false;
                    btn.innerText = 'Generate Account';
                }
            })
            .catch(err => {
                status.style.color = 'var(--pay-red)';
                status.innerText = 'An error occurred. Please try again.';
                btn.disabled = false;
                btn.innerText = 'Generate Account';
            });
        });
        </script>
    <?php endif; ?>
</div>

<script>
    function switchSection(sec) {
        // Toggle Sections
        document.querySelectorAll('.pay-section-block').forEach(el => el.style.display = 'none');
        document.getElementById('section-' + sec).style.display = 'block';

        // Toggle Tabs
        document.querySelectorAll('.pay-tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-btn-' + sec).classList.add('active');
    }

    function revealCardDetails(cardId) {
        const btn = document.getElementById('btn-reveal-' + cardId);
        const detailsDiv = document.getElementById('details-' + cardId);
        
        if (detailsDiv.style.display === 'block') {
            detailsDiv.style.display = 'none';
            btn.innerHTML = '<ion-icon name="eye-outline"></ion-icon> Reveal Details & Billing';
            return;
        }

        btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Fetching Details...';
        btn.disabled = true;

        fetch('/pay/cards/details?card_id=' + encodeURIComponent(cardId))
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = '<ion-icon name="eye-off-outline"></ion-icon> Hide Card Details';
                btn.disabled = false;

                if (data.success) {
                    // Display masked PAN
                    const displayPan = data.details.masked_pan
                        ? data.details.masked_pan.replace(/(.{4})/g, '$1 ').trim()
                        : null;
                    if (displayPan && document.getElementById('pan-' + cardId)) {
                        document.getElementById('pan-' + cardId).innerText = displayPan;
                    }
                    // Expiry
                    if (data.details.expiry && document.getElementById('exp-' + cardId)) {
                        document.getElementById('exp-' + cardId).innerText = data.details.expiry;
                    }
                    // CVV (masked)
                    if (data.details.cvv && document.getElementById('cvv-' + cardId)) {
                        document.getElementById('cvv-' + cardId).innerText = data.details.cvv;
                    }

                    // Card Number: use plain text if available, else server-built iframe
                    const panEl = document.getElementById('full-pan-text-' + cardId);
                    if (panEl) {
                        if (data.details.card_number) {
                            panEl.innerText = data.details.card_number;
                        } else if (data.details.card_number_html) {
                            panEl.innerHTML = data.details.card_number_html;
                        } else {
                            panEl.innerText = 'Not available';
                        }
                    }

                    // CVV: use plain text if available, else server-built iframe
                    const cvvFullEl = document.getElementById('full-cvv-text-' + cardId);
                    if (cvvFullEl) {
                        if (data.details.cvv) {
                            cvvFullEl.innerText = data.details.cvv;
                        } else if (data.details.cvv_html) {
                            cvvFullEl.innerHTML = data.details.cvv_html;
                        } else {
                            cvvFullEl.innerText = '***';
                        }
                    }

                    // Hide copy buttons (can't copy from iframes)
                    if (document.getElementById('copy-pan-' + cardId)) document.getElementById('copy-pan-' + cardId).style.display = 'none';
                    if (document.getElementById('copy-cvv-' + cardId)) document.getElementById('copy-cvv-' + cardId).style.display = 'none';
                    
                    if (data.details.expiry && document.getElementById('full-exp-text-' + cardId)) {
                        document.getElementById('full-exp-text-' + cardId).innerText = data.details.expiry;
                    }

                    // Update billing details
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
                        background: "#02052e",
                        color: "#fff",
                        confirmButtonColor: "#ff9900"
                    });
                }
            })
            .catch(err => {
                btn.innerHTML = '<ion-icon name="eye-outline"></ion-icon> Reveal Details & Billing';
                btn.disabled = false;
                Swal.fire({
                    title: "Error",
                    text: "Network error occurred while fetching details.",
                    icon: "error",
                    background: "#02052e",
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
                background: "#02052e",
                color: "#fff",
                confirmButtonColor: "#ff9900"
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
                background: "#02052e",
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
            confirmButtonColor: "#ff9900",
            cancelButtonColor: "#333",
            confirmButtonText: "Yes, Proceed",
            background: "#02052e",
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
                            background: "#02052e",
                            color: "#fff",
                            confirmButtonColor: "#ff9900"
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: data.message || "Failed to update card status.",
                            icon: "error",
                            background: "#02052e",
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
                        background: "#02052e",
                        color: "#fff",
                        confirmButtonColor: "#e3342f"
                    });
                });
            }
        });
    }

    const CURRENCY_RATES = <?= json_encode($ratesConfig) ?>;
    const CURRENCY_SYMBOLS = <?= json_encode($symbolsConfig) ?>;
    const EXCHANGE_RATE = <?= json_encode((float)$rate) ?>;
    const CREATION_FEE = <?= json_encode((float)$creationFee) ?>;
    const MIN_DEPOSIT = <?= json_encode((float)$minDepositConfig) ?>;
    const USER_HAS_KYC = <?= json_encode((bool)$hasKyc) ?>;

    function selectCardTier(tier) {
        const liteCard = document.getElementById('tier-card-lite');
        const reloadableCard = document.getElementById('tier-card-reloadable');
        const liteRadio = document.getElementById('tier-input-lite');
        const reloadableRadio = document.getElementById('tier-input-reloadable');
        const kycAlert = document.getElementById('reloadable-kyc-alert');
        const liteIdGroup = document.getElementById('lite-id-group');
        const amountInput = document.getElementById('cardAmountInput');
        const minHelper = document.getElementById('minAmountHelper');
        const submitBtn = document.getElementById('btnSubmitCreateCard');
        const submitText = document.getElementById('btnSubmitCardText');

        if (!liteCard || !reloadableCard) return;

        if (tier === 'lite') {
            liteRadio.checked = true;
            liteCard.style.borderColor = '#FFA600';
            liteCard.style.background = 'rgba(255,166,0,0.08)';
            reloadableCard.style.borderColor = 'rgba(255,255,255,0.15)';
            reloadableCard.style.background = 'rgba(255,255,255,0.02)';
            
            if (kycAlert) kycAlert.style.display = 'none';
            if (liteIdGroup) liteIdGroup.style.display = 'grid';
            
            amountInput.min = MIN_DEPOSIT.toFixed(2);
            amountInput.placeholder = 'Min $' + MIN_DEPOSIT.toFixed(2) + ' USD';
            minHelper.innerHTML = 'Minimum initial deposit for Lite Card is <strong>$' + MIN_DEPOSIT.toFixed(2) + ' USD</strong>.';
            submitText.innerText = 'Issue Instant Lite Card Now';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        } else {
            reloadableRadio.checked = true;
            reloadableCard.style.borderColor = '#00d68f';
            reloadableCard.style.background = 'rgba(0,214,143,0.08)';
            liteCard.style.borderColor = 'rgba(255,255,255,0.15)';
            liteCard.style.background = 'rgba(255,255,255,0.02)';
            
            if (kycAlert) kycAlert.style.display = 'block';
            if (liteIdGroup) liteIdGroup.style.display = 'none';
            
            amountInput.min = MIN_DEPOSIT.toFixed(2);
            amountInput.placeholder = 'Min $' + MIN_DEPOSIT.toFixed(2) + ' USD';
            minHelper.innerHTML = 'Minimum initial deposit for Reloadable Card is <strong>$' + MIN_DEPOSIT.toFixed(2) + ' USD</strong>.';
            submitText.innerText = 'Issue Reloadable Business Card';

            if (!USER_HAS_KYC) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitText.innerText = 'KYC Required for Reloadable Card';
            } else {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
        }
        updateCardCostBreakdown(amountInput.value);
    }

    function selectBrand(brand) {
        const visaOpt = document.getElementById('brand-opt-visa');
        const mcOpt = document.getElementById('brand-opt-mastercard');
        const visaRadio = document.querySelector('input[name="brand"][value="VISA"]');
        const mcRadio = document.querySelector('input[name="brand"][value="MASTERCARD"]');

        if (brand === 'VISA') {
            if (visaRadio) visaRadio.checked = true;
            if (visaOpt) {
                visaOpt.style.borderColor = '#FFA600';
                visaOpt.style.background = 'rgba(255,166,0,0.08)';
            }
            if (mcOpt) {
                mcOpt.style.borderColor = 'rgba(255,255,255,0.15)';
                mcOpt.style.background = 'rgba(255,255,255,0.03)';
            }
        } else {
            if (mcRadio) mcRadio.checked = true;
            if (mcOpt) {
                mcOpt.style.borderColor = '#FFA600';
                mcOpt.style.background = 'rgba(255,166,0,0.08)';
            }
            if (visaOpt) {
                visaOpt.style.borderColor = 'rgba(255,255,255,0.15)';
                visaOpt.style.background = 'rgba(255,255,255,0.03)';
            }
        }
    }

    function updateCardCostBreakdown(val) {
        const deposit = parseFloat(val) || 0;
        const totalUsd = deposit > 0 ? (deposit + CREATION_FEE) : 0;
        
        const selWallet = document.getElementById('selectPaymentCurrency');
        const curr = selWallet ? selWallet.value.toUpperCase() : 'NGN';
        const rate = (typeof CURRENCY_RATES[curr] !== 'undefined') ? parseFloat(CURRENCY_RATES[curr]) : 1.0;
        const sym = (typeof CURRENCY_SYMBOLS[curr] !== 'undefined') ? CURRENCY_SYMBOLS[curr] : (curr + ' ');
        
        const totalDeduct = totalUsd * rate;

        const depEl = document.getElementById('breakdownDeposit');
        const feeEl = document.getElementById('breakdownFee');
        const totUsdEl = document.getElementById('breakdownTotalUsd');
        const rateLabelEl = document.getElementById('breakdownRateLabel');
        const rateValEl = document.getElementById('breakdownRateVal');
        const deductLabelEl = document.getElementById('breakdownDeductLabel');
        const deductEl = document.getElementById('breakdownTotalDeduct');
        const badgeEl = document.getElementById('selectedWalletBalanceBadge');
        const alertEl = document.getElementById('breakdownBalanceAlert');

        if (depEl) depEl.innerText = '$' + deposit.toFixed(2) + ' USD';
        if (feeEl) feeEl.innerText = '$' + CREATION_FEE.toFixed(2) + ' USD';
        if (totUsdEl) totUsdEl.innerText = '$' + totalUsd.toFixed(2) + ' USD';

        // Selected wallet available balance
        let availBal = 0;
        if (selWallet && selWallet.selectedOptions && selWallet.selectedOptions[0]) {
            availBal = parseFloat(selWallet.selectedOptions[0].getAttribute('data-balance')) || 0;
        }
        if (badgeEl) {
            badgeEl.innerText = 'Available: ' + sym + availBal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        if (rateLabelEl && rateValEl) {
            if (curr === 'USD') {
                rateLabelEl.innerText = 'Conversion:';
                rateValEl.innerText = '1:1 (Direct USD Debit)';
            } else {
                rateLabelEl.innerText = curr + '/USD Rate:';
                rateValEl.innerText = sym + rate.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' / $1 USD';
            }
        }

        if (deductLabelEl) {
            deductLabelEl.innerText = 'Total ' + curr + ' Deducted:';
        }

        if (deductEl) {
            deductEl.innerText = sym + totalDeduct.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + curr;
        }

        if (alertEl) {
            if (deposit > 0 && availBal < totalDeduct) {
                alertEl.style.display = 'block';
                alertEl.style.color = '#ff6b6b';
                alertEl.style.background = 'rgba(227,52,47,0.12)';
                alertEl.style.padding = '8px 12px';
                alertEl.style.borderRadius = '6px';
                alertEl.style.border = '1px solid rgba(227,52,47,0.3)';
                alertEl.innerHTML = '<ion-icon name="alert-circle-outline" style="vertical-align:middle;"></ion-icon> Insufficient ' + curr + ' wallet balance (' + sym + availBal.toFixed(2) + '). Required: ' + sym + totalDeduct.toFixed(2) + ' ' + curr + '.';
            } else if (deposit > 0) {
                alertEl.style.display = 'block';
                alertEl.style.color = '#00d68f';
                alertEl.style.background = 'rgba(0,214,143,0.08)';
                alertEl.style.padding = '6px 12px';
                alertEl.style.borderRadius = '6px';
                alertEl.style.border = '1px solid rgba(0,214,143,0.2)';
                alertEl.innerHTML = '<ion-icon name="checkmark-circle-outline" style="vertical-align:middle;"></ion-icon> Sufficient ' + curr + ' wallet funds available.';
            } else {
                alertEl.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const amtInput = document.getElementById('cardAmountInput');
        if (amtInput) {
            updateCardCostBreakdown(amtInput.value);
        }
    });
</script>
<style>
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>
