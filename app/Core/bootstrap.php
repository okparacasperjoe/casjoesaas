<?php

// Validates that we are running in the app
define('APP_START', microtime(true));

// Load Composer Autoloader
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// 1. Load System Settings (DB Configuration)
// Connect to DB directly for bootstrap
require_once __DIR__ . '/Database.php';
try {
    $pdo = \App\Core\Database::getInstance()->getConnection();
    
    // Check Configured Mode
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // key => value

    define('AI_EMPLOYEE_PRICE_NGN', isset($settings['ai_employee_price_ngn']) && $settings['ai_employee_price_ngn'] !== '' ? (float)$settings['ai_employee_price_ngn'] : 15000);
    define('AI_EMPLOYEE_PRICE_USD', isset($settings['ai_employee_price_usd']) && $settings['ai_employee_price_usd'] !== '' ? (float)$settings['ai_employee_price_usd'] : 15);

    $mode = $settings['payment_routing_mode'] ?? 'global';

    // Always define platform payment & virtual card keys cleanly trimmed so services never fail when mode is 'wallet' or 'tenant'
    define('FLUTTERWAVE_PUBLIC_KEY', trim($settings['flutterwave_public_key'] ?? ''));
    define('FLUTTERWAVE_SECRET_KEY', trim($settings['flutterwave_secret_key'] ?? ''));
    define('PAYSTACK_PUBLIC_KEY', trim($settings['paystack_public_key'] ?? ''));
    define('PAYSTACK_SECRET_KEY', trim($settings['paystack_secret_key'] ?? ''));
    define('SUDO_API_KEY', trim($settings['sudo_api_key'] ?? ''));
    define('SUDO_API_SECRET', trim($settings['sudo_api_secret'] ?? ''));
    define('STROWALLET_PUBLIC_KEY', trim($settings['strowallet_public_key'] ?? ''));
    define('STROWALLET_SECRET_KEY', trim($settings['strowallet_secret_key'] ?? ''));
    define('ZIIROPAY_PUBLIC_KEY', trim($settings['ziiropay_public_key'] ?? '55acb03a4ff77aa26c05a1ab0ab5c7e661871743'));
    define('ZIIROPAY_SECRET_KEY', trim($settings['ziiropay_secret_key'] ?? $settings['strowallet_secret_key'] ?? ''));
    // OAuth Integration Keys (Dynamically loaded from system_settings)
    $googleClientId = !empty($settings['google_client_id']) ? trim($settings['google_client_id']) : '592104978629-dcu7carhf0kgh1bacu9v3ifmt4s221ej.apps.googleusercontent.com';
    $googleClientSecret = !empty($settings['google_client_secret']) ? trim($settings['google_client_secret']) : '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';
    define('GOOGLE_CLIENT_ID', $googleClientId);
    define('GOOGLE_CLIENT_SECRET', $googleClientSecret);
    define('GOOGLE_REDIRECT_URI', 'https://app.casjoe.com/auth/google/callback');

    define('LINKEDIN_CLIENT_ID', trim($settings['linkedin_client_id'] ?? ''));
    define('LINKEDIN_CLIENT_SECRET', trim($settings['linkedin_client_secret'] ?? ''));
    define('LINKEDIN_REDIRECT_URI', 'https://app.casjoe.com/auth/linkedin/callback');

} catch (PDOException $e) {
    // Fallback if DB not ready (install time)
    define('FLUTTERWAVE_PUBLIC_KEY', '');
    define('FLUTTERWAVE_SECRET_KEY', '');
    if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', '592104978629-dcu7carhf0kgh1bacu9v3ifmt4s221ej.apps.googleusercontent.com');
    if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', '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');
    if (!defined('GOOGLE_REDIRECT_URI')) define('GOOGLE_REDIRECT_URI', 'https://app.casjoe.com/auth/google/callback');
    if (!defined('LINKEDIN_CLIENT_ID')) define('LINKEDIN_CLIENT_ID', '');
    if (!defined('LINKEDIN_CLIENT_SECRET')) define('LINKEDIN_CLIENT_SECRET', '');
    if (!defined('LINKEDIN_REDIRECT_URI')) define('LINKEDIN_REDIRECT_URI', 'https://app.casjoe.com/auth/linkedin/callback');
    if (!defined('AI_EMPLOYEE_PRICE_NGN')) define('AI_EMPLOYEE_PRICE_NGN', 15000);
    if (!defined('AI_EMPLOYEE_PRICE_USD')) define('AI_EMPLOYEE_PRICE_USD', 15);
}

