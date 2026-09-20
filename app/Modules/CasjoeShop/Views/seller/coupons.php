<?php 
$title = "Coupons - Vendor Dashboard";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark"><i class="bi bi-ticket-perforated"></i> Coupons & Discounts</h1>
            <div class="text-muted">Create promo codes to boost sales.</div>
        </div>
        <div>
            <a href="/shop/vendor/dashboard" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- List Coupons -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Active Coupons</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Discount</th>
                                    <th>Usage</th>
                                    <th>Expiry</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($coupons)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No coupons created yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach($coupons as $c): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold">
                                            <span class="badge bg-light text-dark border border-dark border-dashed font-monospace"><?= htmlspecialchars($c['code']) ?></span>
                                        </td>
                                        <td>
                                            <?php if($c['type'] == 'percent'): ?>
                                                <?= number_format($c['value']) ?>% OFF
                                            <?php else: ?>
                                                ₦<?= number_format($c['value'], 2) ?> OFF
                                            <?php endif; ?>
                                            <div class="small text-muted">Min Spend: ₦<?= number_format($c['min_spend'], 2) ?></div>
                                        </td>
                                        <td>
                                            <?= $c['used_count'] ?> 
                                            <span class="text-muted">/ <?= $c['usage_limit'] > 0 ? $c['usage_limit'] : '∞' ?></span>
                                        </td>
                                        <td>
                                            <?php if($c['expires_at']): ?>
                                                <?= date('M d, Y', strtotime($c['expires_at'])) ?>
                                                <?php if(strtotime($c['expires_at']) < time()): ?>
                                                    <span class="badge bg-danger">Expired</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="/shop/vendor/coupons/delete/<?= $c['id'] ?>" method="POST" onsubmit="return confirm('Delete this coupon?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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

        <!-- Add Coupon -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Create Coupon</h5>
                </div>
                <div class="card-body">
                    <form action="/shop/vendor/coupons" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. WELCOME10" required>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="percent">Percent (%)</option>
                                    <option value="fixed">Fixed Amount ($)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Value</label>
                                <input type="number" name="value" class="form-control" step="0.01" min="0" placeholder="10" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum Spend ($)</label>
                            <input type="number" name="min_spend" class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Usage Limit (Total)</label>
                            <input type="number" name="usage_limit" class="form-control" min="0" placeholder="0 = Unlimited">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expires At</label>
                            <input type="date" name="expires_at" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Create Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

