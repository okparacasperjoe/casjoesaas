<?php

use App\Core\Router;

// --- PIN Security ---
Router::get('/pay/pin/setup', [\App\Modules\CasjoePay\Controllers\PinController::class, 'setup']);
Router::get('/pay/pin/verify', [\App\Modules\CasjoePay\Controllers\PinController::class, 'verify']);
Router::post('/pay/pin/process', [\App\Modules\CasjoePay\Controllers\PinController::class, 'process']);

// --- Main Dashboard / Wallet Page ---
Router::get('/pay', [\App\Modules\CasjoePay\Controllers\PayController::class, 'index']);
Router::get('/casjoe-pay', [\App\Modules\CasjoePay\Controllers\PayController::class, 'index']);

// --- Wallet Funding ---
Router::get('/pay/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'fund']);
Router::get('/casjoe-pay/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'fund']);

Router::get('/pay/fund/verify', [\App\Modules\CasjoePay\Controllers\PayController::class, 'verifyFund']);
Router::get('/casjoe-pay/fund/verify', [\App\Modules\CasjoePay\Controllers\PayController::class, 'verifyFund']);

Router::post('/pay/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processFund']);
Router::post('/casjoe-pay/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processFund']);

// --- Wallet Management ---
Router::post('/pay/wallet/create', [\App\Modules\CasjoePay\Controllers\PayController::class, 'createWallet']);
Router::post('/casjoe-pay/wallet/create', [\App\Modules\CasjoePay\Controllers\PayController::class, 'createWallet']);

// --- Transfers ---
Router::get('/pay/transfer', [\App\Modules\CasjoePay\Controllers\PayController::class, 'transfer']);
Router::get('/casjoe-pay/transfer', [\App\Modules\CasjoePay\Controllers\PayController::class, 'transfer']);
Router::post('/pay/transfer/resolve-account', [\App\Modules\CasjoePay\Controllers\PayController::class, 'resolveAccount']);

Router::post('/pay/transfer', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processTransfer']);
Router::post('/casjoe-pay/transfer', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processTransfer']);

// --- Payment Links ---
Router::get('/pay/links', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'index']);
Router::get('/casjoe-pay/links', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'index']);

Router::post('/pay/links/create', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'create']);
Router::post('/casjoe-pay/links/create', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'create']);

Router::post('/pay/links/delete/{id}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'delete']);
Router::post('/casjoe-pay/links/delete/{id}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'delete']);

Router::get('/pay/link/{slug}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'pay']);
Router::get('/casjoe-pay/link/{slug}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'pay']);

Router::post('/pay/process/{slug}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'processPayment']);
Router::post('/casjoe-pay/process/{slug}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'processPayment']);

// --- Payment Requests ---
Router::get('/pay/requests', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'index']);
Router::post('/pay/requests/create', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'create']);
Router::get('/pay/requests/edit/{id}', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'edit']);
Router::post('/pay/requests/update/{id}', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'update']);
Router::post('/pay/requests/delete/{id}', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'delete']);
Router::get('/pay/request/{reference}', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'pay']);
Router::post('/pay/request/process/{reference}', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'processPayment']);
Router::get('/pay/request/verify', [\App\Modules\CasjoePay\Controllers\PaymentRequestController::class, 'verify']);

Router::get('/pay/link-callback', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'callback']);
Router::get('/casjoe-pay/link-callback', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'callback']);

// --- Virtual Cards ---
Router::get('/pay/cards', [\App\Modules\CasjoePay\Controllers\CardController::class, 'index']);
Router::get('/casjoe-pay/cards', [\App\Modules\CasjoePay\Controllers\CardController::class, 'index']);
Router::get('/pay/cards/details', [\App\Modules\CasjoePay\Controllers\CardController::class, 'getCardDetails']);
Router::get('/pay/cards/fund', [\App\Modules\CasjoePay\Controllers\CardController::class, 'showFundForm']);
Router::post('/pay/cards/fund', [\App\Modules\CasjoePay\Controllers\CardController::class, 'processFundCard']);
Router::post('/pay/cards/freeze', [\App\Modules\CasjoePay\Controllers\CardController::class, 'toggleFreeze']);
Router::post('/pay/cards/terminate', [\App\Modules\CasjoePay\Controllers\CardController::class, 'terminate']);

Router::post('/pay/cards/create', [\App\Modules\CasjoePay\Controllers\CardController::class, 'create']);
Router::post('/casjoe-pay/cards/create', [\App\Modules\CasjoePay\Controllers\CardController::class, 'create']);
Router::post('/pay/cards/delete-mock', [\App\Modules\CasjoePay\Controllers\CardController::class, 'deleteMock']);

// --- Virtual Accounts ---
Router::get('/pay/virtual-bank', [\App\Modules\CasjoePay\Controllers\VirtualBankController::class, 'index']);
Router::get('/casjoe-pay/virtual-bank', [\App\Modules\CasjoePay\Controllers\VirtualBankController::class, 'index']);

Router::post('/pay/virtual-bank/create', [\App\Modules\CasjoePay\Controllers\VirtualBankController::class, 'create']);
Router::post('/casjoe-pay/virtual-bank/create', [\App\Modules\CasjoePay\Controllers\VirtualBankController::class, 'create']);
Router::post('/pay/virtual-bank/delete-mock', [\App\Modules\CasjoePay\Controllers\VirtualBankController::class, 'deleteMock']);

