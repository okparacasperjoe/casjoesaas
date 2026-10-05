<?php

// Deployment script for Casjoe SAAS
// Usage: php deploy.php [optional_file_to_upload]

$localPath = __DIR__;
$targetFile = $argv[1] ?? null;

echo "========================================\n";
echo "   Casjoe Production Deployment Tool    \n";
echo "========================================\n\n";

// 1. Configuration - recursive scan
function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            // Filter files
            $relPath = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $path);
            $relPath = str_replace('\\', '/', $relPath); // Standardize to forward slashes for FTP usage
            
            // Exclude git, vendor, artifacts, temp files
            if (strpos($relPath, '.git') === 0) continue;
            if (strpos($relPath, 'vendor') === 0) continue;
            if (strpos($relPath, 'mobile') === 0) continue;
            if (strpos($relPath, 'node_modules') === 0) continue;
            if (strpos($relPath, 'deploy.php') !== false) continue;
            if (strpos($relPath, 'test_') === 0) continue;
            if (strpos($relPath, '.') === 0 && $relPath !== '.htaccess') continue; // Exclude hidden files except .htaccess

            $results[] = $relPath;
        } else if ($value != "." && $value != "..") {
            // Exclude git, brain, mobile, and vendor dirs
            if ($value === '.git' || $value === '.gemini' || $value === 'vendor' || $value === 'mobile' || $value === 'node_modules') continue; 
            
            getDirContents($path, $results);
        }
    }
    return $results;
}

echo "Scanning local files...\n";
$allFiles = getDirContents(getcwd());
$filesToUpload = [];

