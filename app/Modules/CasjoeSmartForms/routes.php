<?php

use App\Core\Router;
use App\Modules\CasjoeSmartForms\Controllers\FormController;
use App\Modules\CasjoeSmartForms\Controllers\PublicFormController;
use App\Modules\CasjoeShop\Controllers\AdminController;

// Admin Routes (Builder & Stats)
Router::get('/smart-forms', [FormController::class, 'index']);
Router::get('/smart-forms/create', [FormController::class, 'create']);
Router::post('/smart-forms/store', [FormController::class, 'store']);
Router::get('/smart-forms/edit/{id}', [FormController::class, 'edit']);
Router::post('/smart-forms/update/{id}', [FormController::class, 'update']);
Router::post('/smart-forms/delete/{id}', [FormController::class, 'delete']);
Router::get('/smart-forms/stats/{id}', [FormController::class, 'stats']);
Router::get('/smart-forms/templates', [FormController::class, 'templates']);
Router::get('/smart-forms/templates/use/{id}', [FormController::class, 'useTemplate']);
Router::get('/smart-forms/export/{id}', [FormController::class, 'export']);
Router::get('/smart-forms/responses/{id}', [FormController::class, 'responses']);
Router::post('/smart-forms/toggle/{id}', [FormController::class, 'toggle']);

// Public Routes (Rendering & Submission)
Router::get('/sf/{id}', [PublicFormController::class, 'show']);
Router::post('/sf/{id}/submit', [PublicFormController::class, 'submit']);
Router::post('/sf/{id}/track', [PublicFormController::class, 'trackAJAX']);

// Specific Redirect for backward compatibility
Router::get('/forms', function() {
    header('Location: /smart-forms');
    exit;
});
Router::get('/sf', function() {
    header('Location: /smart-forms');
    exit;
});