// --- Virtual Phone Numbers ---
Router::get('/pay/phone', [\App\Modules\CasjoePay\Controllers\PhoneController::class, 'index']);
Router::get('/casjoe-pay/phone', [\App\Modules\CasjoePay\Controllers\PhoneController::class, 'index']);

Router::post('/pay/phone/create', [\App\Modules\CasjoePay\Controllers\PhoneController::class, 'create']);
Router::post('/casjoe-pay/phone/create', [\App\Modules\CasjoePay\Controllers\PhoneController::class, 'create']);

// --- Currency Conversion ---
Router::get('/pay/convert', [\App\Modules\CasjoePay\Controllers\PayController::class, 'convert']);
Router::get('/casjoe-pay/convert', [\App\Modules\CasjoePay\Controllers\PayController::class, 'convert']);

Router::post('/pay/convert/process', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processConvert']);
Router::post('/casjoe-pay/convert/process', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processConvert']);

Router::get('/pay/api/rate', [\App\Modules\CasjoePay\Controllers\PayController::class, 'getRateApi']);
Router::get('/casjoe-pay/api/rate', [\App\Modules\CasjoePay\Controllers\PayController::class, 'getRateApi']);

// --- Naira Cards (Virtual + Physical ATM) ---
Router::get('/pay/naira-cards', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'index']);
Router::get('/casjoe-pay/naira-cards', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'index']);

Router::post('/pay/naira-cards/register', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'registerUser']);
Router::post('/casjoe-pay/naira-cards/register', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'registerUser']);



Router::post('/pay/naira-cards/create-physical', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'createPhysicalCard']);
Router::post('/casjoe-pay/naira-cards/create-physical', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'createPhysicalCard']);

Router::post('/pay/naira-cards/toggle-status', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'toggleStatus']);
Router::post('/casjoe-pay/naira-cards/toggle-status', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'toggleStatus']);

Router::get('/pay/naira-cards/history/{card_id}', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'history']);
Router::get('/casjoe-pay/naira-cards/history/{card_id}', [\App\Modules\CasjoePay\Controllers\NairaCardController::class, 'history']);

// --- Naira Card Webhook ---
Router::post('/webhook/naira-card', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'nairaCard']);
Router::get('/webhook/naira-card', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'nairaCard']);

// --- Strowallet & Virtual Account Webhooks ---
Router::post('/api/strowallet/webhook', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::get('/api/strowallet/webhook', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::post('/pay/webhook/strowallet', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::get('/pay/webhook/strowallet', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::post('/webhook/safeheaven', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::post('/webhook/paga', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::post('/webhook/amucha', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);

// --- ZiiroPay Webhooks ---
Router::post('/api/ziiropay/webhook', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::get('/api/ziiropay/webhook', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::post('/pay/webhook/ziiropay', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::get('/pay/webhook/ziiropay', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::post('/webhook/ziiropay', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);
Router::get('/webhook/ziiropay', [\App\Modules\CasjoePay\Controllers\WebhookController::class, 'strowallet']);

Router::get('/pay/transfer/banks', [\App\Modules\CasjoePay\Controllers\PayController::class, 'getBanks']);
Router::get('/casjoe-pay/transfer/banks', [\App\Modules\CasjoePay\Controllers\PayController::class, 'getBanks']);

// --- PIN Recovery & Self-Service Reset ---
Router::get('/pay/pin/forgot', [\App\Modules\CasjoePay\Controllers\PinController::class, 'forgot']);
Router::post('/pay/pin/send-otp', [\App\Modules\CasjoePay\Controllers\PinController::class, 'sendOtp']);
Router::get('/pay/pin/verify-otp', [\App\Modules\CasjoePay\Controllers\PinController::class, 'verifyOtpView']);
Router::post('/pay/pin/confirm-otp', [\App\Modules\CasjoePay\Controllers\PinController::class, 'confirmOtp']);

// --- Hosted Checkout ---
Router::get('/pay/checkout', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'index']);
Router::post('/pay/checkout', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'index']);
Router::post('/pay/checkout/process', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'process']);
Router::get('/pay/checkout/callback', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'callback']);
Router::post('/pay/checkout/callback', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'callback']);

Router::get('/casjoe-pay/checkout', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'index']);
Router::post('/casjoe-pay/checkout', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'index']);
Router::post('/casjoe-pay/checkout/process', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'process']);
Router::get('/casjoe-pay/checkout/callback', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'callback']);
Router::post('/casjoe-pay/checkout/callback', [\App\Modules\CasjoePay\Controllers\CheckoutController::class, 'callback']);

// Plans Routes
Router::get('/pay/plans', [\App\Modules\CasjoePay\Controllers\PlanController::class, 'index']);
Router::get('/pay/plans/create', [\App\Modules\CasjoePay\Controllers\PlanController::class, 'create']);
Router::post('/pay/plans/store', [\App\Modules\CasjoePay\Controllers\PlanController::class, 'store']);

// Wallet Fund Aliases
Router::get('/pay/wallet/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'fund']);
Router::post('/pay/wallet/fund/init', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processFund']);
Router::post('/pay/initiate', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processFund']);



