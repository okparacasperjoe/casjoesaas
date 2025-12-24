<?php

use App\Core\Router;
use App\Modules\CasjoeMail\Controllers\MailController;

// Dashboard
Router::get('/mail', [MailController::class, 'index']);

// Campaigns
Router::get('/mail/campaigns', [MailController::class, 'campaigns']);
Router::get('/mail/campaigns/create', [MailController::class, 'createCampaign']);

// AI & Templates
Router::post('/mail/ai/generate', [MailController::class, 'generateAiContent']);
Router::get('/mail/template/get', [MailController::class, 'templateContent']);
