<?php 
$title = "My Wishlist";
require __DIR__ . '/../layout/store_header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm">
                <a href="/shop/account" class="list-group-item list-group-item-action">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="/shop/account/orders" class="list-group-item list-group-item-action">
                    <i class="bi bi-box-seam me-2"></i> My Orders
                </a>
                 <a href="/shop/account/wishlist" class="list-group-item list-group-item-action active">
                    <i class="bi bi-heart me-2"></i> Wishlist
                </a>
                <a href="/shop/logout" class="list-group-item list-group-item-action text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-heart-fill text-danger"></i> My Wishlist</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($items)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-heart fs-1 text-muted mb-3 d-block"></i>
                            <p class="text-muted">Your wishlist is empty.</p>
                            <a href="/shop" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach($items as $p): ?>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <?php if($p['image_path']): ?>
                                        <img src="<?= htmlspecialchars($p['image_path']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light card-img-top d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bi bi-box fs-1 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title text-truncate"><?= htmlspecialchars($p['name']) ?></h5>
                                        <p class="card-text fw-bold text-primary">$<?= number_format($p['price'], 2) ?></p>
                                        
                                        <div class="d-grid gap-2">
                                            <form action="/shop/cart/add" method="POST">
                                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                                            </form>
                                            <a href="/shop/wishlist/remove/<?= $p['wishlist_id'] ?>" class="btn btn-sm btn-link text-danger text-decoration-none">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

