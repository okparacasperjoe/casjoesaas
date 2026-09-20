<?php
/**
 * Currency Switcher Component
 * Include this in the shop header/navbar
 */

use App\Modules\CasjoeShop\Services\CurrencyService;

$selectedCurrency = CurrencyService::getSelectedCurrency();
$currencies = CurrencyService::$currencies;
?>

<div class="currency-switcher">
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="currencyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-currency-exchange"></i> 
            <span class="d-none d-md-inline">Currency:</span>
            <strong><?= $selectedCurrency ?></strong>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="currencyDropdown">
            <?php foreach ($currencies as $code => $info): ?>
                <li>
                    <a class="dropdown-item <?= $selectedCurrency === $code ? 'active' : '' ?>" 
                       href="/shop/set-currency?currency=<?= $code ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">
                        <span class="currency-symbol"><?= $info['symbol'] ?></span>
                        <span class="currency-name"><?= $info['name'] ?></span>
                        <span class="currency-code">(<?= $code ?>)</span>
                        <?php if ($selectedCurrency === $code): ?>
                            <i class="bi bi-check2 float-end text-success"></i>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<style>
.currency-switcher .dropdown-item {
    font-size: 14px;
    padding: 8px 16px;
}
.currency-switcher .currency-symbol {
    font-weight: bold;
    margin-right: 8px;
    color: #2c3e50;
}
.currency-switcher .currency-name {
    color: #34495e;
}
.currency-switcher .currency-code {
    color: #95a5a6;
    font-size: 12px;
}
.currency-switcher .dropdown-item.active {
    background-color: #e8f5e9;
    color: #27ae60;
}
</style>
