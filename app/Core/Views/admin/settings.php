<?php
$pageTitle = 'Platform Settings';
require __DIR__ . '/header.php';
?>
<style>
        .settings-container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: transparent;
            padding: 0; 
            display: flex;
            flex-direction: column;
            min-height: 500px;
        }
        
        /* Tabs Container Header */
        .tabs-header-container {
            background: var(--glass-bg);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .tabs-nav {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0 20px;
            overflow-x: auto;
        }
        
        .tab-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding: 20px 25px;
            cursor: pointer;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            white-space: nowrap;
        }
        
        .tab-btn:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .tab-btn::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary);
            border-radius: 4px 4px 0 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tab-btn.active {
            color: white;
            font-weight: 600;
        }
        
        .tab-btn.active::after {
            opacity: 1;
        }
        
        .tab-btn ion-icon {
            font-size: 1.3rem;
        }
        
        /* Tab Content Area */
        .tab-content-area {
            background: var(--glass-bg);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 30px;
            position: relative;
            flex-grow: 1;
        }
        
        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Form Styles */
        .form-section h3 { 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            margin-top: 0;
            color: white; 
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.1);
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .btn-save-container {
            margin-top: 30px;
            text-align: right;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-save:hover { 
            filter: brightness(1.1); 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4); 
        }
        
        @media (max-width: 768px) {
            .tabs-nav { padding: 0 10px; }
            .tab-btn { padding: 15px 20px; }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
        <div class="top-bar" style="margin-bottom: 20px;">
            <h2><ion-icon name="options-outline" style="vertical-align: middle;"></ion-icon> Platform Settings</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;"><ion-icon name="checkmark-circle"></ion-icon> Settings updated successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;"><ion-icon name="checkmark-circle"></ion-icon> <?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <form action="/<?= ADMIN_PATH ?>/settings/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
            <input type="hidden" name="tab" id="active_tab_input" value="<?= htmlspecialchars($_GET['tab'] ?? 'offline') ?>">
            
            <div class="settings-container">
                <!-- Tabs Container -->
                <div class="tabs-header-container">
                    <div class="tabs-nav">
                        <button type="button" class="tab-btn active" onclick="openTab('payment', this)">
                            <ion-icon name="card-outline"></ion-icon> Payment Gateways
                        </button>
                        <button type="button" class="tab-btn" onclick="openTab('virtual-cards', this)">
                            <ion-icon name="wallet-outline"></ion-icon> Virtual Cards
                        </button>
                        <button type="button" class="tab-btn" onclick="openTab('offline', this)">
                            <ion-icon name="cash-outline"></ion-icon> Offline Deposit
                        </button>
                        <button type="button" class="tab-btn" onclick="openTab('general', this)">
                            <ion-icon name="settings-outline"></ion-icon> General
                        </button>
                    </div>
                </div>
                
                <!-- Tabs Content -->
                <div class="tab-content-area">
                    
                    <!-- Tab: Payment Gateways -->
                    <div id="payment" class="tab-pane active">
                        <div class="form-section">
                            <h3>Monnify Settings</h3>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>API Key</label>
                                    <input type="text" name="settings[monnify_api_key]" class="form-control" value="<?= htmlspecialchars($settings['monnify_api_key'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Secret Key</label>
                                    <input type="password" name="settings[monnify_secret_key]" class="form-control" value="<?= htmlspecialchars($settings['monnify_secret_key'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Contract Code</label>
                                    <input type="text" name="settings[monnify_contract_code]" class="form-control" value="<?= htmlspecialchars($settings['monnify_contract_code'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Mode</label>
                                    <select name="settings[monnify_mode]" class="form-control" style="background-color: #1a1a2e;">
                                        <option value="test" <?= ($settings['monnify_mode'] ?? 'test') == 'test' ? 'selected' : '' ?>>Test</option>
                                        <option value="live" <?= ($settings['monnify_mode'] ?? 'test') == 'live' ? 'selected' : '' ?>>Live</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section mt-4">
                            <h3>Flutterwave Settings</h3>
                            <div class="form-group">
                                <label>Public Key</label>
                                <input type="text" name="settings[flutterwave_public_key]" class="form-control" value="<?= htmlspecialchars($settings['flutterwave_public_key'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Secret Key</label>
                                <input type="password" name="settings[flutterwave_secret_key]" class="form-control" value="<?= htmlspecialchars($settings['flutterwave_secret_key'] ?? '') ?>">
                                <small style="color: rgba(255,255,255,0.4); margin-top: 5px; display: block;">Leave unchanged unless updating.</small>
                            </div>
                        </div>
                        
                        <div class="form-section mt-4">
                            <h3>Paystack Settings</h3>
                            <div class="form-group">
                                <label>Public Key</label>
                                <input type="text" name="settings[paystack_public_key]" class="form-control" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Secret Key</label>
                                <input type="password" name="settings[paystack_secret_key]" class="form-control" value="<?= htmlspecialchars($settings['paystack_secret_key'] ?? '') ?>">
                                <small style="color: rgba(255,255,255,0.4); margin-top: 5px; display: block;">Leave unchanged unless updating.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Virtual Cards -->
                    <div id="virtual-cards" class="tab-pane">
                        <div class="form-section">
                            <h3>Provider Configurations</h3>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Active Card Provider</label>
                                    <select name="settings[virtual_card_provider]" class="form-control" style="background-color: #1a1a2e;">
                                        <option value="ziiropay" <?= in_array($settings['virtual_card_provider'] ?? 'ziiropay', ['ziiropay', 'ziirocard', ''], true) ? 'selected' : '' ?>>ZiiroPay (USD Virtual Dollar Cards)</option>
                                        <option value="strowallet" <?= ($settings['virtual_card_provider'] ?? '') == 'strowallet' ? 'selected' : '' ?>>StroWallet</option>
                                        <option value="sudo" <?= ($settings['virtual_card_provider'] ?? '') == 'sudo' ? 'selected' : '' ?>>Sudo Africa</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Active Payout/Transfer Provider</label>
                                    <select name="settings[payout_provider]" class="form-control" style="background-color: #1a1a2e;">
                                        <option value="flutterwave" <?= ($settings['payout_provider'] ?? 'flutterwave') == 'flutterwave' ? 'selected' : '' ?>>Flutterwave</option>
                                        <option value="strowallet" <?= ($settings['payout_provider'] ?? '') == 'strowallet' ? 'selected' : '' ?>>StroWallet</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section mt-4">
                            <h3>Fee & Minimum Funding Configuration</h3>
                            <div class="grid-3">
                                <div class="form-group">
                                    <label>USD Exchange Rate (NGN/USD)</label>
                                    <input type="number" step="0.01" name="settings[usd_exchange_rate]" class="form-control" placeholder="e.g. 1600.00" value="<?= htmlspecialchars($settings['usd_exchange_rate'] ?? '1600') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Card Creation Fee (USD)</label>
                                    <input type="number" step="0.01" name="settings[virtual_card_creation_fee]" class="form-control" placeholder="e.g. 4.00" value="<?= htmlspecialchars($settings['virtual_card_creation_fee'] ?? '4') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Minimum Initial Card Funding / Deposit (USD)</label>
                                    <input type="number" step="0.01" name="settings[virtual_card_min_deposit]" class="form-control" placeholder="e.g. 5.00" value="<?= htmlspecialchars($settings['virtual_card_min_deposit'] ?? '5') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="grid-2 mt-4">
                            <div class="form-section">
                                <h3>Sudo Africa</h3>
                                <div class="form-group">
                                    <label>API Key</label>
                                    <input type="text" name="settings[sudo_api_key]" class="form-control" value="<?= htmlspecialchars($settings['sudo_api_key'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>API Secret</label>
                                    <input type="password" name="settings[sudo_api_secret]" class="form-control" value="<?= htmlspecialchars($settings['sudo_api_secret'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h3>StroWallet (Virtual Accounts & Naira Cards)</h3>
                                <div class="form-group">
                                    <label>Public Key (starts with pub_...)</label>
                                    <input type="text" name="settings[strowallet_public_key]" class="form-control" value="<?= htmlspecialchars($settings['strowallet_public_key'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Secret Key (starts with sec_...)</label>
                                    <input type="password" name="settings[strowallet_secret_key]" class="form-control" value="<?= htmlspecialchars($settings['strowallet_secret_key'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h3>ZiiroPay (USD Virtual Dollar Cards)</h3>
                                <div class="form-group">
                                    <label>Public Key</label>
                                    <input type="text" name="settings[ziiropay_public_key]" class="form-control" value="<?= htmlspecialchars($settings['ziiropay_public_key'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Secret Key</label>
                                    <input type="password" name="settings[ziiropay_secret_key]" class="form-control" value="<?= htmlspecialchars($settings['ziiropay_secret_key'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Offline Deposit -->
                    <div id="offline" class="tab-pane">
                        <div class="form-section">
                            <h3>Offline Bank Transfer Details</h3>
                            <p style="color: rgba(255,255,255,0.6); margin-bottom: 20px; font-size: 0.9rem;">
                                These details will be shown to users when they select "Offline Deposit" or "Bank Transfer" as their payment method.
                            </p>
                            
                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" name="settings[offline_deposit_bank]" class="form-control" placeholder="e.g. Chase Bank, Guaranty Trust Bank" value="<?= htmlspecialchars($settings['offline_deposit_bank'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Account Name</label>
                                <input type="text" name="settings[offline_deposit_account_name]" class="form-control" placeholder="e.g. Casjoe Global Solutions" value="<?= htmlspecialchars($settings['offline_deposit_account_name'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" name="settings[offline_deposit_account_number]" class="form-control" placeholder="e.g. 1234567890" value="<?= htmlspecialchars($settings['offline_deposit_account_number'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Tab: General -->
                    <div id="general" class="tab-pane">
                        <div class="form-section">
                            <h3>General Options</h3>
                            <div class="form-group">
                                <label>Payment Routing Mode</label>
                                <select name="settings[payment_routing_mode]" class="form-control" style="background-color: #1a1a2e;">
                                    <option value="direct" <?= ($settings['payment_routing_mode'] ?? '') == 'direct' ? 'selected' : '' ?>>Direct to Gateway</option>
                                    <option value="wallet" <?= ($settings['payment_routing_mode'] ?? '') == 'wallet' ? 'selected' : '' ?>>Wallet Balance Priority</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Minimum Funding Amount</label>
                                <input type="number" step="0.01" name="settings[min_funding_amount]" class="form-control" placeholder="e.g. 1000.00" value="<?= htmlspecialchars($settings['min_funding_amount'] ?? '1000') ?>">
                                <small style="color: rgba(255,255,255,0.4); margin-top: 5px; display: block;">The minimum amount a user can deposit to their wallet.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="btn-save-container">
                        <button type="submit" class="btn-save"><ion-icon name="checkmark-circle-outline"></ion-icon> Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
<script>
    function openTab(tabId, btnElement) {
        // Hide all tabs
        const panes = document.querySelectorAll('.tab-pane');
        panes.forEach(pane => pane.classList.remove('active'));
        
        // Remove active class from buttons
        const btns = document.querySelectorAll('.tab-btn');
        btns.forEach(btn => btn.classList.remove('active'));
        
        // Show selected tab
        const target = document.getElementById(tabId);
        if (target) target.classList.add('active');
        
        // Set active class on clicked button
        if (btnElement) btnElement.classList.add('active');

        const tabInput = document.getElementById('active_tab_input');
        if (tabInput) tabInput.value = tabId;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            const pane = document.getElementById(tab);
            if (pane) {
                const btns = document.querySelectorAll('.tab-btn');
                btns.forEach(b => {
                    if (b.getAttribute('onclick') && b.getAttribute('onclick').includes("'" + tab + "'")) {
                        openTab(tab, b);
                    }
                });
            }
        }
    });
</script>
<?php require __DIR__ . '/footer.php'; ?>