// Filter for only specific modified files
$modifiedFiles = [
    # Dashboard Branding Updates
    'app/Core/Views/global_dashboard.php',
    'app/Core/Views/admin/dashboard.php',
    'app/Modules/CasjoeCloud/Views/dashboard.php',
    'app/Modules/CasjoePay/Views/dashboard.php',
    'app/Modules/CasjoeERP/Views/dashboard.php',
    'app/Modules/CasjoeMail/Views/dashboard.php',
    'app/Modules/CasjoeLinks/Views/dashboard.php',
    'app/Modules/CasjoeAcademy/Views/business/dashboard.php',
    'app/Modules/CasjoeAcademy/Views/instructor/dashboard.php',
    'app/Modules/CasjoeAcademy/Views/learner/dashboard.php',
    'app/Modules/CasjoeAcademy/Views/student_dashboard.php',
    'app/Modules/CasjoeERP/Views/layout/header.php',
    'app/Modules/CasjoeShop/Views/layout/store_header.php',
    'app/Modules/CasjoeSmartForms/Views/layout/header.php',
    'app/Modules/CasjoeERP/Views/ai-manager/dashboard.php',
    'app/Modules/CasjoeERP/Views/client/dashboard.php',
    'app/Modules/CasjoeERP/Views/crm/dashboard.php',
    'app/Modules/CasjoeERP/Views/finance/dashboard.php',
    'app/Modules/CasjoeERP/Views/hr/self_service/dashboard.php',
    'app/Modules/CasjoeERP/Views/hr/index.php',
    
    # CMS Editor Upgrade
    'app/Core/Views/admin/cms/create_post.php',
    'app/Core/Views/admin/cms/edit_post.php',
    'app/Core/Views/admin/cms/edit_page.php',

    # CRM Form Integration
    'app/Modules/CasjoeERP/Controllers/WebhookController.php',
    'app/Modules/CasjoeERP/routes.php',
    'app/Modules/CasjoeERP/Controllers/IntegrationController.php',
    'app/Modules/CasjoeERP/Views/crm/integrations/embed.php',
    'app/Modules/CasjoeERP/Views/crm/integrations/index.php',
    'app/Modules/CasjoeERP/Views/crm/integrations/edit.php',

    # Chat Widget Lead Capture
    'app/Modules/CasjoeERP/Controllers/ChatController.php',
    'public/js/chat-widget.js',
    'app/Modules/CasjoeERP/routes.php',
    'app/Modules/CasjoeAcademy/seed_erp_course.php',
    'app/Views/landing.php',
    'app/Core/Views/global_footer.php',
    'app/Core/Views/admin/bot_training.php',
    'public/rebrand_db.php',
    'app/Core/AI/Providers/HuggingFaceProvider.php',
    'app/Modules/CasjoeERP/Controllers/ChatController.php',
    'public/train_cori_ai.php',
    'public/fetch_payment_info.php',
    'public/purge_rules.php',
    'public/inspect_library.php',
    'public/seed_ceo_books.php',
    'public/uploads/books/placeholder.pdf',
    'public/uploads/covers/good_to_great.png',
    'public/uploads/covers/hard_thing.png',
    'public/uploads/covers/zero_to_one.png',
    'public/uploads/covers/lean_startup.png',
    'public/uploads/covers/thinking_fast_slow.png',
    'public/uploads/covers/innovators_dilemma.png',
    'public/uploads/covers/principles.png',
    'public/uploads/covers/blue_ocean.png',
    'public/uploads/covers/start_with_why.png',
    'public/uploads/covers/measure_what_matters.png',
    'public/debug_tenants.php',
    'public/check_classora.php',
    'public/check_controller.php',
    'public/list_server.php',
    'public/find_controllers.php',
    'public/emergency_fix.php',
    'public/check_pdf.php',
    'app/Modules/CasjoeAcademy/Views/library/edit.php',
    'public/emergency_edit_fix.php',
    'public/debug_session.php',
    'public/check_tenant_user.php',
    
    # Invoice Automations
    'app/Modules/CasjoeERP/Controllers/FinanceController.php',
    'app/Modules/CasjoeERP/Views/layout/sidebar.php',
    'app/Modules/CasjoeERP/routes.php',
    'app/Modules/CasjoeERP/Views/finance/reminders.php',
    'app/Modules/CasjoeERP/Views/finance/create_reminder.php',
    'app/Modules/CasjoeERP/Views/finance/edit_reminder.php',
    'app/Modules/CasjoeERP/Views/finance/invoices.php',
    'app/Modules/CasjoeERP/Views/finance/create_invoice.php',
    'app/Modules/CasjoeERP/Views/finance/edit_invoice.php',
    'app/Modules/CasjoeERP/Views/finance/invoice_public.php',
    'app/Modules/CasjoeERP/Views/finance/edit_invoice.php',
    'cron_invoice_reminders.php',
    'app/Modules/CasjoeERP/migrate_invoice_reminders.php',
    'app/Modules/CasjoeERP/migrate_invoice_frequency.php',
    'public/test_flutterwave.php',
    'public/check_db_schema.php',
    'public/read_logs.php',
    
    # Google Sign-in Prompt Updates
    'app/Views/partials/google_prompt.php',
    'app/Views/landing.php',
    'app/Views/contact.php',
    'app/Views/page.php',
    'app/Views/blog/index.php',
    'app/Views/blog/single.php',
    
    # Shop Features
    'app/Modules/CasjoeShop/Views/store/index.php',
    'app/Modules/CasjoeShop/Controllers/ShopController.php',
    'public/migrate_shop_features.php',
    'public/migrate_shop_sliders.php',
    'app/Modules/CasjoeShop/Views/admin/sliders.php',
    'app/Core/Views/admin/sidebar.php',
    'app/Modules/CasjoeShop/routes.php',
    'app/Modules/CasjoeShop/Controllers/AdminController.php',

    # Casjoe Pay Card Live Fixes
    'app/Core/Controllers/AdminController.php',
    'app/Core/Views/admin/cards.php',
    'app/Modules/CasjoePay/Services/StroWalletService.php',
    'app/Modules/CasjoePay/Controllers/CardController.php',
    'app/Modules/CasjoePay/Controllers/PayController.php',
    'app/Modules/CasjoePay/Views/cards.php',
    'app/Modules/CasjoePay/Views/fund_card.php',
    'app/Modules/CasjoePay/Views/dashboard.php',
    'public/test_api.php',

    # AI Manager Advancement
    'app/Modules/CasjoeERP/Controllers/AIManagerController.php',
    'app/Modules/CasjoeERP/Views/ai-manager/dashboard.php',
    'app/Modules/CasjoeERP/routes.php',
    'app/Core/AI/ContextAggregator.php',

    # Attendance Report
    'app/Modules/CasjoeERP/Controllers/EmployeeController.php',
    'app/Modules/CasjoeERP/Views/hr/attendance/index.php',
    'app/Modules/CasjoeERP/Views/hr/attendance/report.php',

    # Multi-Location Inventory & Staff Management
    'app/Modules/CasjoeERP/Controllers/LocationController.php',
    'app/Modules/CasjoeERP/Controllers/InventoryController.php',
    'app/Modules/CasjoeERP/Views/locations/index.php',
    'app/Modules/CasjoeERP/Views/locations/create.php',
    'app/Modules/CasjoeERP/Views/locations/edit.php',
    'app/Modules/CasjoeERP/Views/inventory/index.php',
    'app/Modules/CasjoeERP/Views/inventory/create.php',
    'app/Modules/CasjoeERP/Views/inventory/edit.php',
    'app/Modules/CasjoeERP/routes.php',
    'app/Modules/CasjoeERP/Views/layout/sidebar.php',
    'app/Core/Auth.php',
    'public/migrate_locations.php',

    # Moniepoint POS Integration
    'app/Modules/CasjoeERP/Services/MoniepointService.php',
    'app/Modules/CasjoeERP/Controllers/MoniepointIntegrationController.php',
    'app/Modules/CasjoeERP/Views/moniepoint_settings.php',
    'app/Modules/CasjoeERP/Views/system/settings.php',
    'app/Modules/CasjoeERP/Views/layout/sidebar.php',
    'app/Modules/CasjoeERP/Views/finance.php',
    'app/Modules/CasjoeERP/routes.php',
];

