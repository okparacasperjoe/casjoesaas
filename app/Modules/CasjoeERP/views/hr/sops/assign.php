<?php
$title = "Assign SOP: " . htmlspecialchars($sop['title']);
require __DIR__ . '/../../layout/header.php';
?>
<div class="mb-4">
    <h2><?= $title ?></h2>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] == 'no_employees'): ?>
    <div class="alert alert-danger">No employees were selected or found in the selected department.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="/erp/sops/assign/store" method="POST">
            <input type="hidden" name="sop_id" value="<?= $sop['id'] ?>">
            
            <div class="mb-3">
                <label>Option 1: Assign to Entire Department</label>
                <select name="department_id" class="form-control">
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">If selected, this overrides individual employee selection.</small>
            </div>
            
            <hr>
            
            <div class="mb-3">
                <label>Option 2: Assign to Specific Employees</label>
                <select name="employee_ids[]" class="form-control" multiple size="10">
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?> (<?= htmlspecialchars($emp['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Hold Ctrl/Cmd to select multiple employees.</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Assign SOP</button>
            <a href="/erp/sops" class="btn btn-secondary">Back to SOPs</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
