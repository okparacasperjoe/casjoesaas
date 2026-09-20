<?php 
$title = "Shipping Settings - Vendor Dashboard";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark"><i class="bi bi-truck"></i> Shipping Zones</h1>
            <div class="text-muted">Manage where you deliver and how much it costs.</div>
        </div>
        <div>
            <a href="/shop/vendor/dashboard" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <div class="row">
        <!-- List Zones -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Active Shipping Zones</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Zone Name</th>
                                <th>Cost</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($zones)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">No shipping zones defined. You are offering global/free shipping or haven't set up yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($zones as $z): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= htmlspecialchars($z['zone_name']) ?></td>
                                    <td>₦<?= number_format($z['cost'], 2) ?></td>
                                    <td class="text-end pe-4">
                                        <form action="/shop/vendor/shipping/delete/<?= $z['id'] ?>" method="POST" onsubmit="return confirm('Are you sure?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
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

        <!-- Add Zone -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Add New Zone</h5>
                </div>
                <div class="card-body">
                    <form action="/shop/vendor/shipping" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Zone Name</label>
                            <input type="text" name="zone_name" class="form-control" placeholder="e.g. Lagos, Nationwide, International" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shipping Cost (₦)</label>
                            <input type="number" name="cost" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Zone</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