foreach ($allFiles as $f) {
    // Normalize slashes
    $f = str_replace('\\', '/', $f);
    
    if ($targetFile) {
        if ($f === $targetFile) {
            $filesToUpload[] = $f;
        }
    } else {
        if (in_array($f, $modifiedFiles)) {
            $filesToUpload[] = $f;
        }
    }
}
echo "Found " . count($filesToUpload) . " files to upload.\n";


// Interactive Credentials
$ftpHost = 'ftp.casjoe.com';
$ftpUser = 'app@casjoe.com';
$ftpPass = 'app@casjoe.com';
$remotePath = '/';




// 2. Connect
echo "\nConnecting to $ftpHost...\n";
$conn = @ftp_connect($ftpHost);
if (!$conn) {
    echo "Direct connection to $ftpHost failed, falling back to ftp.gb.stackcp.com...\n";
    $conn = @ftp_connect('ftp.gb.stackcp.com');
}
if (!$conn) die("Could not connect to FTP host.\n");

if (!ftp_login($conn, $ftpUser, $ftpPass)) {
    die("Login failed. Check credentials.\n");
}
ftp_pasv($conn, true);
echo "Connected!\n";

// 3. Helper Function to Create Directories
function ftp_mksubdirs($ftpcon, $ftpbasedir, $ftpath) {
    @ftp_chdir($ftpcon, $ftpbasedir);
    $parts = explode('/', $ftpath);
    foreach ($parts as $part) {
        if (!$part) continue;
        if (!@ftp_chdir($ftpcon, $part)) {
            ftp_mkdir($ftpcon, $part);
            ftp_chdir($ftpcon, $part);
        }
    }
}

// 4. Upload Files
foreach ($filesToUpload as $file) {
    if (!file_exists($file)) {
        echo "[SKIP] Local file not found: $file\n";
        continue;
    }

    $remoteFile = $remotePath . $file;
    $remoteDir = dirname($remoteFile);

    // Create directory structure if needed
    ftp_mksubdirs($conn, '/', trim($remoteDir, '/'));
    
    // Go back to root (or wherever we need to be? mksubdirs moves us. Best to use absolute paths)
    // Actually, ftp_mksubdirs changes CWD. Let's just reset or use the absolute path in put.
    // simpler: valid absolute path for put works regardless of CWD usually? 
    // Wait, ftp_put takes remote_file. 
    
    echo "Uploading: $file -> $remoteFile ... ";
    if (ftp_put($conn, $remoteFile, $file, FTP_BINARY)) {
        echo "OK\n";
    } else {
        $error = error_get_last();
        echo "FAILED (" . ($error['message'] ?? 'Unknown Error') . ")\n";
    }
}

ftp_close($conn);

echo "\n----------------------------------------\n";
echo "Deployment Files Uploaded.\n";
echo "Now, please visit this URL in your browser to run the database migration:\n";
echo "https://YOUR_DOMAIN.com/production_referral_update.php\n";
echo "\nAfter migration, delete the migration script from the server.\n";
echo "Done.\n";
