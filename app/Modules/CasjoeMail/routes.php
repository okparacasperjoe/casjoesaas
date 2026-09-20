<?php

use App\Core\Router;
use App\Modules\CasjoeMail\Controllers\MailController;

// Dashboard
Router::get('/mail', [MailController::class, 'index']);
Router::get('/mail/settings', [App\Modules\CasjoeMail\Controllers\SettingsController::class, 'index']);
Router::post('/mail/settings/save', [App\Modules\CasjoeMail\Controllers\SettingsController::class, 'save']);

// Campaigns
Router::get('/mail/campaigns', [MailController::class, 'campaigns']);
Router::get('/mail/campaigns/create', [MailController::class, 'createCampaign']);
Router::post('/mail/campaigns/send', [MailController::class, 'sendCampaign']);
Router::post('/mail/campaigns/delete', [MailController::class, 'deleteCampaign']);
Router::get('/mail/campaigns/edit', [MailController::class, 'editCampaign']);
Router::post('/mail/campaigns/update', [MailController::class, 'updateCampaign']);

// Lists
Router::get('/mail/lists', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'index']);
Router::get('/mail/lists/create', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'create']);
Router::post('/mail/lists/store', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'store']);
Router::get('/mail/lists/view', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'show']);
Router::post('/mail/lists/update-name', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'updateName']);
Router::post('/mail/lists/add-subscriber', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'addSubscriber']);
Router::get('/mail/lists/import', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'import']);
Router::post('/mail/lists/import-process', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'importProcess']);
Router::post('/mail/lists/import-link-process', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'importLinkProcess']);
Router::post('/mail/lists/sync-erp', [App\Modules\CasjoeMail\Controllers\ListsController::class, 'syncErp']);

// AI & Templates
Router::get('/mail/templates', [MailController::class, 'templates']);
Router::get('/mail/templates/view', [MailController::class, 'viewTemplate']);
Router::post('/mail/templates/delete', [MailController::class, 'deleteTemplate']);
Router::get('/mail/templates/edit', [MailController::class, 'editTemplate']);
Router::post('/mail/templates/update', [MailController::class, 'updateTemplate']);
Router::post('/mail/ai/generate', [MailController::class, 'generateAiContent']);
Router::get('/mail/template/get', [MailController::class, 'templateContent']);


// Sequences (Automations)
Router::get('/mail/sequences', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'index']);
Router::get('/mail/sequences/create', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'create']);
Router::post('/mail/sequences/store', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'store']);
Router::get('/mail/sequences/edit', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'edit']);
Router::post('/mail/sequences/update', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'update']);
Router::post('/mail/sequences/delete', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'delete']);
Router::post('/mail/sequences/duplicate', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'duplicate']);
Router::post('/mail/sequences/add-step', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'addStep']);
Router::post('/mail/sequences/update-step', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'updateStep']);
Router::post('/mail/sequences/delete-step', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'deleteStep']);
Router::post('/mail/sequences/enroll', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'enroll']);
Router::post('/mail/sequences/unenroll', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'unenroll']);
Router::post('/mail/sequences/pause-enrollment', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'pauseEnrollment']);
Router::post('/mail/sequences/resume-enrollment', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'resumeEnrollment']);
Router::get('/mail/sequences/enrollments', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'enrollments']);

// Forms
Router::get('/mail/forms', [MailController::class, 'forms']);

// Additional Campaign, Template, Compose & Public Routes
Router::post('/mail/campaigns/save-visual', [MailController::class, 'saveVisualCampaign']);
Router::get('/mail/compose', [MailController::class, 'compose']);
Router::post('/mail/send', [MailController::class, 'send']);
Router::get('/mail/templates/create', [MailController::class, 'createTemplate']);
Router::post('/mail/templates/save', [MailController::class, 'saveTemplate']);
Router::get('/mail/subscribe', [MailController::class, 'subscribe']);
Router::post('/mail/subscribe/store', [MailController::class, 'subscribeStore']);

// Sequence URL Aliases
Router::post('/mail/sequences/steps/add', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'addStep']);
Router::post('/mail/sequences/steps/delete', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'deleteStep']);
Router::post('/mail/sequences/enrollments/pause', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'pauseEnrollment']);
Router::post('/mail/sequences/enrollments/resume', [App\Modules\CasjoeMail\Controllers\SequenceController::class, 'resumeEnrollment']);

