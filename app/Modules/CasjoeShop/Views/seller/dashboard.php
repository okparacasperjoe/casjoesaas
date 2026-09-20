<?php 
$title = "Vendor Dashboard";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark"><i class="bi bi-speedometer2"></i> Vendor Dashboard</h1>
            <div class="text-muted">Store: <strong><?= htmlspecialchars($vendor['store_name']) ?></strong></div>
        </div>
        <div>
            <a href="/shop/pos" class="btn btn-dark"><i class="bi bi-calculator"></i> POS Terminal</a>
            <a href="/shop/vendor/products/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
            <a href="/shop/vendor/products" class="btn btn-outline-secondary">My Products</a>
            <a href="/shop/vendor/settings" class="btn btn-outline-secondary"><i class="bi bi-gear"></i> Settings</a>
            <a href="/shop/vendor/shipping" class="btn btn-outline-secondary"><i class="bi bi-truck"></i> Shipping</a>
            <a href="/shop/vendor/coupons" class="btn btn-outline-secondary"><i class="bi bi-ticket-perforated"></i> Coupons</a>
        </div>
    </div>

    <!-- Alert for Pending -->
    <?php if($vendor['status'] == 'pending'): ?>
    <div class="alert alert-warning">
        <i class="bi bi-hourglass-split"></i> Your account is pending approval. Your products will not be visible in the store until approved by the admin.
    </div>
    <?php endif; ?>

    <!-- KPIs -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">My Products</h6>
                    <h2 class="display-6 fw-bold mb-0"><?= number_format($totalProducts) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold">Total Orders</h6>
                    <h2 class="display-6 fw-bold mb-0"><?= number_format($totalOrders) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold text-success">Total Revenue</h6>
                    <h2 class="display-6 fw-bold mb-0 text-success">₦<?= number_format($totalRevenue, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    </div>

    <!-- Sales Chart -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Sales Overview (Last 30 Days)</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($salesData['labels']) ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?= json_encode($salesData['values']) ?>,
                    backgroundColor: 'rgba(255, 166, 0, 0.2)',
                    borderColor: '#FFA600',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

    <!-- Recent Orders -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Recent Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recentOrders)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No orders yet. Start listing products!</td></tr>
                        <?php else: ?>
                            <?php foreach($recentOrders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                <td>₦<?= number_format($order['total_amount'], 2) ?></td>
                                <td><span class="badge bg-success"><?= ucfirst($order['status']) ?></span></td>
                                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
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

