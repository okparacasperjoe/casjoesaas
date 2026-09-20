<?php
$title = "SOP Documents";
require __DIR__ . '/../../layout/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>SOP Documents</h2>
    <a href="/erp/sops/create" class="btn btn-primary">Draft New SOP</a>
</div>

<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-success">Action completed successfully.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sops)): ?>
                        <?php foreach ($sops as $sop): ?>
                            <tr>
                                <td><?= htmlspecialchars($sop['id']) ?></td>
                                <td><?= htmlspecialchars($sop['title']) ?></td>
                                <td><?= date('M d, Y', strtotime($sop['created_at'])) ?></td>
                                <td>
                                    <a href="/erp/sops/tracking?id=<?= $sop['id'] ?>" class="btn btn-sm btn-info">Tracking</a>
                                    <a href="/erp/sops/assign?id=<?= $sop['id'] ?>" class="btn btn-sm btn-success">Assign</a>
                                    <a href="/erp/sops/edit?id=<?= $sop['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="/erp/sops/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this SOP?');">
                                        <input type="hidden" name="id" value="<?= $sop['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No SOPs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
