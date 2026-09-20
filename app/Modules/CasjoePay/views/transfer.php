<?php
$title = "Transfer | Casjoe Pay";
$pageTitle = "Transfer Money";
$activeMenu = "transfer";
require_once __DIR__ . '/partials/pay_header.php';
?>

<div class="pay-form-card" style="max-width: 500px; margin: 0 auto;">
    <h2 class="pay-form-header" style="text-align: center; margin-bottom: 24px;">Send Money</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <div style="background: rgba(255,77,106,0.1); border: 1px solid rgba(255,77,106,0.3); color: var(--pay-red); padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
            <ion-icon name="alert-circle" style="vertical-align: middle; margin-right: 6px;"></ion-icon>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    
    <div class="pay-tabs">
        <button type="button" class="pay-tab-btn active" onclick="switchTab('internal')" id="tab-btn-internal">Internal</button>
        <button type="button" class="pay-tab-btn" onclick="switchTab('bank')" id="tab-btn-bank">Bank</button>
        <button type="button" class="pay-tab-btn" onclick="switchTab('momo')" id="tab-btn-momo">Mobile Money</button>
    </div>

    <form method="POST" action="/pay/transfer">
        <input type="hidden" name="type" id="transfer_type" value="internal">

        <!-- Internal Transfer -->
        <div id="form-internal" class="transfer-section">
            <div class="pay-input-group">
                <label class="pay-label">Recipient Email (Casjoe User)</label>
                <input type="email" name="email" placeholder="user@casjoe.com" class="pay-input">
            </div>
        </div>

        <!-- Bank Transfer -->
        <div id="form-bank" class="transfer-section" style="display: none;">
            <div class="pay-input-group">
                <label class="pay-label">Bank Name</label>
                <select name="bank_code" id="bank_code" class="pay-select" onchange="resolveAccount()">\n                    <option value="">Loading Banks...</option>\n                </select>
            </div>
            <div class="pay-input-group">
                <label class="pay-label">Account Number</label>
                <input type="text" name="account_number" id="account_number" placeholder="1234567890" class="pay-input" maxlength="10" oninput="resolveAccount()">
            </div>
            
            <div id="account_name_display" style="display: none; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="person-circle-outline" style="font-size: 1.5rem; color: var(--pay-gold);"></ion-icon>
                <div style="flex: 1;">
                    <div style="font-size: 0.75rem; color: var(--pay-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Account Name</div>
                    <div id="account_name_text" style="font-weight: 600; color: white;">Waiting for account number...</div>
                </div>
            </div>
        </div>

        <!-- Mobile Money -->
        <div id="form-momo" class="transfer-section" style="display: none;">
             <div class="pay-input-group">
                <label class="pay-label">Provider</label>
                <select name="momo_provider" class="pay-select">
                    <option value="mtn">MTN MoMo</option>
                    <option value="airtel">Airtel Money</option>
                    <option value="mpesa">M-Pesa</option>
                </select>
            </div>
            <div class="pay-input-group">
                <label class="pay-label">Phone Number</label>
                <input type="tel" name="phone_number" placeholder="+234..." class="pay-input">
            </div>
        </div>

        <div class="pay-input-group">
            <label class="pay-label">Amount</label>
            <div class="pay-input-with-addon">
                <select name="currency" class="pay-addon-select">
                    <option value="NGN">NGN</option>
                    <option value="USD">USD</option>
                </select>
                <input type="number" name="amount" placeholder="0.00" class="pay-addon-input" min="10" required step="0.01">
            </div>
        </div>
        
        <div class="pay-input-group">
            <label class="pay-label">Transaction PIN</label>
            <input type="password" name="pin" placeholder="Enter 4-digit PIN" class="pay-input" maxlength="4" required pattern="[0-9]{4}">
        </div>
        
        <button type="submit" class="pay-btn-primary">
            <ion-icon name="paper-plane-outline" style="font-size: 1.2rem;"></ion-icon> Proceed with Transfer
        </button>
        <p style="text-align: center; color: var(--pay-text-muted); margin-top: 15px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; gap: 4px;">
            <ion-icon name="shield-checkmark-outline"></ion-icon> Secure 256-bit Encryption
        </p>
    </form>

    <script>
        function switchTab(type) {
            // Update hidden input
            document.getElementById('transfer_type').value = type;

            // Toggle Forms
            document.querySelectorAll('.transfer-section').forEach(el => el.style.display = 'none');
            document.getElementById('form-' + type).style.display = 'block';

            // Toggle Tabs
            document.querySelectorAll('.pay-tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-btn-' + type).classList.add('active');
        }
        let resolveTimeout;

        function resolveAccount() {
            const bankCode = document.getElementById('bank_code').value;
            const accountNum = document.getElementById('account_number').value;
            const type = document.getElementById('transfer_type').value;
            const nameDisplay = document.getElementById('account_name_display');
            const nameText = document.getElementById('account_name_text');

            if (type !== 'bank') return;

            if (accountNum.length === 10) {
                nameDisplay.style.display = 'flex';
                nameText.innerHTML = '<span style="color: var(--pay-gold);"><ion-icon name="sync-outline" class="spin"></ion-icon> Resolving account...</span>';
                
                clearTimeout(resolveTimeout);
                resolveTimeout = setTimeout(() => {
                    const formData = new FormData();
                    formData.append('bank_code', bankCode);
                    formData.append('account_number', accountNum);

                    fetch('/pay/transfer/resolve-account', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            nameText.innerHTML = '<span style="color: #4CAF50;"><ion-icon name="checkmark-circle-outline"></ion-icon> ' + data.account_name + '</span>';
                        } else {
                            nameText.innerHTML = '<span style="color: var(--pay-red);"><ion-icon name="close-circle-outline"></ion-icon> ' + (data.message || 'Unable to resolve') + '</span>';
                        }
                    })
                    .catch(err => {
                        nameText.innerHTML = '<span style="color: var(--pay-red);">Error resolving account</span>';
                    });
                }, 500); // 500ms debounce
            } else {
                nameDisplay.style.display = accountNum.length > 0 ? 'flex' : 'none';
                nameText.innerHTML = 'Enter 10-digit account number...';
            }
        }

        // Add a spin animation if it doesn't exist globally
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes spin { 100% { transform: rotate(360deg); } }
            .spin { animation: spin 1s linear infinite; }
        `;
        document.head.appendChild(style);
    </script>
</div>

<?php require_once __DIR__ . '/partials/pay_footer.php'; ?>


<script>
document.addEventListener('DOMContentLoaded', function() {
        fetch('/pay/transfer/banks', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(async res => {
            if (!res.ok) {
                let text = await res.text();
                throw new Error("HTTP " + res.status + ": " + text.substring(0, 100));
            }
            return res.json();
        })
        .then(res => {
            if (res.success && res.data) {
                let select = document.getElementById('bank_code');
                select.innerHTML = '<option value="">Select Bank...</option>';
                // Handle both direct array or Strowallet's {bank_list: [...]} object
                let bankArray = Array.isArray(res.data) ? res.data : (res.data.bank_list || []);
                
                // Sort banks alphabetically
                let sortedBanks = bankArray.sort((a, b) => a.bankName.localeCompare(b.bankName));
                sortedBanks.forEach(bank => {
                    select.innerHTML += `<option value="${bank.bankCode}">${bank.bankName}</option>`;
                });
            } else {
                document.getElementById('bank_code').innerHTML = '<option value="">Failed to load banks</option>';
            }
        })
        .catch(err => {
            console.error('Error loading banks:', err);
            document.getElementById('bank_code').innerHTML = '<option value="">Error: ' + err.message + '</option>';
        });
});
</script>
