<div class="text-center mb-4">
    <h3 class="fw-bold">What is your primary goal?</h3>
    <p class="text-muted">Select all that apply. We'll verify your setup based on this.</p>
</div>

<form action="/onboarding/step3" method="POST">
    <div class="row g-3 mb-4">
        
        <div class="col-md-6">
            <input type="checkbox" class="btn-check" id="intent_sell" name="intent[]" value="sell">
            <label class="btn btn-outline-secondary w-100 h-100 p-3 text-start d-flex align-items-center gap-3" for="intent_sell">
                <i class="bi bi-shop fs-2 text-primary"></i>
                <div>
                    <div class="fw-bold text-dark">Sell Products</div>
                    <div class="small">Physical or Digital goods</div>
                </div>
            </label>
        </div>

        <div class="col-md-6">
            <input type="checkbox" class="btn-check" id="intent_money" name="intent[]" value="money">
            <label class="btn btn-outline-secondary w-100 h-100 p-3 text-start d-flex align-items-center gap-3" for="intent_money">
                <i class="bi bi-wallet2 fs-2 text-success"></i>
                <div>
                    <div class="fw-bold text-dark">Get Paid</div>
                    <div class="small">Invoices & Payment Links</div>
                </div>
            </label>
        </div>

        <div class="col-md-6">
            <input type="checkbox" class="btn-check" id="intent_manage" name="intent[]" value="manage">
            <label class="btn btn-outline-secondary w-100 h-100 p-3 text-start d-flex align-items-center gap-3" for="intent_manage">
                <i class="bi bi-people fs-2 text-info"></i>
                <div>
                    <div class="fw-bold text-dark">Manage Business</div>
                    <div class="small">Staff, Expenses, CRM</div>
                </div>
            </label>
        </div>

        <div class="col-md-6">
            <input type="checkbox" class="btn-check" id="intent_grow" name="intent[]" value="grow">
            <label class="btn btn-outline-secondary w-100 h-100 p-3 text-start d-flex align-items-center gap-3" for="intent_grow">
                <i class="bi bi-graph-up-arrow fs-2 text-warning"></i>
                <div>
                    <div class="fw-bold text-dark">Grow Audience</div>
                    <div class="small">Marketing & Leads</div>
                </div>
            </label>
        </div>

    </div>

    <button type="submit" class="btn btn-primary btn-lg w-100">Continue <i class="bi bi-arrow-right"></i></button>
</form>