if (!defined('AI_EMPLOYEE_PRICE_NGN')) define('AI_EMPLOYEE_PRICE_NGN', 15000);
if (!defined('AI_EMPLOYEE_PRICE_USD')) define('AI_EMPLOYEE_PRICE_USD', 15);

// Mail Config
if (!defined('MAIL_MAILER')) define('MAIL_MAILER', 'smtp');
if (!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.stackmail.com');
if (!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if (!defined('MAIL_USERNAME')) define('MAIL_USERNAME', 'noreply@casjoe.com');
if (!defined('MAIL_PASSWORD')) define('MAIL_PASSWORD', 'noreply@casjoe.com');
if (!defined('MAIL_ENCRYPTION')) define('MAIL_ENCRYPTION', 'tls');
if (!defined('MAIL_FROM_ADDRESS')) define('MAIL_FROM_ADDRESS', 'noreply@casjoe.com');
if (!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', 'Casjoe');

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

// Super Admin Path Constant (used in all admin views via ADMIN_PATH)
define('ADMIN_PATH', 'casper-joe');

// 4. Load Modules
use App\Core\ModuleManager;
ModuleManager::loadModules();

// Permanently Load All 9 Module Routes (Zero 404 Guarantee)
$allModuleRoutes = [
    'CasjoeAcademy',
    'CasjoeCloud',
    'CasjoeERP',
    'CasjoeLinks',
    'CasjoeMail',
    'CasjoePay',
    'CasjoeShop',
    'CasjoeSmartForms',
    'CasjoeSupport',
];
foreach ($allModuleRoutes as $modName) {
    $modRouteFile = __DIR__ . '/../Modules/' . $modName . '/routes.php';
    if (file_exists($modRouteFile)) {
        require_once $modRouteFile;
    }
}

// 5. Load Default Routes
use App\Core\Router;

// Cloud Asset Serving (Global fallback across all modules and external views)
Router::get('/cloud/asset/{tenant_id}/{filename}', [\App\Modules\CasjoeCloud\Controllers\AssetController::class, 'serve']);

Router::get('/', [\App\Core\Controllers\LandingController::class, 'index']);
Router::get('/dashboard', [\App\Core\Controllers\GlobalDashboardController::class, 'index']);
Router::post('/Modules/toggle', [\App\Core\Controllers\ModuleController::class, 'toggle']);
Router::post('/modules/toggle', [\App\Core\Controllers\ModuleController::class, 'toggle']);

// Auth Routes
Router::get('/login', [\App\Core\Controllers\AuthController::class, 'login']);
Router::post('/login', [\App\Core\Controllers\AuthController::class, 'attemptLogin']);
Router::get('/logout', [\App\Core\Controllers\AuthController::class, 'logout']);

// --- Public Careers Routes ---
Router::get('/careers/job', [\App\Core\Controllers\PublicCareersController::class, 'showJob']);
Router::post('/careers/job/apply', [\App\Core\Controllers\PublicCareersController::class, 'submitApplication']);

Router::get('/check-modules', [\App\Core\Controllers\PublicCareersController::class, 'checkModules']);

Router::get('/auth/google', [\App\Core\Controllers\AuthController::class, 'googleRedirect']);
Router::get('/auth/google/callback', [\App\Core\Controllers\AuthController::class, 'googleCallback']);
Router::post('/auth/google/onetap', [\App\Core\Controllers\AuthController::class, 'googleOneTap']);
Router::get('/auth/linkedin', [\App\Core\Controllers\AuthController::class, 'linkedinRedirect']);
Router::get('/auth/linkedin/callback', [\App\Core\Controllers\AuthController::class, 'linkedinCallback']);
Router::get('/register', [\App\Core\Controllers\AuthController::class, 'register']);
Router::post('/register', [\App\Core\Controllers\AuthController::class, 'attemptRegister']);

Router::get('/verify', [\App\Core\Controllers\AuthController::class, 'verifyEmail']);
Router::get('/resend-verification', [\App\Core\Controllers\AuthController::class, 'resendVerification']);
Router::post('/resend-verification', [\App\Core\Controllers\AuthController::class, 'resendVerification']);

Router::get('/forgot-password', [\App\Core\Controllers\AuthController::class, 'forgotPassword']);
Router::post('/forgot-password', [\App\Core\Controllers\AuthController::class, 'handleForgotPassword']);
Router::get('/reset-password', [\App\Core\Controllers\AuthController::class, 'resetPasswordForm']);
Router::post('/reset-password', [\App\Core\Controllers\AuthController::class, 'handleResetPassword']);

Router::get('/2fa/setup', [\App\Core\Controllers\TwoFactorController::class, 'setup']);
Router::post('/2fa/setup/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifySetup']);
Router::get('/2fa/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifyLogin']);
Router::post('/2fa/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifyLogin']);

