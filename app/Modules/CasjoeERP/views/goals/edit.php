<?php
$active_app = 'finance';
$active_sub = 'goals';
$title = "Edit Goal";
require __DIR__ . '/../layout/header.php'; 
?>


    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white">Edit Goal</h1>
            <p class="text-white-50">Update your target or progress.</p>
        </div>
        <a href="/erp/goals" class="btn btn-outline-light">
            <i class="bi bi-arrow-left"></i> Back to Goals
        </a>
    </div>

    <div class="card shadow-sm border-0" style="background: rgba(30,30,30,0.8);">
        <div class="card-body p-4">
            <form action="/erp/goals/update" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                <input type="hidden" name="id" value="<?= $goal['id'] ?>">
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label text-white">Goal Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control bg-dark text-white border-secondary" required value="<?= htmlspecialchars($goal['title']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-white">Target Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="target_value" class="form-control bg-dark text-white border-secondary" required value="<?= $goal['target_value'] ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-white">Current Value</label>
                        <input type="number" step="0.01" name="current_value" class="form-control bg-dark text-white border-secondary" value="<?= $goal['current_value'] ?>">
                    </div>
                     
                    <div class="col-md-4">
                        <label class="form-label text-white">Unit</label>
                        <select name="unit" class="form-select bg-dark text-white border-secondary">
                            <option value="NGN" <?= $goal['unit'] == 'NGN' ? 'selected' : '' ?>>Currency (NGN)</option>
                            <option value="USD" <?= $goal['unit'] == 'USD' ? 'selected' : '' ?>>Currency (USD)</option>
                            <option value="%" <?= $goal['unit'] == '%' ? 'selected' : '' ?>>Percentage (%)</option>
                            <option value="Users" <?= $goal['unit'] == 'Users' ? 'selected' : '' ?>>Count (Users)</option>
                            <option value="Leads" <?= $goal['unit'] == 'Leads' ? 'selected' : '' ?>>Count (Leads)</option>
                            <option value="Items" <?= $goal['unit'] == 'Items' ? 'selected' : '' ?>>Count (Items)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-white">Deadline</label>
                        <input type="date" name="deadline" class="form-control bg-dark text-white border-secondary" value="<?= $goal['deadline'] ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label text-white">Status</label>
                        <select name="status" class="form-select bg-dark text-white border-secondary">
                            <option value="active" <?= $goal['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="achieved" <?= $goal['status'] == 'achieved' ? 'selected' : '' ?>>Achieved</option>
                            <option value="failed" <?= $goal['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="archived" <?= $goal['status'] == 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning fw-bold px-4" style="background-color: #FFA600; border: none; color: #000;">Update Goal</button>
                    
                     <button type="submit" formaction="/erp/goals/delete" formmethod="POST" class="btn btn-outline-danger" onclick="return confirm('Are you sure? This cannot be undone.');">Delete</button>
                </div>
            </form>
        </div>
    </div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
