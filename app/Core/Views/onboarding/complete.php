<div class="text-center mt-5">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success display-1"></i>
    </div>
    <h2 class="fw-bold mb-3">You are Live!</h2>
    <p class="text-muted lead mb-5">Your business system is set up and ready.</p>
    
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="card border-0 bg-light p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Active Modules:</span>
                    <span class="fw-bold text-primary"><?= implode(', ', $active_names) ?></span>
                </div>
            </div>
        </div>
    </div>

    <a href="/dashboard" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow">Go to Dashboard</a>
</div>
