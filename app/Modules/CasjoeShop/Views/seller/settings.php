<?php 
$title = "Store Settings - Vendor Dashboard";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark"><i class="bi bi-gear-fill"></i> Store Settings</h1>
            <div class="text-muted">Manage your store preferences</div>
        </div>
        <div>
            <a href="/shop/vendor/dashboard" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">Transaction Fees</h5>
                </div>
                <div class="card-body">
                    <form action="/shop/vendor/settings" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block mb-3 text-dark">Who pays the transaction fees?</label>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="fee_bearer" id="fee_merchant" value="merchant" <?= ($vendor['fee_bearer'] ?? 'merchant') === 'merchant' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_merchant">
                                    <strong class="text-dark">Merchant (You)</strong>
                                    <div class="text-muted small">
                                        Fees are deducted from your earnings. Customers see free shipping/processing.
                                        <br><em>Example: Product ₦100 -> Customer pays ₦100 -> You receive ~₦97.</em>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="fee_bearer" id="fee_customer" value="customer" <?= ($vendor['fee_bearer'] ?? 'merchant') === 'customer' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="fee_customer">
                                    <strong class="text-dark">Customer (Buyer)</strong>
                                    <div class="text-muted small">
                                        Fees are added to the transaction total. You receive the full product price.
                                        <br><em>Example: Product ₦100 -> Customer pays ~₦103 -> You receive ₦100.</em>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Facebook Pixel ID</label>
                            <input type="text" name="facebook_pixel_id" class="form-control" placeholder="e.g. 123456789012345" value="<?= htmlspecialchars($vendor['facebook_pixel_id'] ?? '') ?>">
                            <div class="form-text">Enter your Facebook Pixel ID to track page views and events on your products.</div>
                        </div>

                        <hr>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Store Currency</label>
                            <select name="currency" class="form-select">
                                <?php 
                                $currencies = [
                                    'USD' => '$ US Dollar',
                                    'GBP' => '£ British Pound',
                                    'EUR' => '€ Euro',
                                    'NGN' => '₦ Nigerian Naira',
                                    'KES' => 'KSh Kenyan Shilling',
                                    'ZAR' => 'R South African Rand',
                                    'GHS' => 'GH₵ Ghanaian Cedi',
                                    'XOF' => 'CFA West African CFA Franc',
                                    'UGX' => 'USh Ugandan Shilling'
                                ];
                                $currentCurrency = $vendor['currency'] ?? 'NGN';
                                foreach ($currencies as $code => $name): 
                                ?>
                                    <option value="<?= $code ?>" <?= $currentCurrency === $code ? 'selected' : '' ?>>
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Your product prices will be set in this currency. Customers can pay in their preferred currency.</div>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

