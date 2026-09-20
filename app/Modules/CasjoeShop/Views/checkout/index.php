<?php 
$title = "Checkout";
require __DIR__ . '/../layout/store_header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Secure Checkout</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php if(isset($serviceFee) && $serviceFee > 0): ?>
                            <div class="d-flex justify-content-between text-muted mb-2 px-4">
                                <span>Subtotal:</span>
                                <span>₦<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-3 px-4">
                                <span>Transaction Fee:</span>
                                <span>₦<?= number_format($serviceFee, 2) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['coupon'])): ?>
                            <div class="d-flex justify-content-between text-success mb-2 px-4" id="discountRow">
                                <span>Discount (<?= $_SESSION['coupon']['code'] ?>):</span>
                                <span id="discountDisplay">-₦<?= number_format($discountAmount, 2) ?></span>
                            </div>
                        <?php endif; ?>

                        <hr class="mx-4">
                        
                        <h2 class="display-4 fw-bold text-success" id="displayTotal">₦<?= number_format($total, 2) ?></h2>
                        <div class="text-muted">Total Amount</div>
                    </div>

                    <!-- Coupon Form -->
                    <?php if(!isset($_SESSION['coupon'])): ?>
                    <form action="/shop/checkout/coupon/apply" method="POST" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="code" class="form-control" placeholder="Promo Code?">
                            <button class="btn btn-outline-secondary" type="submit">Apply</button>
                        </div>
                        <?php if(isset($_SESSION['coupon_error'])): ?>
                            <div class="text-danger small mt-1"><?= $_SESSION['coupon_error']; unset($_SESSION['coupon_error']); ?></div>
                        <?php endif; ?>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
                            <span><i class="bi bi-tag-fill"></i> Coupon <b><?= $_SESSION['coupon']['code'] ?></b> applied!</span>
                            <a href="/shop/checkout/coupon/remove" class="text-danger"><i class="bi bi-x-circle"></i></a>
                        </div>
                        <?php if(isset($couponError)): ?>
                            <div class="alert alert-warning mb-4"><?= $couponError ?></div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="/shop/checkout/process" method="POST" id="checkoutForm">
                        <input type="hidden" name="total_amount" value="<?= $total ?>" id="inputTotal">
                        
                        <!-- Guest / Contact Info -->
                        <?php if(!isset($_SESSION['user_id'])): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold">Contact Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="guest_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="guest_email" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Shipping Address -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Shipping Address</h6>
                            <div class="mb-3">
                                <label class="form-label">Street Address</label>
                                <input type="text" name="shipping_address" class="form-control" required placeholder="123 Main St">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" name="shipping_city" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State/Region</label>
                                    <input type="text" name="shipping_state" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="shipping_phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Selection -->
                        <?php if (!empty($shippingZones)): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Shipping Region</label>
                            <select class="form-select" name="shipping_zone_id" id="shippingZone" required onchange="updateTotal()">
                                <option value="" data-cost="0" selected disabled>Select your delivery location...</option>
                                <?php foreach($shippingZones as $zone): ?>
                                    <option value="<?= $zone['id'] ?>" data-cost="<?= $zone['cost'] ?>">
                                        <?= htmlspecialchars($zone['zone_name']) ?> (+₦<?= number_format($zone['cost'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Payment Method</label>
                            <div class="card p-3 border-primary bg-light">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payCasjoe" value="casjoe_pay" checked>
                                    <label class="form-check-label fw-bold" for="payCasjoe">
                                        <i class="bi bi-wallet2"></i> Pay with Casjoe Pay
                                    </label>
                                    <div class="small text-muted ps-4 pt-1">Cards, Bank Transfer, Mobile Money</div>
                                </div>
                                
                                <div class="mt-2 ps-4 text-muted small">
                                    <i class="bi bi-shield-lock"></i> You will be redirected to a secure payment page to complete your purchase.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Pay Now <span id="payBtnAmount">₦<?= number_format($total, 2) ?></span></button>
                            <a href="/shop/cart" class="btn btn-link text-muted">Cancel</a>
                        </div>
                    </form>

                    <script>
                        const baseTotal = <?= $total ?>;
                        function updateTotal() {
                            const shippingSelect = document.getElementById('shippingZone');
                            let shippingCost = 0;
                            if (shippingSelect) {
                                const selectedOption = shippingSelect.options[shippingSelect.selectedIndex];
                                shippingCost = parseFloat(selectedOption.getAttribute('data-cost')) || 0;
                            }
                            
                            const newTotal = baseTotal + shippingCost;
                            
                            // Update Display
                            document.getElementById('displayTotal').innerText = '₦' + newTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            document.getElementById('payBtnAmount').innerText = '₦' + newTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            
                            // Update Hidden Input (though server re-calcs, this passes visual check)
                            document.getElementById('inputTotal').value = newTotal;

                            // Add/Update Shipping Line Item
                            const summaryDiv = document.querySelector('.text-center');
                            let shippingRow = document.getElementById('shippingRow');
                            
                            if (shippingCost > 0) {
                                if (!shippingRow) {
                                    shippingRow = document.createElement('div');
                                    shippingRow.id = 'shippingRow';
                                    shippingRow.className = 'd-flex justify-content-between text-muted mb-2 px-4';
                                    shippingRow.innerHTML = '<span>Shipping:</span><span id="shippingDisplay"></span>';
                                    
                                    // Insert before Total text or hr
                                    const hr = summaryDiv.querySelector('hr');
                                    if(hr) {
                                        summaryDiv.insertBefore(shippingRow, hr);
                                    } else {
                                        // Fallback if no HR (no service fee)
                                        const h2 = summaryDiv.querySelector('h2');
                                        summaryDiv.insertBefore(shippingRow, h2);
                                    }
                                }
                                document.getElementById('shippingDisplay').innerText = '₦' + shippingCost.toFixed(2);
                            } else if (shippingRow) {
                                shippingRow.remove();
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

