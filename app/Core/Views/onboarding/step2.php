<div class="text-center mb-4">
    <h3 class="fw-bold">Business Identity & Setup</h3>
    <p class="text-muted">Set up your company details, logo, and local currency to personalize your experience.</p>
</div>

<form action="/onboarding/step2" method="POST" enctype="multipart/form-data">
    <!-- Business Logo Upload -->
    <div class="mb-4 text-start">
        <label class="form-label fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-image text-primary"></i> Upload Business Logo <span class="badge bg-light text-muted fw-normal">Optional</span>
        </label>
        <input type="file" name="logo" class="form-control form-control-lg" accept="image/png, image/jpeg, image/jpg, image/webp">
        <div class="form-text">Recommended: PNG or JPG image with transparent background (Max 2MB).</div>
    </div>

    <!-- Company Name -->
    <div class="mb-4 text-start">
        <label class="form-label fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-building text-primary"></i> Company / Business Name
        </label>
        <input type="text" name="business_name" class="form-control form-control-lg" placeholder="e.g. Casjoetech Ltd" value="<?= htmlspecialchars($tenant['name'] ?? '') ?>" required>
    </div>

    <!-- Country & Linked Currency -->
    <div class="mb-4 text-start">
        <label class="form-label fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-globe text-primary"></i> Country of Operation
        </label>
        <select name="country" id="countrySelect" class="form-select form-select-lg" required>
            <option value="" disabled selected>Select your country...</option>
            <optgroup label="Top African Economies">
                <option value="Nigeria" data-currency="NGN" data-symbol="₦">🇳🇬 Nigeria — Naira (₦ NGN)</option>
                <option value="Ghana" data-currency="GHS" data-symbol="GH₵">🇬🇭 Ghana — Cedi (GH₵ GHS)</option>
                <option value="South Africa" data-currency="ZAR" data-symbol="R">🇿🇦 South Africa — Rand (R ZAR)</option>
                <option value="Senegal" data-currency="XOF" data-symbol="CFA">🇸🇳 Senegal — CFA Franc (CFA XOF)</option>
                <option value="Zimbabwe" data-currency="USD" data-symbol="$">🇿🇼 Zimbabwe — US Dollar ($ USD)</option>
                <option value="Kenya" data-currency="KES" data-symbol="KSh">🇰🇪 Kenya — Shilling (KSh KES)</option>
                <option value="Rwanda" data-currency="RWF" data-symbol="FRw">🇷🇼 Rwanda — Franc (FRw RWF)</option>
                <option value="Uganda" data-currency="UGX" data-symbol="USh">🇺🇬 Uganda — Shilling (USh UGX)</option>
                <option value="Tanzania" data-currency="TZS" data-symbol="TSh">🇹🇿 Tanzania — Shilling (TSh TZS)</option>
                <option value="Cameroon" data-currency="XAF" data-symbol="CFA">🇨🇲 Cameroon — CFA Franc (CFA XAF)</option>
                <option value="Ivory Coast" data-currency="XOF" data-symbol="CFA">🇨🇮 Ivory Coast — CFA Franc (CFA XOF)</option>
                <option value="Egypt" data-currency="EGP" data-symbol="E£">🇪🇬 Egypt — Pound (E£ EGP)</option>
                <option value="Morocco" data-currency="MAD" data-symbol="DH">🇲🇦 Morocco — Dirham (DH MAD)</option>
            </optgroup>
            <optgroup label="International / Global">
                <option value="United States" data-currency="USD" data-symbol="$">🇺🇸 United States — Dollar ($ USD)</option>
                <option value="United Kingdom" data-currency="GBP" data-symbol="£">🇬🇧 United Kingdom — Pound (£ GBP)</option>
                <option value="European Union" data-currency="EUR" data-symbol="€">🇪🇺 European Union — Euro (€ EUR)</option>
            </optgroup>
        </select>
        <input type="hidden" name="currency" id="currencyInput" value="NGN">
        
        <div id="currencyBadgeContainer" class="mt-2 d-none">
            <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between border">
                <span class="text-muted small">Assigned Business Currency:</span>
                <span class="badge bg-primary fs-6 px-3 py-2" id="currencyBadgeText">₦ NGN</span>
            </div>
        </div>
    </div>

    <!-- Business Type -->
    <div class="mb-4 text-start">
        <label class="form-label fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-briefcase text-primary"></i> Business Type
        </label>
        <select name="business_type" class="form-select form-select-lg" required>
            <option value="" disabled selected>Select Business Scale...</option>
            <option value="solo">Solo / Freelancer</option>
            <option value="sme" selected>Small Business (1-10 employees)</option>
            <option value="company">Company (10+ employees)</option>
            <option value="ngo">Non-Profit / NGO</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold mt-2 shadow-sm">
        Continue Setup <i class="bi bi-arrow-right ms-2"></i>
    </button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('countrySelect');
    const currencyInput = document.getElementById('currencyInput');
    const badgeContainer = document.getElementById('currencyBadgeContainer');
    const badgeText = document.getElementById('currencyBadgeText');

    countrySelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option && option.dataset.currency) {
            const curr = option.dataset.currency;
            const sym = option.dataset.symbol || curr;
            currencyInput.value = curr;
            badgeText.textContent = `${sym} (${curr})`;
            badgeContainer.classList.remove('d-none');
        } else {
            badgeContainer.classList.add('d-none');
        }
    });

    // If pre-selected
    if (countrySelect.value) {
        countrySelect.dispatchEvent(new Event('change'));
    }
});
</script>
