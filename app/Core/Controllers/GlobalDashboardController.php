<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\TenantContext;

class GlobalDashboardController
{
    public function index()
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();


        // Check Login
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Fetch enabled modules from DB
        /*
        $stmt = $db->prepare("
            SELECT m.name, m.slug, m.description 
            FROM tenant_modules tm
            JOIN modules m ON tm.module_id = m.id
            WHERE tm.tenant_id = ? AND tm.status = 'enabled'
        ");
        $stmt->execute([$tenantId]);
        $dbModules = $stmt->fetchAll();
        */
        // Keeping static array for now but with proper links as requested
        $modules = [
            [
                'name' => 'Casjoe ERP',
                'description' => 'Finance, HR, CRM, Inventory',
                'url' => '/erp',
                'icon' => 'planet',
                'color' => '#000066'
            ],
            [
                'name' => 'Casjoe Pay',
                'description' => 'Wallets, Cards, Transfers',
                'url' => '/pay',
                'icon' => 'card',
                'color' => '#FFA600'
            ],
            [
                'name' => 'Casjoe Academy',
                'description' => 'LMS & Course Management',
                'url' => '/academy',
                'icon' => 'school',
                'color' => '#2ed573'
            ],
            [
                'name' => 'Casjoe Mail',
                'description' => 'Email Marketing & Outreach',
                'url' => '/mail',
                'icon' => 'mail',
                'image' => '/assets/casjoe_mail_logo.png',
                'color' => '#ff4757'
            ],

        ];

        // Get Notifications
        $notifications = \App\Core\Notification::getUnread($_SESSION['user_id']);

        require __DIR__ . '/../Views/global_dashboard.php';
    }
}
