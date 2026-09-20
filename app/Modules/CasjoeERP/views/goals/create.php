<?php
$active_app = 'finance';
$active_sub = 'goals';
$title = "Create New Goal";
require __DIR__ . '/../layout/header.php'; 
?>


    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white">Create Goal</h1>
            <p class="text-white-50">Set a new financial or operational target.</p>
        </div>
        <a href="/erp/goals" class="btn btn-outline-light">
            <i class="bi bi-arrow-left"></i> Back to Goals
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: rgba(30, 30, 30, 0.8);">
        <div class="card-body p-4">
            <form action="/erp/goals/store" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label text-white">Goal Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. Q4 Revenue Target">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-white">Target Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="target_value" class="form-control bg-dark text-white border-secondary" required placeholder="100000">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-white">Current / Starting Value</label>
                        <input type="number" step="0.01" name="current_value" class="form-control bg-dark text-white border-secondary" value="0">
                    </div>
                     
                    <div class="col-md-4">
                        <label class="form-label text-white">Unit</label>
                        <select name="unit" class="form-select bg-dark text-white border-secondary">
                            <option value="NGN">Currency (NGN)</option>
                            <option value="USD">Currency (USD)</option>
                            <option value="%">Percentage (%)</option>
                            <option value="Users">Count (Users)</option>
                            <option value="Leads">Count (Leads)</option>
                            <option value="Items">Count (Items)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-white">Deadline</label>
                        <input type="date" name="deadline" class="form-control bg-dark text-white border-secondary">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-light">Reset</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4" style="background-color: #FFA600; border: none; color: #000;">Create Goal</button>
                </div>
            </form>
        </div>
    </div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
