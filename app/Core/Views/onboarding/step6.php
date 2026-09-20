<div class="text-center mb-4">
    <h3 class="fw-bold">Let's Get Started</h3>
    <p class="text-muted">Choose your first quick win.</p>
</div>

<div class="row g-3">
    <?php if(in_array('casjoe-shop', $active_modules)): ?>
    <div class="col-12">
        <a href="/onboarding/action/add-product" class="card text-decoration-none h-100 hover-shadow transition-all">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded p-3"><i class="bi bi-box-seam fs-3"></i></div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Add First Product</h5>
                    <p class="text-muted small mb-0">Start selling in minutes.</p>
                </div>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
    <?php endif; ?>

    <div class="col-12">
        <a href="/onboarding/action/create-invoice" class="card text-decoration-none h-100 hover-shadow transition-all">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded p-3"><i class="bi bi-receipt fs-3"></i></div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Create Invoice</h5>
                    <p class="text-muted small mb-0">Get paid for your work.</p>
                </div>
                 <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>

    <div class="col-12 mt-4 text-center">
        <a href="/onboarding/skip-action" class="text-muted text-decoration-none">Skip for now</a>
    </div>
</div>
