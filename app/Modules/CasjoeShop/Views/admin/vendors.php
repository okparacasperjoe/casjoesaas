<?php 
$title = "Manage Vendors";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark">Manage Vendors</h1>
        <a href="/<?= ADMIN_PATH ?>/shop" class="btn btn-outline-secondary">&larr; Back to Dashboard</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Store Name</th>
                            <th>Slug</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendors as $v): ?>
                        <tr>
                            <td>#<?= $v['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($v['store_name']) ?></td>
                            <td class="text-muted small">/shop/vendor/<?= htmlspecialchars($v['slug']) ?></td>
                            <td><?= $v['commission_rate'] ?>%</td>
                            <td>
                                <?php if($v['status']=='approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif($v['status']=='pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= ucfirst($v['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($v['created_at'])) ?></td>
                            <td class="text-end">
                                <?php if($v['status']=='pending'): ?>
                                <form action="/<?= ADMIN_PATH ?>/shop/vendors/<?= $v['id'] ?>/approve" method="POST" class="d-inline">
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

