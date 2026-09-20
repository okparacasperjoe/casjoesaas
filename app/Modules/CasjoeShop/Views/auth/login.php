<?php 
$title = "Login - Casjoe Mart";
require __DIR__ . '/../layout/store_header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center fw-bold mb-4">Welcome Back</h2>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- Guest Checkout Option -->
                    <?php if(isset($_GET['redirect']) && strpos($_GET['redirect'], 'checkout') !== false): ?>
                    <div class="mb-4 text-center">
                        <div class="d-grid">
                            <form action="/shop/guest-checkout" method="POST">
                                <button type="submit" class="btn btn-outline-secondary btn-lg">
                                    Continue as Guest
                                </button>
                            </form>
                        </div>
                        <div class="position-relative my-4">
                            <hr>
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted">OR</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form action="/shop/login" method="POST">
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '/shop') ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                        </div>
                        
                        <div class="text-center">
                            Don't have an account? <a href="/shop/register" class="text-decoration-none">Create one</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>
