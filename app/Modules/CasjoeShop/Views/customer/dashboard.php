<?php 
$title = "My Account";
require __DIR__ . '/../layout/store_header.php';
?>

<div class="container mt-5 mb-5 align-items-center">
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">My Account</h5>
                    <div class="list-group list-group-flush">
                        <a href="/shop/account" class="list-group-item list-group-item-action active">Orders</a>
                        <!-- Future: Wishlist, Profile Settings -->
                        <a href="/shop/logout" class="list-group-item list-group-item-action text-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h2 class="fw-bold mb-4">Order History</h2>

            <?php if (empty($orders)): ?>
                <div class="alert alert-info py-5 text-center">
                    <i class="bi bi-cart-x display-4 mb-3 d-block"></i>
                    <p class="lead">You haven't placed any orders yet.</p>
                    <a href="/shop" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#<?= $order['id'] ?></td>
                                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    <td><?= $order['item_count'] ?> items</td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <?php 
                                            $statusBadge = match($order['status']) {
                                                'paid', 'completed' => 'success',
                                                'pending' => 'warning',
                                                'cancelled' => 'danger',
                                                'shipped' => 'info',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?= $statusBadge ?>"><?= ucfirst($order['status']) ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/shop/account/orders/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

