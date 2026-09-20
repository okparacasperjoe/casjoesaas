<?php 
$title = "Shopping Cart";
require __DIR__ . '/../layout/store_header.php';
require __DIR__ . '/../helpers/price_helper.php';
$customerCurrency = \App\Modules\CasjoeShop\Services\CurrencyService::getSelectedCurrency();
?>

<div class="container mt-5">
    <h1 class="fw-bold mb-4">Your Cart</h1>

    <?php if(empty($cartItems)): ?>
        <div class="text-center py-5">
            <h3 class="text-muted">Your cart is empty</h3>
            <a href="/shop" class="btn btn-primary mt-3">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="small text-muted"><?= ucfirst($item['type']) ?></div>
                                    </td>
                                    <td><?= \App\Modules\CasjoeShop\Services\CurrencyService::format($item['display_price'], $customerCurrency) ?></td>
                                    <td><?= $item['qty'] ?></td>
                                    <td class="fw-bold"><?= \App\Modules\CasjoeShop\Services\CurrencyService::format($item['subtotal'], $customerCurrency) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Summary</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span class="fw-bold"><?= \App\Modules\CasjoeShop\Services\CurrencyService::format($total, $customerCurrency) ?></span>
                        </div>
                        <div class="d-grid">
                            <a href="/shop/checkout" class="btn btn-success btn-lg">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

