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
                'name' => 'Casjoe BOS',
                'description' => 'Finance, HR, CRM, Inventory',
                'url' => '/erp',
                'icon' => 'planet',
                'image' => '/assets/casjoe_erp_logo.webp', // New Logo
                'color' => '#000066',
                'slug' => 'casjoe-bos',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Pay',
                'description' => 'Wallets, Cards, Transfers',
                'url' => '/pay',
                'icon' => 'card',
                'image' => '/assets/casjoe_pay_logo.webp', // New Logo
                'color' => '#FFA600',
                'slug' => 'casjoe-pay',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Business School',
                'description' => 'LMS & Course Management',
                'url' => '/academy',
                'icon' => 'school',
                'image' => '/assets/casjoe_academy_logo.webp', // New Logo
                'color' => '#2ed573',
                'slug' => 'casjoe-academy',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Mail',
                'description' => 'Email Marketing & Outreach',
                'url' => '/mail',
                'icon' => 'mail',
                'image' => '/assets/casjoe_mail_logo.webp',
                'color' => '#ff4757',
                'slug' => 'casjoe-mail',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Cloud',
                'description' => 'Secure File Storage & Sharing',
                'url' => '/cloud',
                'icon' => 'cloud-upload',
                'image' => '/assets/casjoe_cloud_logo.webp', // New Logo
                'color' => '#00a8ff',
                'slug' => 'casjoe-cloud',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Links',
                'description' => 'Bio Pages, URL Shortener & QR Codes',
                'url' => '/links',
                'icon' => 'link',
                'image' => '/assets/casjoe_links_logo.webp', // New Logo
                'color' => '#8e44ad',
                'slug' => 'casjoe-links',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Smart Forms',
                'description' => 'Drag-and-drop Forms & Payments',
                'url' => '/smart-forms',
                'icon' => 'document-text',
                'image' => '/assets/smart_forms_icon.webp',
                'color' => '#00b894',
                'slug' => 'casjoe-smart-forms',
                'status' => 'active'
            ],
            [
                'name' => 'Casjoe Mart',
                'description' => 'Multivendor Marketplace',
                'url' => '/shop',
                'icon' => 'storefront',
                'image' => '/assets/casjoe_shop_logo.webp', // Fallback or new logo
                'color' => '#e15f41',
                'slug' => 'casjoe-mart',
                'status' => 'active'
            ]
        ];

        // Fetch tenant module statuses from DB
        try {
            $stmtMod = $db->prepare("
                SELECT m.slug, tm.status 
                FROM tenant_modules tm
                JOIN modules m ON tm.module_id = m.id
                WHERE tm.tenant_id = ?
            ");
            $stmtMod->execute([$tenantId]);
            $modStatuses = $stmtMod->fetchAll(\PDO::FETCH_KEY_PAIR);

            foreach ($modules as &$modItem) {
                $slug = $modItem['slug'];
                $altSlug = ($slug === 'casjoe-bos') ? 'casjoe-erp' : (($slug === 'casjoe-mart') ? 'casjoe-shop' : $slug);

                if (isset($modStatuses[$slug])) {
                    $modItem['status'] = ($modStatuses[$slug] === 'enabled') ? 'active' : 'inactive';
                } elseif (isset($modStatuses[$altSlug])) {
                    $modItem['status'] = ($modStatuses[$altSlug] === 'enabled') ? 'active' : 'inactive';
                }
            }
            unset($modItem);

            // Sort active modules to the top of the line, inactive modules below
            usort($modules, function ($a, $b) {
                $aActive = ($a['status'] === 'active') ? 1 : 0;
                $bActive = ($b['status'] === 'active') ? 1 : 0;
                return $bActive <=> $aActive;
            });
        } catch (\Exception $e) {
            // Keep default active and order
        }

        // Get Notifications
        $notifications = \App\Core\Notification::getUnread($_SESSION['user_id']);

        // AI & DB Morning Briefing (Driven directly from Tenant Database telemetry)
        $empCount = 0;
        try {
            $stmtEmp = $db->prepare("SELECT COUNT(*) as cnt FROM erp_employees WHERE tenant_id = ? AND status = 'active'");
            $stmtEmp->execute([$tenantId]);
            $empCount = (int)($stmtEmp->fetch()['cnt'] ?? 0);
        } catch (\Exception $e) {}

        $custCount = 0;
        try {
            $stmtCust = $db->prepare("SELECT COUNT(*) as cnt FROM erp_customers WHERE tenant_id = ?");
            $stmtCust->execute([$tenantId]);
            $custCount = (int)($stmtCust->fetch()['cnt'] ?? 0);
        } catch (\Exception $e) {}

        $planName = 'Enterprise BOS';
        $tokensAvailable = 0;
        try {
            $stmtSub = $db->prepare("SELECT plan, ai_credits, ai_tokens_limit, ai_tokens_used FROM subscriptions WHERE tenant_id = ? LIMIT 1");
            $stmtSub->execute([$tenantId]);
            $subRow = $stmtSub->fetch();
            if ($subRow) {
                $planName = ucwords(str_replace('-', ' ', $subRow['plan'] ?? 'Enterprise BOS'));
                $tokensAvailable = max(0, ($subRow['ai_tokens_limit'] ?? 0) - ($subRow['ai_tokens_used'] ?? 0)) + ($subRow['ai_credits'] ?? 0);
            }
        } catch (\Exception $e) {}

        $latestInsightText = '';
        try {
            $stmtIns = $db->prepare("SELECT title, description FROM erp_ai_insights WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmtIns->execute([$tenantId]);
            $insRow = $stmtIns->fetch();
            if ($insRow && !empty($insRow['title'])) {
                $latestInsightText = " <br><br><strong>Latest Intelligence Alert:</strong> " . htmlspecialchars($insRow['title']) . " &mdash; " . htmlspecialchars($insRow['description']);
            }
        } catch (\Exception $e) {}

        $activeModsCount = count(array_filter($modules, function($m) { return ($m['status'] ?? '') === 'active'; }));

        // Dynamic Cori AI Briefing — rotates every page load with personality
        $hour = (int) date('H');
        $dayName = date('l');
        $userName = htmlspecialchars(\App\Core\Auth::user()['name'] ?? 'Boss');
        $firstName = explode(' ', $userName)[0];

        // Time-aware greetings
        if ($hour < 12) {
            $timeGreetings = [
                "Good morning, {$firstName}! ☀️",
                "Rise and grind, {$firstName}!",
                "Morning, CEO! Coffee first, domination second. ☕",
                "Top of the morning, {$firstName}! Let's get this bread. 🍞",
            ];
        } elseif ($hour < 17) {
            $timeGreetings = [
                "Good afternoon, {$firstName}! Still crushing it? 💪",
                "Afternoon check-in, {$firstName}. You're halfway to legendary.",
                "Hey {$firstName}, lunch break is over — back to empire building!",
                "Afternoon vibes, {$firstName}. Your business doesn't nap, neither should you. 😤",
            ];
        } else {
            $timeGreetings = [
                "Good evening, {$firstName}! Still at it? That's CEO energy. 🔥",
                "Evening, {$firstName}. Most people are watching Netflix. You're building an empire.",
                "Night owl mode activated, {$firstName}. Legends don't sleep. 🦉",
                "Working late, {$firstName}? Your competitors are definitely not. You're winning.",
            ];
        }

        // Day-specific roasts
        $dayRoasts = [
            'Monday' => "It's Monday — the day your excuses expire. Let's go! 🚀",
            'Tuesday' => "Tuesday: Monday's less dramatic sibling. Time to actually get things done.",
            'Wednesday' => "Midweek already! Halfway to the weekend, {$firstName}. No slowing down.",
            'Thursday' => "It's Thursday — close enough to Friday to smell the freedom. Push through! 💨",
            'Friday' => "Happy Friday, {$firstName}! But your business doesn't take weekends off. Neither does Cori. 😏",
            'Saturday' => "Working on a Saturday? That's either dedication or poor time management. Either way, I respect it. 🫡",
            'Sunday' => "Sunday hustle! While others rest, you strategize. That's the difference between a boss and an employee.",
        ];

        // Contextual roasts based on data
        $contextRoasts = [];
        if ($custCount == 0) {
            $contextRoasts[] = "Zero clients? 😬 Your CRM is lonelier than a Monday morning gym. Time to activate Casjoe Mail and start prospecting!";
            $contextRoasts[] = "No clients registered yet? Even my AI brain had users on day one. Let's fix this — launch your store or send some campaigns!";
            $contextRoasts[] = "Client count: 0. That's not a number, that's a cry for help. Let's get some customers rolling in! 📢";
        } elseif ($custCount < 10) {
            $contextRoasts[] = "Only {$custCount} clients? That's cute. Let's turn that into {$custCount}00 with some proper marketing. 🎯";
            $contextRoasts[] = "{$custCount} clients and counting. Not bad for a start, but Cori knows you can do better. Much better.";
        } else {
            $contextRoasts[] = "{$custCount} clients on the books! Now we're talking. Keep scaling and watch the magic happen. ✨";
            $contextRoasts[] = "Managing {$custCount} clients like a true boss. Your empire is growing and Cori approves. 👑";
        }

        if ($empCount == 0) {
            $contextRoasts[] = "No staff? So you're a one-person army. Impressive... or concerning. Hire someone before you burn out! 😅";
        } elseif ($empCount > 10) {
            $contextRoasts[] = "Leading a team of {$empCount}! That's real CEO energy. Make sure they're all pulling their weight — Cori is watching. 👀";
        }

        if ($tokensAvailable < 1000) {
            $contextRoasts[] = "⚠️ Running low on AI tokens ({$tokensAvailable} left). Upgrade before Cori goes silent — and trust me, you don't want that.";
        } elseif ($tokensAvailable > 30000) {
            $contextRoasts[] = number_format($tokensAvailable) . " AI tokens available. That's a lot of unused brain power. Put Cori to work!";
        }

        if ($activeModsCount < 3) {
            $contextRoasts[] = "Only {$activeModsCount} modules active? You're driving a Ferrari in first gear. Activate more modules and unlock your full potential!";
        }

        // Build the briefing
        $greeting = $timeGreetings[array_rand($timeGreetings)];
        $dayRoast = $dayRoasts[$dayName];
        $contextRoast = !empty($contextRoasts) ? $contextRoasts[array_rand($contextRoasts)] : '';

        // System stats line
        $statsLine = "<strong>Dashboard Pulse:</strong> {$activeModsCount} active modules • {$empCount} staff • {$custCount} clients • {$planName} plan • " . number_format($tokensAvailable) . " Cori tokens";

        $aiBriefing = "<p style='margin: 0 0 8px 0;'>{$greeting}</p>" .
                      "<p style='margin: 0 0 8px 0;'>{$dayRoast}</p>" .
                      (!empty($contextRoast) ? "<p style='margin: 0 0 8px 0;'>{$contextRoast}</p>" : '') .
                      "<p style='margin: 0; font-size: 0.85rem; opacity: 0.85;'>{$statsLine}</p>" .
                      (!empty($latestInsightText) ? $latestInsightText : '');

        $tenantData = [];
        try {
            $stmtTenant = $db->prepare("SELECT name, logo, country, currency FROM tenants WHERE id = ?");
            $stmtTenant->execute([$tenantId]);
            $tenantData = $stmtTenant->fetch() ?: [];
        } catch (\Exception $e) {}

        if (!isset($_SESSION['cori_login_session_id'])) {
            $_SESSION['cori_login_session_id'] = bin2hex(random_bytes(8));
        }
        $briefingSessionId = $_SESSION['cori_login_session_id'];

        require __DIR__ . '/../Views/global_dashboard.php';
    }
}
