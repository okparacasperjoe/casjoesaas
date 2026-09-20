<?php 
$title = "Shop Admin";
require __DIR__ . '/../../../../Core/Views/global_header.php'; // Correct path to global header
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark"><i class="bi bi-shop"></i> Casjoe Mart Admin</h1>
        <div>
            <a href="/<?= ADMIN_PATH ?>/shop/vendors" class="btn btn-outline-primary">Manage Vendors</a>
            <a href="/shop" target="_blank" class="btn btn-primary"><i class="bi bi-box-arrow-up-right"></i> View Storefront</a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Total Vendors</h6>
                    <h2 class="display-6 fw-bold mb-0"><?= number_format($totalVendors) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Total Products</h6>
                    <h2 class="display-6 fw-bold mb-0"><?= number_format($totalProducts) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Total Orders</h6>
                    <h2 class="display-6 fw-bold mb-0"><?= number_format($totalOrders) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Total Revenue</h6>
                    <h2 class="display-6 fw-bold mb-0 text-success">$<?= number_format($revenue, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Vendors -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Pending Vendor Approvals</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Store Name</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingVendors)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">No pending approvals.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pendingVendors as $vendor): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($vendor['store_name']) ?></td>
                                <td><?= date('M j, Y', strtotime($vendor['created_at'])) ?></td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                                <td class="text-end">
                                    <form action="/<?= ADMIN_PATH ?>/shop/vendors/<?= $vendor['id'] ?>/approve" method="POST" class="d-inline">
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