// Profile Routes
Router::get('/profile', [\App\Core\Controllers\ProfileController::class, 'index']);
Router::post('/profile/update', [\App\Core\Controllers\ProfileController::class, 'updateProfile']);
Router::post('/profile/currency', [\App\Core\Controllers\ProfileController::class, 'updateCurrency']);
Router::post('/profile/password', [\App\Core\Controllers\ProfileController::class, 'updatePassword']);
Router::post('/profile/kyc', [\App\Core\Controllers\ProfileController::class, 'submitKyc']);
Router::post('/profile/kyc/details', [\App\Core\Controllers\ProfileController::class, 'updateKycDetails']);
Router::post('/profile/kyc/update-details', [\App\Core\Controllers\ProfileController::class, 'updateKycDetails']);

// AI Office
Router::get('/ai-office', [\App\Core\Controllers\AIEmployeeController::class, 'index']);
Router::post('/ai-office/chat', [\App\Core\Controllers\AIEmployeeController::class, 'chat']);
Router::post('/ai-office/queue/approve', [\App\Core\Controllers\AIEmployeeController::class, 'approveQueueAction']);
Router::post('/ai-office/queue/reject', [\App\Core\Controllers\AIEmployeeController::class, 'rejectQueueAction']);
Router::post('/ai-office/automations/create', [\App\Core\Controllers\AIEmployeeController::class, 'createAutomation']);
Router::post('/ai-office/automations/delete', [\App\Core\Controllers\AIEmployeeController::class, 'deleteAutomation']);

// AI Builder / Onboarding
Router::get('/onboarding', [\App\Core\Controllers\OnboardingController::class, 'index']);
Router::get('/onboarding/step1', [\App\Core\Controllers\OnboardingController::class, 'step1']);
Router::post('/onboarding/step1', [\App\Core\Controllers\OnboardingController::class, 'postStep1']);
Router::get('/onboarding/step2', [\App\Core\Controllers\OnboardingController::class, 'step2']);
Router::post('/onboarding/step2', [\App\Core\Controllers\OnboardingController::class, 'postStep2']);
Router::get('/onboarding/step3', [\App\Core\Controllers\OnboardingController::class, 'step3']);
Router::post('/onboarding/step3', [\App\Core\Controllers\OnboardingController::class, 'postStep3']);
Router::get('/onboarding/step4', [\App\Core\Controllers\OnboardingController::class, 'step4']);
Router::post('/onboarding/step4', [\App\Core\Controllers\OnboardingController::class, 'postStep4']);
Router::get('/onboarding/step5', [\App\Core\Controllers\OnboardingController::class, 'step5']);
Router::post('/onboarding/step5', [\App\Core\Controllers\OnboardingController::class, 'postStep5']);
Router::get('/onboarding/step6', [\App\Core\Controllers\OnboardingController::class, 'step6']);
Router::get('/onboarding/action/{action}', [\App\Core\Controllers\OnboardingController::class, 'action']);
Router::get('/onboarding/skip-action', [\App\Core\Controllers\OnboardingController::class, 'skipAction']);
Router::get('/onboarding/step7', [\App\Core\Controllers\OnboardingController::class, 'step7']);
Router::get('/onboarding/complete', [\App\Core\Controllers\OnboardingController::class, 'complete']);
Router::get('/onboarding/finish', [\App\Core\Controllers\OnboardingController::class, 'finish']);

Router::get('/ai-builder', [\App\Core\Controllers\BusinessBuilderController::class, 'index']);
Router::post('/ai-builder/chat', [\App\Core\Controllers\BusinessBuilderController::class, 'chat']);
Router::post('/ai-builder/complete', [\App\Core\Controllers\BusinessBuilderController::class, 'complete']);
Router::post('/ai-builder/process', [\App\Core\Controllers\BusinessBuilderController::class, 'processWizard']);

