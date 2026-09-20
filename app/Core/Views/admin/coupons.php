<?php
$pageTitle = 'Manage Coupons';
require __DIR__ . '/header.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
        .app-container { min-height: 100vh; display: flex; }
        
        /* Sidebar Link Reset */
        a { text-decoration: none; }
        
        .nav-menu { list-style: none; padding: 0; margin: 20px 0; }
        
        /* Explicit Modal Styling to ensure visibility */
        .modal-content { background-color: #ffffff !important; color: #333 !important; box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important; border: none; }
        .modal-header { border-bottom: 1px solid #eee; }
        .modal-footer { border-top: 1px solid #eee; }
        .form-control, .form-select { background-color: #fff !important; color: #333 !important; border: 1px solid #ced4da !important; }

        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
        }
    </style>
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <!-- Force Dark Text -->
                <h1 class="h3 mb-0" style="color: #ffffff !important; font-weight: 700; text-shadow: none !important;">🎟️ Billing Coupons</h1>
                <p class="" style="color: #94a3b8 !important; margin: 0;">Create discount codes for subscriptions.</p>
            </div>
            <div class="col-md-6 text-end">
                <!-- Force Button Visibility -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCouponModal" style="background-color: #000066 !important; border-color: #000066 !important; color: white !important; cursor: pointer;">
                    <ion-icon name="add-circle-outline" class="me-1"></ion-icon> Create Coupon
                </button>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Coupons Table -->
        <div class="card border-0 shadow-sm" style="background: white !important;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="color: #333 !important;">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Duration</th>
                            <th>Expires</th>
                            <th>Usage</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($coupons)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No active coupons found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fs-6"><?= htmlspecialchars($coupon['code']) ?></span>
                                </td>
                                <td>
                                    <?php if ($coupon['type'] === 'percent'): ?>
                                        <span class="badge bg-success"><?= $coupon['value'] ?>% OFF</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">₦<?= number_format($coupon['value']) ?> OFF</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $coupon['duration_months'] ?> Month(s)</td>
                                <td>
                                    <?php if ($coupon['expires_at']): ?>
                                        <?= date('M d, Y', strtotime($coupon['expires_at'])) ?>
                                        <?php if (strtotime($coupon['expires_at']) < time()): ?>
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= number_format($coupon['usage_count']) ?> times</td>
                                <td class="text-end">
                                    <form action="/casper-joe/coupons/delete" method="POST" onsubmit="return confirm('Delete this coupon?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $coupon['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<!-- Create Modal -->
<div class="modal fade" id="createCouponModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/casper-joe/coupons/store" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #000066; font-weight: bold;">Create New Coupon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Coupon Code</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. WELCOME50" required style="text-transform: uppercase;">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Type</label>
                        <select name="type" class="form-select">
                            <option value="percent">Percentage (%) Off</option>
                            <option value="fixed">Fixed Amount (₦) Off</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Value</label>
                        <input type="number" name="value" class="form-control" placeholder="e.g. 50 or 2000" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Duration (Months)</label>
                    <input type="number" name="duration_months" class="form-control" value="1" min="1" required>
                    <div class="form-text text-muted">How long the discount lasts.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Expires At (Optional)</label>
                    <input type="date" name="expires_at" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background-color: #000066;">Create Coupon</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS (Required for Modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Fallback: Manual Modal Trigger if data-bs-toggle fails
    document.addEventListener('DOMContentLoaded', function() {
        const createBtn = document.querySelector('[data-bs-target="#createCouponModal"]');
        const modalEl = document.getElementById('createCouponModal');
        
        if(createBtn && modalEl && typeof bootstrap !== 'undefined') {
            createBtn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        }
    });
</script>
<?php require __DIR__ . '/footer.php'; ?>
