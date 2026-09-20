<?php
$title = "Tracking: " . htmlspecialchars($sop['title']);
require __DIR__ . '/../../layout/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= $title ?></h2>
    <div>
        <a href="/erp/sops/assign?id=<?= $sop['id'] ?>" class="btn btn-success">Assign More Employees</a>
        <a href="/erp/sops" class="btn btn-secondary">Back to SOPs</a>
    </div>
</div>

<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-success">SOP Assigned Successfully.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Signed At</th>
                        <th>Signature Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignments)): ?>
                        <?php foreach ($assignments as $asg): ?>
                            <tr>
                                <td><?= htmlspecialchars($asg['first_name'] . ' ' . $asg['last_name']) ?></td>
                                <td><?= htmlspecialchars($asg['email']) ?></td>
                                <td>
                                    <?php if ($asg['status'] == 'signed'): ?>
                                        <span class="badge bg-success">Signed</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $asg['signed_at'] ? date('M d, Y h:i A', strtotime($asg['signed_at'])) : '-' ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($asg['signed_name'] ?? '-') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No employees assigned to this SOP yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
