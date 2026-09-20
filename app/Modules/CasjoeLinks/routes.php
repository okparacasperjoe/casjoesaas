<?php

use App\Core\Router;
use App\Modules\CasjoeLinks\Controllers\LinksController;

// Dashboard
Router::get('/links', [LinksController::class, 'dashboard']); // Main Dashboard

// Bio Pages
Router::get('/links/bio', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'index']);
Router::get('/links/bio/create', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'create']);
Router::post('/links/bio/store', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'store']);
Router::get('/links/bio/edit/{id}', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'edit']);
Router::post('/links/bio/update/{id}', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'update']);
Router::post('/links/bio/delete/{id}', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'delete']);

// Shortener
Router::get('/links/short', [App\Modules\CasjoeLinks\Controllers\ShortenerController::class, 'index']);
Router::get('/links/short/create', [App\Modules\CasjoeLinks\Controllers\ShortenerController::class, 'create']);
Router::post('/links/short/store', [App\Modules\CasjoeLinks\Controllers\ShortenerController::class, 'store']);
Router::get('/links/short/edit/{id}', [App\Modules\CasjoeLinks\Controllers\ShortenerController::class, 'edit']);
Router::post('/links/short/update/{id}', [App\Modules\CasjoeLinks\Controllers\ShortenerController::class, 'update']);
Router::post('/links/short/delete/{id}', [App\Modules\CasjoeLinks\Controllers\ShortenerController::class, 'delete']);

// QR Codes
Router::get('/links/qr', [App\Modules\CasjoeLinks\Controllers\QrCodeController::class, 'index']);
Router::get('/links/qr/create', [App\Modules\CasjoeLinks\Controllers\QrCodeController::class, 'create']);
Router::post('/links/qr/store', [App\Modules\CasjoeLinks\Controllers\QrCodeController::class, 'store']);
Router::post('/links/qr/delete/{id}', [App\Modules\CasjoeLinks\Controllers\QrCodeController::class, 'delete']);

// Sales Funnels
Router::get('/links/funnels', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'index']);
Router::get('/links/funnels/create', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'create']);
Router::post('/links/funnels/store', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'store']);
Router::get('/links/funnels/edit/{id}', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'edit']);
Router::post('/links/funnels/update/{id}', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'update']);
Router::post('/links/funnels/delete/{id}', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'delete']);
Router::post('/links/funnels/toggle/{id}', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'toggleStatus']);
Router::post('/links/funnels/steps', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'addStep']);
Router::post('/links/funnels/steps/{id}', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'updateStep']);
Router::post('/links/funnels/steps/{id}/delete', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'deleteStep']);
Router::get('/links/funnels/steps/{id}', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'getStep']);
Router::get('/links/funnels/templates', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'getTemplates']);
Router::post('/links/funnels/templates/apply', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'applyTemplate']);
Router::get('/links/funnels/products', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'getProducts']);
Router::get('/links/funnels/forms', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'getForms']);
Router::post('/links/funnels/reorder', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'reorderSteps']);

// Funnel Public Routing
Router::get('/f/{slug}', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'show']);
Router::post('/f/{slug}/process', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'processStep']);
Router::get('/f/{slug}/complete', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'complete']);
Router::get('/f/{slug}/payment/callback', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'paymentCallback']);
Router::post('/f/{slug}/payment/callback', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'paymentCallback']);
Router::get('/f/payment/callback', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'paymentCallback']);
Router::post('/f/payment/callback', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'paymentCallback']);

// Funnel URL Aliases (/funnel/{slug})
Router::get('/funnel/{slug}', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'show']);
Router::post('/funnel/{slug}/process', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'processStep']);
Router::get('/funnel/{slug}/complete', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'complete']);
Router::get('/funnel/{slug}/payment/callback', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'paymentCallback']);
Router::post('/funnel/{slug}/payment/callback', [App\Modules\CasjoeLinks\Controllers\FunnelPublicController::class, 'paymentCallback']);

// Static Sites
Router::get('/links/static', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'index']);
Router::get('/links/static/create', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'create']);
Router::get('/links/static/ai-create', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'aiCreate']);
Router::post('/links/static/store', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'store']);
Router::post('/links/static/ai-generate', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'aiGenerate']);
Router::post('/links/static/ai-refine/{id}', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'aiRefine']);
Router::get('/links/static/edit/{id}', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'edit']);
Router::post('/links/static/update/{id}', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'update']);
Router::post('/links/static/delete/{id}', [App\Modules\CasjoeLinks\Controllers\StaticSiteController::class, 'delete']);

// Public Bio Page Route (/@username and /bio/username)
Router::get('/@{slug}', [App\Modules\CasjoeLinks\Controllers\LinkResolverController::class, 'resolveBioPage']);
Router::get('/bio/{slug}', [App\Modules\CasjoeLinks\Controllers\LinkResolverController::class, 'resolveBioPage']);

// Short URL Resolver (/l/code)
Router::get('/l/{code}', [App\Modules\CasjoeLinks\Controllers\LinkResolverController::class, 'resolveShortUrl']);

// Social Planner & Calendar
Router::get('/links/social', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'index']);
Router::get('/links/social/composer', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'composer']);
Router::post('/links/social/store', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'store']);
Router::get('/links/social/calendar', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'calendar']);
Router::post('/links/social/ai-generate', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'aiGenerate']);
Router::post('/links/social/connect', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'connectAccount']);
Router::post('/links/social/delete', [App\Modules\CasjoeLinks\Controllers\SocialPlannerController::class, 'delete']);

// Bio and Funnel Aliases & Fallbacks
Router::post('/links/bio/update', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'update']);
Router::post('/links/bio/subscribe', [App\Modules\CasjoeLinks\Controllers\BioPageController::class, 'subscribe']);
Router::post('/links/funnels/toggle', [App\Modules\CasjoeLinks\Controllers\FunnelController::class, 'toggleStatus']);

