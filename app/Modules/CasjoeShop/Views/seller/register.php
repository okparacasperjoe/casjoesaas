<?php 
$title = "Become a Vendor";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <h1 class="h3 fw-bold mb-3"><i class="bi bi-shop"></i> Start Selling on Casjoe Mart</h1>
                    <p class="text-muted mb-4">Create your own store and sell physical or digital products instantly.</p>
                    
                    <form action="/shop/vendor/register" method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" name="store_name" class="form-control" id="storeName" placeholder="My Awesome Store" required>
                            <label for="storeName">Store Name</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Create My Store</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

