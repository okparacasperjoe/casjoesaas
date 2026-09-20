<?php

use App\Core\Router;
use App\Modules\CasjoeSupport\Controllers\SupportController;

// echo "DEBUG: CasjoeSupport Routes Loaded.\n"; // Removed debug

Router::get('/support', [SupportController::class, 'index']); // List Tickets
Router::get('/support/create', [SupportController::class, 'create']); // Create Form
Router::post('/support/create', [SupportController::class, 'create']); // Helper: Handles POST too
Router::get('/support/view', [SupportController::class, 'view']); // View Ticket with ?id=
Router::post('/support/view', [SupportController::class, 'view']); // Reply POST
Router::post('/support/ai-chat', [SupportController::class, 'aiChat']); // AI Support Agent powered by Cori Tokens
