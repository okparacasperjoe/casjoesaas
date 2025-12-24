<?php

use App\Core\Router;

Router::get('/pay', [\App\Modules\CasjoePay\Controllers\PayController::class, 'index']);
Router::get('/pay/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'fund']);
Router::get('/pay/fund/verify', [\App\Modules\CasjoePay\Controllers\PayController::class, 'verifyFund']); // Added route for verification
Router::post('/pay/fund', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processFund']);

Router::get('/pay/transfer', [\App\Modules\CasjoePay\Controllers\PayController::class, 'transfer']);
Router::post('/pay/transfer', [\App\Modules\CasjoePay\Controllers\PayController::class, 'processTransfer']);

// Payment Links
Router::get('/pay/links', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'index']);
Router::post('/pay/links/create', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'create']);
Router::get('/pay/link/{slug}', [\App\Modules\CasjoePay\Controllers\LinkController::class, 'pay']);
