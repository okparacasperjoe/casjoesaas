<?php 
$title = "Order #{$order['id']} - Details";
require __DIR__ . '/../layout/store_header.php';
?>

<div class="container mt-5 mb-5 align-items-center">
    <!-- Back Link -->
    <a href="/shop/account" class="text-decoration-none text-muted mb-4 d-inline-block">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Order Items</h5>
                    <span class="badge bg-secondary"><?= count($items) ?> Items</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach($items as $item): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center">
                                <!-- Image -->
                                <div style="width: 60px; height: 60px; background: #f8f9fa; border-radius: 8px; overflow: hidden; flex-shrink: 0;" class="me-3 d-flex align-items-center justify-content-center">
                                    <?php if($item['image_path']): ?>
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-box text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Details -->
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                                    <div class="text-muted small">
                                        $<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?>
                                    </div>
                                    
                                    <!-- Digital Download Link -->
                                    <?php if ($item['type'] === 'digital' && $order['status'] === 'paid' && $item['token']): ?>
                                        <div class="mt-2">
                                            <a href="/shop/download/<?= $item['token'] ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-download"></i> Download File
                                            </a>
                                            <span class="text-muted small ms-2">
                                                (<?= $item['download_count'] ?>/<?= $item['max_downloads'] ?> downloads used)
                                            </span>
                                        </div>
                                    <?php elseif ($item['type'] === 'digital' && $order['status'] !== 'paid'): ?>
                                        <span class="badge bg-warning text-dark mt-2">Payment Pending</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Total -->
                                <div class="text-end fw-bold">
                                    $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order ID:</span>
                        <span class="fw-bold">#<?= $order['id'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Date:</span>
                        <span><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Payment:</span>
                        <?php 
                            $statusBadge = match($order['status']) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                        ?>
                        <span class="badge bg-<?= $statusBadge ?>"><?= ucfirst($order['status']) ?></span>
                    </div>
                    
                    <hr>

                    <?php if (isset($order['service_fee']) && $order['service_fee'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span>$<?= number_format($order['total_amount'] - $order['service_fee'], 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Fee:</span>
                            <span>$<?= number_format($order['service_fee'], 2) ?></span>
                        </div>
                        <hr>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between">
                        <span class="h5 fw-bold">Total:</span>
                        <span class="h5 fw-bold text-success">$<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>
            
            <?php if($order['status'] === 'pending'): ?>
                <a href="#" class="btn btn-success w-100 mt-3 btn-lg">Pay Now</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

