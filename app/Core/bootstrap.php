<?php

// Validates that we are running in the app
define('APP_START', microtime(true));

// 1. Load System Settings (DB Configuration)
// Connect to DB directly for bootstrap
require_once __DIR__ . '/Database.php';
try {
    $pdo = \App\Core\Database::getInstance()->getConnection();
    
    // Check Configured Mode
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // key => value

    $mode = $settings['payment_routing_mode'] ?? 'global';

    if ($mode === 'global') {
        define('FLUTTERWAVE_PUBLIC_KEY', $settings['flutterwave_public_key'] ?? '');
        define('FLUTTERWAVE_SECRET_KEY', $settings['flutterwave_secret_key'] ?? '');
        define('PAYSTACK_PUBLIC_KEY', $settings['paystack_public_key'] ?? '');
        define('PAYSTACK_SECRET_KEY', $settings['paystack_secret_key'] ?? '');
        define('SUDO_API_KEY', $settings['sudo_api_key'] ?? '');
        define('SUDO_API_SECRET', $settings['sudo_api_secret'] ?? '');
    } else {
        // Tenant Mode (Placeholder/Fallback)
        // In future: Load from tenant_settings table based on subdomain
        define('FLUTTERWAVE_PUBLIC_KEY', '');
        define('FLUTTERWAVE_SECRET_KEY', '');
    }



} catch (PDOException $e) {
    // Fallback if DB not ready (install time)
    define('FLUTTERWAVE_PUBLIC_KEY', '');
    define('FLUTTERWAVE_SECRET_KEY', '');
    


    // Mail Config
    define('MAIL_MAILER', 'smtp');
    define('MAIL_HOST', 'smtp.stackmail.com');
    define('MAIL_PORT', 587);
    define('MAIL_USERNAME', 'noreply@casjoe.com');
    define('MAIL_PASSWORD', 'Ply@casjoe.co');
    define('MAIL_ENCRYPTION', 'tls');
    define('MAIL_FROM_ADDRESS', 'hello@casjoe.com');
    define('MAIL_FROM_NAME', 'Casjoe LLC');
}

// Google OAuth
define('GOOGLE_CLIENT_ID', '592104978629-dcu7carhf0kgh1bacu9v3ifmt4s221ej.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'https://app.casjoe.com/auth/google/callback');

// LinkedIn OAuth placeholders
define('LINKEDIN_CLIENT_ID', '');
define('LINKEDIN_CLIENT_SECRET', '');
define('LINKEDIN_REDIRECT_URI', 'https://app.casjoe.com/auth/linkedin/callback');

// 1. Autoloader
spl_autoload_register(function ($class) {
    // Prefix: App\
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 2. Start Session
session_start();

// 3. Resolve Tenant
use App\Core\TenantContext;

if (!TenantContext::resolve()) {
    die("Tenant not found or invalid domain.");
}

// 4. Load Modules
use App\Core\ModuleManager;
ModuleManager::loadModules();

// 5. Load Default Routes
use App\Core\Router;

Router::get('/', [\App\Core\Controllers\GlobalDashboardController::class, 'index']);
Router::get('/dashboard', [\App\Core\Controllers\GlobalDashboardController::class, 'index']);

// Auth Routes
Router::get('/login', [\App\Core\Controllers\AuthController::class, 'login']);
Router::post('/login', [\App\Core\Controllers\AuthController::class, 'attemptLogin']);
Router::get('/logout', [\App\Core\Controllers\AuthController::class, 'logout']);

Router::get('/auth/google', [\App\Core\Controllers\AuthController::class, 'googleRedirect']);
Router::get('/auth/google/callback', [\App\Core\Controllers\AuthController::class, 'googleCallback']);
Router::get('/auth/linkedin', [\App\Core\Controllers\AuthController::class, 'linkedinRedirect']);
Router::get('/auth/linkedin/callback', [\App\Core\Controllers\AuthController::class, 'linkedinCallback']);
Router::get('/register', [\App\Core\Controllers\AuthController::class, 'register']);
Router::post('/register', [\App\Core\Controllers\AuthController::class, 'attemptRegister']);

Router::get('/2fa/setup', [\App\Core\Controllers\TwoFactorController::class, 'setup']);
Router::post('/2fa/setup/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifySetup']);
Router::get('/2fa/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifyLogin']);
Router::post('/2fa/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifyLogin']);




Router::get('/billing', [\App\Core\Controllers\BillingController::class, 'index']);
Router::get('/billing/upgrade', [\App\Core\Controllers\BillingController::class, 'upgrade']);
Router::post('/billing/pay', [\App\Core\Controllers\BillingController::class, 'initiatePayment']);
Router::get('/billing/callback', [\App\Core\Controllers\BillingController::class, 'callback']);
Router::post('/billing/cancel', [\App\Core\Controllers\BillingController::class, 'cancel']);
Router::post('/billing/cancel', [\App\Core\Controllers\BillingController::class, 'cancel']);

Router::get('/security', [\App\Core\Controllers\SecurityController::class, 'index']);
Router::post('/security/2fa/disable', [\App\Core\Controllers\SecurityController::class, 'disable2FA']);

// Admin Routes
// Admin Routes
Router::get('/admin', [\App\Core\Controllers\AdminController::class, 'index']); // Dashboard
Router::get('/admin/users', [\App\Core\Controllers\AdminController::class, 'users']); // User List
Router::get('/admin/users/edit/{id}', [\App\Core\Controllers\AdminController::class, 'edit']);
Router::post('/admin/users/update/{id}', [\App\Core\Controllers\AdminController::class, 'update']);
Router::post('/admin/users/delete/{id}', [\App\Core\Controllers\AdminController::class, 'delete']);
Router::get('/admin/users/impersonate/{id}', [\App\Core\Controllers\AdminController::class, 'impersonate']);
Router::get('/admin/settings', [\App\Core\Controllers\AdminController::class, 'settings']);
Router::post('/admin/settings/update', [\App\Core\Controllers\AdminController::class, 'updateSettings']);
Router::get('/admin/broadcast', [\App\Core\Controllers\AdminController::class, 'broadcast']);
Router::post('/admin/broadcast/send', [\App\Core\Controllers\AdminController::class, 'sendBroadcast']);
