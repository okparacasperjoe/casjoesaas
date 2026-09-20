<?php
$title = "My Documents & SOPs";
require __DIR__ . '/../../layout/header.php';
?>
<div class="mb-4">
    <h2><?= $title ?></h2>
    <p>Please review and sign any pending Standard Operating Procedures (SOPs) or documents.</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'signed'): ?>
    <div class="alert alert-success">Document signed successfully.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Document Title</th>
                        <th>Assigned On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignments)): ?>
                        <?php foreach ($assignments as $asg): ?>
                            <tr>
                                <td><?= htmlspecialchars($asg['title']) ?></td>
                                <td><?= date('M d, Y', strtotime($asg['sop_created_at'])) ?></td>
                                <td>
                                    <?php if ($asg['status'] == 'signed'): ?>
                                        <span class="badge bg-success">Signed on <?= date('M d, Y', strtotime($asg['signed_at'])) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending Signature</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($asg['status'] == 'signed'): ?>
                                        <a href="/erp/my-portal/sops/<?= $asg['id'] ?>/read" class="btn btn-sm btn-info">Review Document</a>
                                    <?php else: ?>
                                        <a href="/erp/my-portal/sops/<?= $asg['id'] ?>/read" class="btn btn-sm btn-primary">Read & Sign</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">You have no assigned SOPs.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
