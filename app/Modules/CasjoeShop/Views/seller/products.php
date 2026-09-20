<?php 
$title = "My Products";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark">My Products</h1>
        <div>
            <a href="/shop/vendor/dashboard" class="btn btn-outline-secondary">&larr; Dashboard</a>
            <a href="/shop/vendor/products/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Sales</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($products)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-box-seam text-light" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted">No products yet</h5>
                                    <p class="small text-muted mb-4">You haven't added any products to your store.</p>
                                    <a href="/shop/vendor/products/create" class="btn btn-sm btn-primary">Add Your First Product</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($products as $p): ?>
                            <?php $productUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/shop/product/" . $p['id']; ?>
                            <tr>
                                <td style="width: 60px;">
                                    <?php if($p['image_path']): ?>
                                        <img src="<?= htmlspecialchars($p['image_path']) ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-box"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></td>
                                <td>
                                    <?php if($p['type'] == 'digital'): ?>
                                        <span class="badge bg-info text-dark">Digital</span>
                                    <?php elseif($p['type'] == 'service'): ?>
                                        <span class="badge bg-warning text-dark">Service</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Physical</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-dark fw-bold">₦<?= number_format($p['price'], 2) ?></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td class="text-dark">0</td> <!-- Placeholder for sales count -->
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- View -->
                                        <a href="/shop/product/<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-info text-white" title="View Public Page">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Share Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Share">
                                                <i class="bi bi-share"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="https://wa.me/?text=Check+out+<?= urlencode($p['name']) ?>+<?= urlencode($productUrl) ?>" target="_blank"><i class="bi bi-whatsapp text-success me-2"></i> WhatsApp</a></li>
                                                <li><a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($productUrl) ?>" target="_blank"><i class="bi bi-facebook text-primary me-2"></i> Facebook</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button class="dropdown-item" onclick="copyLink('<?= $productUrl ?>')"><i class="bi bi-clipboard me-2"></i> Copy Link</button></li>
                                            </ul>
                                        </div>

                                        <!-- Edit -->
                                        <a href="/shop/vendor/products/edit/<?= $p['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="/shop/vendor/products/delete/<?= $p['id'] ?>" method="POST" onsubmit="return confirm('Delete this product?');" style="display:inline;">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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

<script>
function copyLink(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            prompt("Copy this link:", url);
        });
    } else {
        prompt("Copy this link:", url);
    }
}
</script>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