// Google Phone Prompt
Router::get('/auth/google/phone', [\App\Core\Controllers\AuthController::class, 'promptGooglePhone']);
Router::post('/auth/google/phone', [\App\Core\Controllers\AuthController::class, 'saveGooglePhone']);

// Billing & Coupon Redemptions (Zero 404 Guarantee)
Router::get('/billing', [\App\Core\Controllers\BillingController::class, 'index']);
Router::get('/billing/upgrade', [\App\Core\Controllers\BillingController::class, 'upgrade']);
Router::post('/billing/pay', [\App\Core\Controllers\BillingController::class, 'initiatePayment']);
Router::get('/billing/callback', [\App\Core\Controllers\BillingController::class, 'callback']);
Router::get('/billing/redeem-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::post('/billing/redeem-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::get('/billing/apply-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::post('/billing/apply-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::get('/billing/validate-coupon', [\App\Core\Controllers\BillingController::class, 'validateCoupon']);
Router::post('/billing/validate-coupon', [\App\Core\Controllers\BillingController::class, 'validateCoupon']);
Router::get('/billing/coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::post('/billing/coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::get('/redeem-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::post('/redeem-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::get('/apply-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::post('/apply-coupon', [\App\Core\Controllers\BillingController::class, 'redeemCoupon']);
Router::post('/billing/cancel', [\App\Core\Controllers\BillingController::class, 'cancel']);
Router::post('/billing/subscribe', [\App\Core\Controllers\BillingController::class, 'initiatePayment']);
Router::post('/ai-office/subscribe', [\App\Core\Controllers\BillingController::class, 'upgrade']);
Router::get('/api/integrations', [\App\Modules\CasjoeMail\Controllers\MailController::class, 'getIntegrations']);

// Security
Router::get('/security', [\App\Core\Controllers\SecurityController::class, 'index']);
Router::post('/security/2fa/disable', [\App\Core\Controllers\SecurityController::class, 'disable2FA']);

// Knowledge Base
Router::get('/invoice/view/{token}', [\App\Core\Controllers\InvoiceController::class, 'view']);
Router::post('/invoice/pay/{token}', [\App\Core\Controllers\InvoiceController::class, 'pay']);
Router::get('/kb', [\App\Core\Controllers\KnowledgeBaseController::class, 'index']);
Router::get('/kb/article/{slug}', [\App\Core\Controllers\KnowledgeBaseController::class, 'view']);

// Support Routes (Core Fallback)
Router::get('/support', [\App\Core\Controllers\SupportController::class, 'index']);
Router::get('/support/create', [\App\Core\Controllers\SupportController::class, 'create']);
Router::post('/support/create', [\App\Core\Controllers\SupportController::class, 'store']);
Router::get('/support/view', [\App\Core\Controllers\SupportController::class, 'view']);
Router::post('/support/view', [\App\Core\Controllers\SupportController::class, 'view']);
Router::post('/support/ai-chat', [\App\Core\Controllers\SupportController::class, 'aiChat']);

// Super Admin Routes
Router::get('/' . ADMIN_PATH, [\App\Core\Controllers\AdminController::class, 'index']);
Router::get('/' . ADMIN_PATH . '/users', [\App\Core\Controllers\AdminController::class, 'users']);
Router::get('/' . ADMIN_PATH . '/users/edit/{id}', [\App\Core\Controllers\AdminController::class, 'edit']);
Router::post('/' . ADMIN_PATH . '/users/update/{id}', [\App\Core\Controllers\AdminController::class, 'update']);
Router::post('/' . ADMIN_PATH . '/users/delete/{id}', [\App\Core\Controllers\AdminController::class, 'delete']);
Router::get('/' . ADMIN_PATH . '/users/impersonate/{id}', [\App\Core\Controllers\AdminController::class, 'impersonate']);
Router::post('/' . ADMIN_PATH . '/users/unlock-pin/{id}', [\App\Core\Controllers\AdminController::class, 'unlockUserPin']);
Router::post('/' . ADMIN_PATH . '/users/reset-pin/{id}', [\App\Core\Controllers\AdminController::class, 'resetUserPin']);
Router::get('/' . ADMIN_PATH . '/settings', [\App\Core\Controllers\AdminController::class, 'settings']);
Router::post('/' . ADMIN_PATH . '/settings/update', [\App\Core\Controllers\AdminController::class, 'updateSettings']);
Router::get('/' . ADMIN_PATH . '/settings/integrations', [\App\Core\Controllers\AdminController::class, 'integrations']);
Router::post('/' . ADMIN_PATH . '/settings/integrations/update', [\App\Core\Controllers\AdminController::class, 'integrationsUpdate']);
Router::get('/' . ADMIN_PATH . '/broadcast', [\App\Core\Controllers\AdminController::class, 'broadcast']);
Router::post('/' . ADMIN_PATH . '/broadcast/send', [\App\Core\Controllers\AdminController::class, 'sendBroadcast']);

// Admin Deposits/Withdrawals/Cards/Stores/Invoices
Router::get('/' . ADMIN_PATH . '/deposits', [\App\Core\Controllers\AdminController::class, 'deposits']);
Router::post('/' . ADMIN_PATH . '/deposits/approve', [\App\Core\Controllers\AdminController::class, 'depositsApprove']);
Router::post('/' . ADMIN_PATH . '/deposits/reject', [\App\Core\Controllers\AdminController::class, 'depositsReject']);

Router::get('/' . ADMIN_PATH . '/withdrawals', [\App\Core\Controllers\AdminController::class, 'withdrawals']);
Router::post('/' . ADMIN_PATH . '/withdrawals/approve', [\App\Core\Controllers\AdminController::class, 'withdrawalsApprove']);
Router::post('/' . ADMIN_PATH . '/withdrawals/reject', [\App\Core\Controllers\AdminController::class, 'withdrawalsReject']);

Router::get('/' . ADMIN_PATH . '/cards', [\App\Core\Controllers\AdminController::class, 'cards']);
Router::post('/' . ADMIN_PATH . '/cards/approve', [\App\Core\Controllers\AdminController::class, 'cardsApprove']);
Router::post('/' . ADMIN_PATH . '/cards/reject', [\App\Core\Controllers\AdminController::class, 'cardsReject']);
Router::post('/' . ADMIN_PATH . '/cards/toggle', [\App\Core\Controllers\AdminController::class, 'cardsToggleStatus']);
Router::post('/' . ADMIN_PATH . '/cards/delete', [\App\Core\Controllers\AdminController::class, 'cardsDelete']);
Router::post('/' . ADMIN_PATH . '/cards/delete-all', [\App\Core\Controllers\AdminController::class, 'cardsDeleteAll']);

// Admin Naira Physical Cards Inventory
Router::get('/' . ADMIN_PATH . '/naira-cards-inventory', [\App\Core\Controllers\AdminController::class, 'nairaCardsInventory']);
Router::post('/' . ADMIN_PATH . '/naira-cards-inventory/add', [\App\Core\Controllers\AdminController::class, 'nairaCardsInventoryAdd']);
Router::post('/' . ADMIN_PATH . '/naira-cards-inventory/delete', [\App\Core\Controllers\AdminController::class, 'nairaCardsInventoryDelete']);

// Admin Migration Route (Temporary)
Router::get('/' . ADMIN_PATH . '/run-naira-migration', [\App\Core\Controllers\AdminController::class, 'runNairaMigration']);

Router::get('/' . ADMIN_PATH . '/stores', [\App\Core\Controllers\AdminController::class, 'stores']);
Router::post('/' . ADMIN_PATH . '/stores/approve', [\App\Core\Controllers\AdminController::class, 'storesApprove']);
Router::post('/' . ADMIN_PATH . '/stores/reject', [\App\Core\Controllers\AdminController::class, 'storesReject']);

Router::get('/' . ADMIN_PATH . '/invoices', [\App\Core\Controllers\AdminController::class, 'invoices']);
Router::get('/' . ADMIN_PATH . '/invoices/approve/{id}', [\App\Core\Controllers\AdminController::class, 'invoicesApprove']);
Router::get('/' . ADMIN_PATH . '/invoices/reject/{id}', [\App\Core\Controllers\AdminController::class, 'invoicesReject']);

// Admin KYC Routes
Router::get('/' . ADMIN_PATH . '/kyc', [\App\Core\Controllers\AdminController::class, 'kyc']);
Router::post('/' . ADMIN_PATH . '/kyc/approve', [\App\Core\Controllers\AdminController::class, 'kycApprove']);
Router::post('/' . ADMIN_PATH . '/kyc/reject', [\App\Core\Controllers\AdminController::class, 'kycReject']);

// Admin SOC Routes
Router::get('/' . ADMIN_PATH . '/soc', [\App\Core\Controllers\AdminController::class, 'soc']);
Router::post('/' . ADMIN_PATH . '/soc/block', [\App\Core\Controllers\AdminController::class, 'socBlock']);
Router::post('/' . ADMIN_PATH . '/soc/unblock', [\App\Core\Controllers\AdminController::class, 'socUnblock']);

// Admin Permissions Routes
Router::get('/' . ADMIN_PATH . '/permissions', [\App\Core\Controllers\AdminController::class, 'permissions']);
Router::get('/' . ADMIN_PATH . '/moderator-permissions', [\App\Core\Controllers\AdminController::class, 'permissions']);
Router::post('/' . ADMIN_PATH . '/permissions/update', [\App\Core\Controllers\AdminController::class, 'permissionsUpdate']);
Router::post('/' . ADMIN_PATH . '/moderator-permissions/update', [\App\Core\Controllers\AdminController::class, 'permissionsUpdate']);

// Admin Modules & Ads Routes
Router::get('/' . ADMIN_PATH . '/modules', [\App\Core\Controllers\AdminController::class, 'modules']);
Router::post('/' . ADMIN_PATH . '/modules/update', [\App\Core\Controllers\AdminController::class, 'modulesUpdate']);
Router::post('/' . ADMIN_PATH . '/modules/toggle', [\App\Core\Controllers\ModuleController::class, 'toggle']);
Router::get('/' . ADMIN_PATH . '/ads', [\App\Core\Controllers\AdminController::class, 'ads']);
Router::get('/' . ADMIN_PATH . '/shop/ads', [\App\Core\Controllers\AdminController::class, 'ads']);
Router::get('/' . ADMIN_PATH . '/shop/sliders', [\App\Core\Controllers\AdminController::class, 'sliders']);

// Email Templates
Router::get('/' . ADMIN_PATH . '/email-templates', [\App\Core\Controllers\AdminEmailTemplateController::class, 'index']);
Router::get('/' . ADMIN_PATH . '/email-templates/create', [\App\Core\Controllers\AdminEmailTemplateController::class, 'create']);
Router::post('/' . ADMIN_PATH . '/email-templates/store', [\App\Core\Controllers\AdminEmailTemplateController::class, 'store']);
Router::get('/' . ADMIN_PATH . '/email-templates/edit/{id}', [\App\Core\Controllers\AdminEmailTemplateController::class, 'edit']);
Router::post('/' . ADMIN_PATH . '/email-templates/update/{id}', [\App\Core\Controllers\AdminEmailTemplateController::class, 'update']);
Router::post('/' . ADMIN_PATH . '/email-templates/delete/{id}', [\App\Core\Controllers\AdminEmailTemplateController::class, 'delete']);

// Email Campaigns
Router::get('/' . ADMIN_PATH . '/email-campaigns', [\App\Core\Controllers\AdminEmailCampaignController::class, 'index']);
Router::get('/' . ADMIN_PATH . '/email-campaigns/create', [\App\Core\Controllers\AdminEmailCampaignController::class, 'create']);
Router::post('/' . ADMIN_PATH . '/email-campaigns/store', [\App\Core\Controllers\AdminEmailCampaignController::class, 'store']);
Router::get('/' . ADMIN_PATH . '/email-campaigns/edit/{id}', [\App\Core\Controllers\AdminEmailCampaignController::class, 'edit']);
Router::post('/' . ADMIN_PATH . '/email-campaigns/update/{id}', [\App\Core\Controllers\AdminEmailCampaignController::class, 'update']);
Router::post('/' . ADMIN_PATH . '/email-campaigns/delete/{id}', [\App\Core\Controllers\AdminEmailCampaignController::class, 'delete']);

// Email Settings
Router::get('/' . ADMIN_PATH . '/email-settings', [\App\Core\Controllers\AdminEmailSettingsController::class, 'index']);
Router::post('/' . ADMIN_PATH . '/email-settings/update', [\App\Core\Controllers\AdminEmailSettingsController::class, 'update']);
Router::post('/' . ADMIN_PATH . '/email-settings/test', [\App\Core\Controllers\AdminEmailSettingsController::class, 'test']);

// Coupons
Router::get('/' . ADMIN_PATH . '/coupons', [\App\Core\Controllers\AdminCouponController::class, 'index']);
Router::post('/' . ADMIN_PATH . '/coupons/store', [\App\Core\Controllers\AdminCouponController::class, 'store']);
Router::post('/' . ADMIN_PATH . '/coupons/delete', [\App\Core\Controllers\AdminCouponController::class, 'delete']);

// CMS & Pages
Router::get('/' . ADMIN_PATH . '/cms', [\App\Core\Controllers\AdminCmsController::class, 'index']);
Router::get('/' . ADMIN_PATH . '/cms/edit/{id}', [\App\Core\Controllers\AdminCmsController::class, 'editPage']);
Router::get('/' . ADMIN_PATH . '/cms/page/{id}', [\App\Core\Controllers\AdminCmsController::class, 'editPage']);
Router::get('/' . ADMIN_PATH . '/cms/page/edit/{id}', [\App\Core\Controllers\AdminCmsController::class, 'editPage']);
Router::post('/' . ADMIN_PATH . '/cms/update/{id}', [\App\Core\Controllers\AdminCmsController::class, 'updatePage']);
Router::post('/' . ADMIN_PATH . '/cms/page/update/{id}', [\App\Core\Controllers\AdminCmsController::class, 'updatePage']);
Router::get('/' . ADMIN_PATH . '/cms/delete/{id}', [\App\Core\Controllers\AdminCmsController::class, 'deletePage']);
Router::get('/' . ADMIN_PATH . '/cms/page/delete/{id}', [\App\Core\Controllers\AdminCmsController::class, 'deletePage']);
Router::post('/' . ADMIN_PATH . '/cms/delete/{id}', [\App\Core\Controllers\AdminCmsController::class, 'deletePage']);
Router::post('/' . ADMIN_PATH . '/cms/page/delete/{id}', [\App\Core\Controllers\AdminCmsController::class, 'deletePage']);
Router::get('/' . ADMIN_PATH . '/cms/post/create', [\App\Core\Controllers\AdminCmsController::class, 'createPost']);
Router::post('/' . ADMIN_PATH . '/cms/post/store', [\App\Core\Controllers\AdminCmsController::class, 'storePost']);
Router::get('/' . ADMIN_PATH . '/cms/post/edit/{id}', [\App\Core\Controllers\AdminCmsController::class, 'editPost']);
Router::post('/' . ADMIN_PATH . '/cms/post/update/{id}', [\App\Core\Controllers\AdminCmsController::class, 'updatePost']);
Router::get('/' . ADMIN_PATH . '/cms/post/delete/{id}', [\App\Core\Controllers\AdminCmsController::class, 'deletePost']);
Router::post('/' . ADMIN_PATH . '/cms/post/delete/{id}', [\App\Core\Controllers\AdminCmsController::class, 'deletePost']);
Router::post('/' . ADMIN_PATH . '/cms/post/ai-generate', [\App\Core\Controllers\AdminCmsController::class, 'generateAiContent']);

// AI Bot
Router::get('/' . ADMIN_PATH . '/bot', [\App\Core\Controllers\AdminBotController::class, 'index']);
Router::post('/' . ADMIN_PATH . '/bot/save', [\App\Core\Controllers\AdminBotController::class, 'save']);
Router::post('/' . ADMIN_PATH . '/bot/delete', [\App\Core\Controllers\AdminBotController::class, 'delete']);
Router::get('/' . ADMIN_PATH . '/bot-training', [\App\Core\Controllers\AdminBotController::class, 'index']);
Router::post('/' . ADMIN_PATH . '/bot-training/save', [\App\Core\Controllers\AdminBotController::class, 'save']);
Router::post('/' . ADMIN_PATH . '/bot-training/delete', [\App\Core\Controllers\AdminBotController::class, 'delete']);

// Admin Support Tickets
Router::get('/' . ADMIN_PATH . '/support', [\App\Core\Controllers\AdminSupportController::class, 'index']);
Router::get('/' . ADMIN_PATH . '/support/view/{id}', [\App\Core\Controllers\AdminSupportController::class, 'view']);
Router::post('/' . ADMIN_PATH . '/support/reply', [\App\Core\Controllers\AdminSupportController::class, 'reply']);
Router::post('/' . ADMIN_PATH . '/support/status', [\App\Core\Controllers\AdminSupportController::class, 'updateStatus']);
Router::post('/' . ADMIN_PATH . '/support/updateStatus', [\App\Core\Controllers\AdminSupportController::class, 'updateStatus']);

// Public CMS & Blog Routes (PageController)
Router::get('/blog', [\App\Core\Controllers\PageController::class, 'blogIndex']);
Router::get('/blog/{slug}', [\App\Core\Controllers\PageController::class, 'blogPost']);
Router::get('/about', [\App\Core\Controllers\PageController::class, 'about']);
Router::get('/about-us', [\App\Core\Controllers\PageController::class, 'about']);
Router::get('/privacy', [\App\Core\Controllers\PageController::class, 'privacy']);
Router::get('/privacy-policy', [\App\Core\Controllers\PageController::class, 'privacy']);
Router::get('/terms', [\App\Core\Controllers\PageController::class, 'terms']);
Router::get('/terms-of-service', [\App\Core\Controllers\PageController::class, 'terms']);
Router::get('/accessibility', [\App\Core\Controllers\PageController::class, 'accessibility']);
Router::get('/accessibility-statement', [\App\Core\Controllers\PageController::class, 'accessibility']);
Router::get('/contact', [\App\Core\Controllers\PageController::class, 'contact']);
Router::post('/contact/submit', [\App\Core\Controllers\PageController::class, 'submitContact']);
Router::get('/page/{slug}', [\App\Core\Controllers\PageController::class, 'page']);

// Knowledge Base Routes
Router::get('/knowledge-base', [\App\Core\Controllers\KnowledgeBaseController::class, 'index']);
Router::get('/knowledge-base/{slug}', [\App\Core\Controllers\KnowledgeBaseController::class, 'view']);

// Support Center Routes (User Side)
Router::get('/support/tickets', [\App\Core\Controllers\SupportController::class, 'index']);
Router::get('/support/create', [\App\Core\Controllers\SupportController::class, 'create']);
Router::post('/support/store', [\App\Core\Controllers\SupportController::class, 'store']);
Router::get('/support/ticket/{id}', [\App\Core\Controllers\SupportController::class, 'view']);
Router::post('/support/ticket/{id}/reply', [\App\Core\Controllers\SupportController::class, 'reply']);
Router::post('/support/ai-chat', [\App\Core\Controllers\SupportController::class, 'aiChat']);

// 2FA Routes
Router::get('/2fa/setup', [\App\Core\Controllers\TwoFactorController::class, 'setup']);
Router::post('/2fa/verify-setup', [\App\Core\Controllers\TwoFactorController::class, 'verifySetup']);
Router::get('/2fa/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifyLogin']);
Router::post('/2fa/verify', [\App\Core\Controllers\TwoFactorController::class, 'verifyLogin']);

// Notification Routes
Router::get('/notifications', [\App\Core\Controllers\NotificationController::class, 'index']);
Router::post('/notifications/read/{id}', [\App\Core\Controllers\NotificationController::class, 'markRead']);
Router::post('/notifications/read-all', [\App\Core\Controllers\NotificationController::class, 'markAllRead']);
Router::post('/notifications/mark-read', [\App\Core\Controllers\NotificationController::class, 'markRead']);
Router::post('/notifications/mark-all-read', [\App\Core\Controllers\NotificationController::class, 'markAllRead']);

// --- Unified Inbox ---
Router::get('/inbox', [\App\Core\Controllers\InboxController::class, 'index']);
Router::get('/inbox/conversation', [\App\Core\Controllers\InboxController::class, 'conversation']);
Router::post('/inbox/reply', [\App\Core\Controllers\InboxController::class, 'reply']);
Router::post('/inbox/close', [\App\Core\Controllers\InboxController::class, 'close']);
Router::get('/inbox/api/unread', [\App\Core\Controllers\InboxController::class, 'apiUnreadCount']);

// Business Builder Routes
Router::get('/business-builder', [\App\Core\Controllers\BusinessBuilderController::class, 'index']);
Router::post('/business-builder/chat', [\App\Core\Controllers\BusinessBuilderController::class, 'chat']);
Router::post('/business-builder/complete', [\App\Core\Controllers\BusinessBuilderController::class, 'complete']);
Router::post('/business-builder/process', [\App\Core\Controllers\BusinessBuilderController::class, 'processWizard']);

