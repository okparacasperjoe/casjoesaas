<div class="text-center mb-4">
    <i class="bi bi-shield-lock-fill text-success fs-1"></i>
    <h3 class="fw-bold mt-3">Activate Casjoe Pay</h3>
    <p class="text-muted">Your secure financial backbone for all transactions.</p>
</div>

<div class="card bg-light border-0 p-3 mb-4 rounded-3 shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-white p-2 rounded shadow-sm d-flex align-items-center justify-content-center">
            <img src="/assets/casjoe_logo.webp" style="height: 30px;">
        </div>
        <div>
            <div class="fw-bold text-dark">Casjoe Wallet</div>
            <div class="small text-muted">Auto-created for <?= htmlspecialchars($tenant['name'] ?? 'Your Business') ?></div>
        </div>
        <div class="ms-auto">
            <span class="badge bg-success px-3 py-2">Ready</span>
        </div>
    </div>
</div>

<form action="/onboarding/step4" method="POST" id="step4Form">
    <div class="mb-4">
        <label class="form-label fw-bold d-flex justify-content-between align-items-center">
            <span>Set Payout Account</span>
            <span class="badge bg-light text-muted border fw-normal">Optional for now</span>
        </label>
        
        <!-- Bank Account Option -->
        <div class="form-check p-3 border rounded mb-2 bg-white shadow-sm" style="cursor: pointer; transition: 0.2s;" onclick="selectPayoutMethod('bank')">
            <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="payout_method" id="bank" value="bank" checked>
            <label class="form-check-label d-block w-100" for="bank" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="text-dark"><i class="bi bi-bank text-primary me-2"></i> Bank Account</strong>
                    <span class="badge bg-primary bg-opacity-10 text-primary small">Local Withdrawal</span>
                </div>
                <div class="small text-muted mt-1">Withdraw directly to your local bank account</div>
            </label>
            
            <!-- Bank Input Fields (Shown when Bank is ticked) -->
            <div id="bankDetailsBox" class="mt-3 pt-3 border-top">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="e.g., Zenith Bank, GTBank, First Bank, Capitec...">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Account Number</label>
                        <input type="text" name="account_number" class="form-control form-control-sm" placeholder="10-digit Account No / IBAN">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Account Holder Name</label>
                        <input type="text" name="account_name" class="form-control form-control-sm" placeholder="Exact Account Holder Name" value="<?= htmlspecialchars(\App\Core\Auth::user()['name'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Crypto Wallet Option (USDT/USDC Only) -->
        <div class="form-check p-3 border rounded bg-white shadow-sm" style="cursor: pointer; transition: 0.2s;" onclick="selectPayoutMethod('crypto')">
            <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="payout_method" id="crypto" value="crypto">
            <label class="form-check-label d-block w-100" for="crypto" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="text-dark"><i class="bi bi-currency-bitcoin text-warning me-2"></i> Crypto Wallet</strong>
                    <span class="badge bg-warning bg-opacity-10 text-dark small border border-warning">Stablecoins Only</span>
                </div>
                <div class="small text-muted mt-1">Withdraw to USDT or USDC stablecoins right to your crypto wallet</div>
            </label>
            
            <!-- Crypto Input Fields (Shown when Crypto is ticked) -->
            <div id="cryptoDetailsBox" class="mt-3 pt-3 border-top d-none">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Select Stablecoin</label>
                        <select name="crypto_coin" class="form-select form-select-sm">
                            <option value="USDT">USDT (Tether USD)</option>
                            <option value="USDC">USDC (USD Coin)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-dark mb-1">Network / Chain</label>
                        <select name="crypto_network" class="form-select form-select-sm">
                            <option value="TRC20">TRC-20 (Tron — Lowest Fees)</option>
                            <option value="BEP20">BEP-20 (BNB Smart Chain)</option>
                            <option value="ERC20">ERC-20 (Ethereum)</option>
                            <option value="SOL">Solana Network</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Wallet Address</label>
                    <input type="text" name="crypto_address" class="form-control form-control-sm" placeholder="Paste your exact USDT / USDC receiving wallet address...">
                </div>
                <div class="small text-warning d-flex align-items-center mt-2" style="font-size: 0.75rem;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Ensure the network matches your wallet exactly to avoid permanent fund loss.
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm rounded-pill" style="background: linear-gradient(135deg, #000066 0%, #1a1a80 100%); border: none;">
        Save & Continue <i class="bi bi-arrow-right ms-2"></i>
    </button>
</form>

<script>
function selectPayoutMethod(method) {
    const bankRadio = document.getElementById('bank');
    const cryptoRadio = document.getElementById('crypto');
    const bankBox = document.getElementById('bankDetailsBox');
    const cryptoBox = document.getElementById('cryptoDetailsBox');

    if (method === 'bank') {
        bankRadio.checked = true;
        bankBox.classList.remove('d-none');
        cryptoBox.classList.add('d-none');
    } else if (method === 'crypto') {
        cryptoRadio.checked = true;
        cryptoBox.classList.remove('d-none');
        bankBox.classList.add('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="payout_method"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            selectPayoutMethod(this.value);
        });
    });
});
</script>
