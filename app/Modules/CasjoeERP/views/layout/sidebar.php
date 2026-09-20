<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<?php
$sidebarLogoUrl = '/assets/casjoe_logo.png';
if (class_exists('\App\Core\Database') && class_exists('\App\Core\TenantContext')) {
    try {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $tid = \App\Core\TenantContext::getTenantId();
        if ($tid) {
            $stmt = $pdo->prepare("SELECT logo FROM tenants WHERE id = ?");
            $stmt->execute([$tid]);
            $tenantLogo = $stmt->fetchColumn();
            if (!empty($tenantLogo)) {
                $sidebarLogoUrl = $tenantLogo;
            }
        }
    } catch (\Exception $e) {}
}
?>
<aside class="sidebar" style="overflow-y: auto;">
    <?php include __DIR__ . '/sidebar_erp_css.php'; ?>

    <div class="erp-brand">
        <a href="/erp" style="text-decoration: none; display: flex; align-items: center; justify-content: center; width: 100%;">
            <img src="<?= htmlspecialchars($sidebarLogoUrl) ?>" alt="Casjoe Apps" style="max-height: 40px; max-width: 200px; object-fit: contain;">
        </a>
    </div>
    
    <style>
        .sidebar { width: 270px !important; min-width: 270px !important; background: rgba(3, 4, 20, 0.95) !important; backdrop-filter: blur(30px) !important; -webkit-backdrop-filter: blur(30px) !important; border-right: 1px solid rgba(255, 166, 0, 0.25) !important; box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important; position: fixed !important; top: 0 !important; bottom: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; transition: left 0.3s ease !important; display: flex !important; flex-direction: column !important; }
        
        @media (max-width: 991px) {
            .sidebar { left: -270px !important; }
            .sidebar.active { left: 0 !important; }
            .main-content, .cori-container, .erp-main, .main { margin-left: 0 !important; padding: 15px !important; padding-top: 80px !important; width: 100% !important; max-width: 100vw !important; box-sizing: border-box !important; }
            .top-bar { flex-direction: column; align-items: flex-start; gap: 10px; margin-top: 20px; }
            .top-bar .btn { width: 100%; text-align: center; }
            dialog { width: 95% !important; max-width: 95% !important; padding: 15px !important; }
            .card { padding: 15px !important; overflow-x: hidden; }
        }
        .brand { padding: 24px 0 10px; }
        .nav-header { color: #ffffff !important; font-weight: 600; padding: 10px 20px; text-transform: none; font-size: 0.95rem; letter-spacing: 0.5px; opacity: 0.7; }
        
        .erp-menu { padding-right: 12px; list-style: none; margin: 0; padding-left: 0; }
        
        .erp-link { 
            display: flex;
            align-items: center;
            justify-content: space-between; 
            cursor: pointer; 
            color: #ffa600; 
            font-weight: 600; 
            transition: all 0.2s; 
            border: none;
            border-radius: 12px;
            margin: 4px 10px 4px 16px;
            padding: 12px 16px;
            text-decoration: none;
        }
        .erp-link:hover { background: rgba(255, 166, 0, 0.08); text-decoration: none; }
        .sidebar .erp-link ion-icon, .sidebar .erp-link i { 
            color: #FFA600 !important; 
            font-size: 1.35rem !important; 
            margin-right: 10px !important;
            filter: drop-shadow(0 0 5px rgba(255, 166, 0, 0.6)) !important;
        }
        .sidebar .erp-link ion-icon.arrow { 
            margin-right: 0 !important; 
            font-size: 1.1rem !important; 
            transition: transform 0.2s !important; 
        }
        .sidebar .erp-link.active-parent ion-icon.arrow { 
            transform: rotate(180deg) !important; 
            color: #d84315 !important; 
        }
        
        /* Active Single Item */
        .sidebar .erp-link.active { background: rgba(255,166,0,0.15); color: #ffa600; font-weight: 700; }
        
        /* Active Parent */
        .sidebar .erp-link.active-parent {
            background: rgba(3, 4, 20, 0.8) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 16px 16px 0 0 !important;
            border: 1px solid rgba(255, 166, 0, 0.25) !important;
            border-bottom: none !important;
            margin-bottom: 0 !important;
        }
        
        /* Submenu container */
        .submenu { 
            display: none; 
            background: rgba(3, 4, 20, 0.4) !important;
            list-style: none;
            margin: 0 10px 8px 16px !important; 
            padding: 8px 10px 12px !important;
            border-radius: 0 0 16px 16px !important;
            border: 1px solid rgba(255, 166, 0, 0.25) !important;
            border-top: none !important;
        }
        .submenu.open { display: block; }
        
        /* Submenu items */
        .submenu .erp-link { 
            font-size: 0.92rem !important; 
            padding: 10px 14px 10px 32px !important; 
            color: rgba(255, 255, 255, 0.8) !important; 
            font-weight: 500 !important; 
            margin: 2px 0 !important;
            border-radius: 10px !important;
            position: relative !important;
            background: transparent !important;
            border: none !important;
        }
        .submenu .erp-link::before {
            content: '' !important;
            position: absolute !important;
            left: 14px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 5px !important;
            height: 5px !important;
            border: 1.5px solid rgba(255, 255, 255, 0.5) !important;
            border-radius: 50% !important;
        }
        .submenu .erp-link ion-icon { display: none !important; }
        .submenu .erp-link:hover { color: #FFA600 !important; background: rgba(255, 166, 0, 0.1) !important; }
        .submenu .erp-link:hover::before { border-color: #FFA600 !important; }
        
        .submenu .erp-link.active { 
            color: #FFA600 !important; 
            font-weight: 700 !important; 
            background: rgba(255, 166, 0, 0.2) !important; 
        }
        .submenu .erp-link.active::before { border-color: #FFA600 !important; background: #FFA600 !important; }
        
        /* =========================================================
           GLOBAL EXECUTIVE CLEAN WHITE / LIGHT ENTERPRISE UI
           ========================================================= */
        body {
            background: #ffffff !important;
            color: #1e293b !important;
            margin: 0;
            padding: 0;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 270px !important;
            }
        }
        .main-content {
            flex: 1;
            padding: 35px 40px !important;
            background: #ffffff !important;
            color: #1e293b !important;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* Executive Cards */
        .main-content .card, .card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
            border-radius: 16px !important;
            padding: 24px !important;
            color: #1e293b !important;
            margin-bottom: 24px;
        }
        .main-content .card h1, .main-content .card h2, .main-content .card h3, .main-content .card h4, .main-content .card h5, .main-content .card label,
        .card h1, .card h2, .card h3, .card h4, .card h5, .card label {
            color: #000066 !important;
            font-weight: 700 !important;
        }
        .main-content h1, .main-content h2, .main-content h3, .top-bar h1, .top-bar h2, h1, h2, h3, h4, h5 {
            color: #000066 !important;
            font-weight: 800 !important;
        }

        /* Crisp Tables */
        .main-content table, table {
            width: 100%;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            color: #334155 !important;
            background: #ffffff !important;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0 !important;
            margin-top: 10px;
        }
        .main-content table th, table th {
            background: #ffffff !important;
            color: #000066 !important;
            font-weight: 700 !important;
            font-size: 0.88rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 14px 16px !important;
            text-align: left;
        }
        .main-content table td, table td {
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 14px 16px !important;
            color: #334155 !important;
            font-size: 0.95rem !important;
        }
        .main-content table tr:hover td, table tr:hover td {
            background: #f8fafc !important;
        }

        /* Form Inputs */
        .main-content input.form-control, .main-content textarea.form-control, .main-content select.form-control, .main-content select,
        input.form-control, textarea.form-control, select.form-control, select {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #1e293b !important;
            border-radius: 10px !important;
            padding: 10px 14px !important;
            box-shadow: none !important;
            height: auto;
        }
        .main-content input.form-control:focus, .main-content textarea.form-control:focus, .main-content select:focus,
        input.form-control:focus, textarea.form-control:focus, select:focus {
            outline: none !important;
            border-color: #000066 !important;
            box-shadow: 0 0 0 3px rgba(0, 0, 102, 0.1) !important;
            background: #ffffff !important;
            color: #0f172a !important;
        }
        select option {
            background: #ffffff !important;
            color: #1e293b !important;
        }

        /* Modals & Dialogs */
        dialog {
            background: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
            padding: 28px !important;
        }
        dialog h1, dialog h2, dialog h3, dialog label {
            color: #0f172a !important;
        }

        /* Top Bar & Buttons */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .btn {
            background: #000066;
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn:hover {
            background: #000044;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 102, 0.2);
            color: #ffffff !important;
        }
        .btn-sm {
            padding: 6px 14px;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        /* ── DARK THEME OVERRIDES ── */
        html.dark-theme .main-content {
            background: #0f172a !important;
            color: #f1f5f9 !important;
        }
        html.dark-theme .main-content .card, html.dark-theme .card {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2) !important;
        }
        html.dark-theme .main-content h1, html.dark-theme .main-content h2, html.dark-theme .main-content h3, 
        html.dark-theme .main-content h4, html.dark-theme .main-content h5, html.dark-theme .main-content label, 
        html.dark-theme .top-bar h1, html.dark-theme .top-bar h2, html.dark-theme .card h1, html.dark-theme .card h2, 
        html.dark-theme .card h3, html.dark-theme .card h4, html.dark-theme .card h5, html.dark-theme .card label {
            color: #ffffff !important;
        }
        html.dark-theme .main-content table, html.dark-theme table {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html.dark-theme .main-content table th, html.dark-theme table th {
            background: #1e293b !important;
            color: #FFA600 !important;
            border-bottom-color: #334155 !important;
        }
        html.dark-theme .main-content table td, html.dark-theme table td {
            background: transparent !important;
            border-bottom-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html.dark-theme .main-content table tr:hover td, html.dark-theme table tr:hover td {
            background: #334155 !important;
        }
        html.dark-theme .main-content input.form-control, html.dark-theme .main-content textarea.form-control, 
        html.dark-theme .main-content select.form-control, html.dark-theme .main-content select,
        html.dark-theme input.form-control, html.dark-theme textarea.form-control, html.dark-theme select.form-control, html.dark-theme select {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        html.dark-theme .main-content input.form-control:focus, html.dark-theme .main-content textarea.form-control:focus, 
        html.dark-theme .main-content select:focus, html.dark-theme input.form-control:focus, 
        html.dark-theme textarea.form-control:focus, html.dark-theme select:focus {
            border-color: #FFA600 !important;
            box-shadow: 0 0 0 3px rgba(255,166,0,0.1) !important;
        }
        html.dark-theme dialog {
            background: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        html.dark-theme dialog h1, html.dark-theme dialog h2, html.dark-theme dialog h3, html.dark-theme dialog label {
            color: #ffffff !important;
        }
        html.dark-theme .top-bar {
            border-bottom-color: #334155 !important;
        }
        html.dark-theme .btn {
            background: #FFA600 !important;
            color: #000 !important;
        }
        html.dark-theme .btn:hover {
            background: #e69500 !important;
            box-shadow: 0 4px 12px rgba(255,166,0,0.3) !important;
        }

        /* ── MOBILE OVERRIDES (MUST BE AT END OF STYLE BLOCK) ── */
        @media (max-width: 991px) {
            .sidebar { 
                position: fixed !important;
                left: -280px !important; 
                width: 270px !important;
                height: 100vh !important;
                z-index: 10000 !important;
                top: 0 !important;
                bottom: 0 !important;
                transition: left 0.3s ease !important;
            }
            .sidebar.active { 
                left: 0 !important; 
                display: flex !important;
            }
            .main-content, .cori-container, .erp-main, .main { 
                margin-left: 0 !important; 
                padding: 15px !important; 
                padding-top: 85px !important; 
                padding-bottom: 90px !important;
                width: 100% !important; 
                max-width: 100vw !important; 
                box-sizing: border-box !important; 
            }
            .top-bar { flex-direction: column; align-items: flex-start; gap: 10px; margin-top: 20px; }
            .top-bar .btn { width: 100%; text-align: center; }
            dialog { width: 95% !important; max-width: 95% !important; padding: 15px !important; }
            .card { padding: 15px !important; overflow-x: hidden; }
        }
    </style>

    <ul class="erp-menu">
        <li class="nav-header">Main</li>
        <li class="erp-item">
            <a href="/dashboard" class="erp-link">
                <span><ion-icon name="grid-outline"></ion-icon> Dashboard</span>
            </a>
        </li>
        <li class="erp-item">
            <a href="/inbox" class="erp-link <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/inbox') !== false ? 'active' : '' ?>">
                <span><ion-icon name="chatbubbles-outline"></ion-icon> Unified Inbox</span>
            </a>
        </li>
        <li class="erp-item">
            <a href="/ai-office" class="erp-link <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/ai-office') !== false ? 'active' : '' ?>">
                <span><ion-icon name="briefcase"></ion-icon> AI Office</span>
            </a>
        </li>
        <li class="erp-item">
            <a href="/erp" class="erp-link <?= ($_SERVER['REQUEST_URI'] ?? '') == '/erp' ? 'active' : '' ?>">
                <span><ion-icon name="briefcase-outline"></ion-icon> ERP Overview</span>
            </a>
        </li>
        <li class="erp-item">
            <a href="/erp/goals" class="erp-link <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/erp/goals') !== false ? 'active' : '' ?>">
                <span><ion-icon name="trophy-outline"></ion-icon> Goals & OKRs</span>
            </a>
        </li>
        <li class="erp-item">
            <a href="/erp/ai-manager" class="erp-link <?= strpos($_SERVER['REQUEST_URI'] ?? '', '/erp/ai-manager') !== false ? 'active' : '' ?>">
                <span><ion-icon name="sparkles"></ion-icon> AI Manager</span>
            </a>
        </li>

        <?php
        $uri = $_SERVER['REQUEST_URI'] ?? '/erp';
        
        // HR Paths
        $hr_active = strpos($uri, '/erp/hr') !== false || 
                     strpos($uri, '/erp/employees') !== false || 
                     strpos($uri, '/erp/departments') !== false ||
                     strpos($uri, '/erp/attendance') !== false ||
                     strpos($uri, '/erp/leave') !== false ||
                     strpos($uri, '/erp/payroll') !== false ||
                     strpos($uri, '/erp/performance') !== false ||
                     strpos($uri, '/erp/recruitment') !== false ||
                     strpos($uri, '/erp/training') !== false ||
                     strpos($uri, '/erp/lifecycle') !== false ||
                     strpos($uri, 'promotion') !== false ||
                     strpos($uri, 'resignation') !== false ||
                     strpos($uri, 'termination') !== false ||
                     strpos($uri, '/erp/sops') !== false ||
                     strpos($uri, '/erp/locations') !== false;

        // CRM Paths
        $crm_active = strpos($uri, '/erp/crm') !== false || 
                      strpos($uri, '/erp/leads') !== false ||
                      strpos($uri, '/erp/opportunities') !== false ||
                      strpos($uri, '/erp/sales') !== false ||
                      strpos($uri, '/erp/scheduler') !== false ||
                      strpos($uri, '/erp/whatsapp') !== false ||
                      strpos($uri, '/erp/crm/integrations') !== false;

        // Project Paths
        $proj_active = strpos($uri, '/erp/projects') !== false || 
                       strpos($uri, '/erp/tasks') !== false ||
                       strpos($uri, '/erp/calendar') !== false;

        // Finance Paths
        $fin_active = strpos($uri, '/erp/finance') !== false || 
                      strpos($uri, '/erp/inventory') !== false ||
                      strpos($uri, '/erp/assets') !== false ||
                      strpos($uri, '/erp/transactions') !== false ||
                      strpos($uri, '/erp/wallet') !== false;

        // System Paths
        $sys_active = strpos($uri, '/erp/settings') !== false || 
                      strpos($uri, '/erp/users') !== false ||
                      strpos($uri, '/erp/chat') !== false ||
                      strpos($uri, '/erp/announcements') !== false ||
                      strpos($uri, '/erp/activity') !== false ||
                      strpos($uri, '/erp/roles') !== false ||
                      strpos($uri, '/erp/permissions') !== false;
        
        $user = \App\Core\Auth::user();
        $role = $user['role'] ?? '';
        $isAdmin = ($role === 'admin');
        $isHr = ($role === 'hr' || $isAdmin);

        // Decode accessible_modules if present
        $userModules = [];
        if (!empty($user['accessible_modules'])) {
            $decoded = is_string($user['accessible_modules']) ? json_decode($user['accessible_modules'], true) : $user['accessible_modules'];
            if (is_array($decoded)) {
                $userModules = $decoded;
            } elseif (is_string($user['accessible_modules'])) {
                $userModules = explode(',', $user['accessible_modules']);
            }
        }

        // Granular permission check helper
        $hasModule = function($modKey) use ($isAdmin, $role, $userModules) {
            if ($isAdmin) return true; // Admins always get full access to all modules
            if (!empty($userModules)) {
                return in_array($modKey, $userModules);
            }
            // Fallback legacy access if no modules explicitly assigned yet:
            if ($role === 'hr' && in_array($modKey, ['hr', 'projects', 'crm'])) return true;
            if ($role === 'user' && in_array($modKey, ['projects'])) return true;
            return false;
        };
        ?>

        <?php if ($hasModule('hr')): ?>
        <!-- Human Resources -->
        <li class="erp-item">
            <div class="erp-link <?= $hr_active ? 'active-parent' : '' ?>" onclick="toggleMenu('hr-menu')">
                <span><ion-icon name="people-outline"></ion-icon> Human Resources</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $hr_active ? 'open' : '' ?>" id="hr-menu">
                <li class="erp-item"><a href="/erp/departments" class="erp-link <?= strpos($uri, '/erp/departments') !== false ? 'active' : '' ?>">Departments</a></li>
                <li class="erp-item"><a href="/erp/hr" class="erp-link <?= (strpos($uri, '/erp/hr') !== false || strpos($uri, '/erp/employees') !== false)  ? 'active' : '' ?>">Employees</a></li>
                <li class="erp-item"><a href="/erp/attendance" class="erp-link <?= strpos($uri, '/erp/attendance') !== false ? 'active' : '' ?>">Attendance</a></li>
                <li class="erp-item"><a href="/erp/leave" class="erp-link <?= strpos($uri, '/erp/leave') !== false ? 'active' : '' ?>">Leave Mgmt</a></li>
                <li class="erp-item"><a href="/erp/payroll" class="erp-link <?= strpos($uri, '/erp/payroll') !== false ? 'active' : '' ?>">Payroll</a></li>
                <li class="erp-item"><a href="/erp/performance" class="erp-link <?= strpos($uri, '/erp/performance') !== false ? 'active' : '' ?>">Performance</a></li>
                <li class="erp-item"><a href="/erp/recruitment" class="erp-link <?= strpos($uri, '/erp/recruitment') !== false ? 'active' : '' ?>">Recruitment</a></li>
                <li class="erp-item"><a href="/erp/training" class="erp-link <?= strpos($uri, '/erp/training') !== false ? 'active' : '' ?>">Training</a></li>
                <li class="erp-item"><a href="/erp/lifecycle/promotion" class="erp-link <?= strpos($uri, 'promotion') !== false ? 'active' : '' ?>">Promotion</a></li>
                <li class="erp-item"><a href="/erp/lifecycle/resignation" class="erp-link <?= strpos($uri, 'resignation') !== false ? 'active' : '' ?>">Resignation</a></li>
                <li class="erp-item"><a href="/erp/lifecycle/termination" class="erp-link <?= strpos($uri, 'termination') !== false ? 'active' : '' ?>">Termination</a></li>
                <li class="erp-item"><a href="/erp/sops" class="erp-link <?= strpos($uri, '/erp/sops') !== false ? 'active' : '' ?>">SOPs & Policies</a></li>
                <li class="erp-item"><a href="/erp/locations" class="erp-link <?= strpos($uri, '/erp/locations') !== false ? 'active' : '' ?>">Office Locations</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if ($hasModule('crm')): ?>
        <!-- CRM & Sales -->
        <li class="erp-item">
            <div class="erp-link <?= $crm_active ? 'active-parent' : '' ?>" onclick="toggleMenu('crm-menu')">
                <span><ion-icon name="people-circle-outline"></ion-icon> CRM & Sales</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $crm_active ? 'open' : '' ?>" id="crm-menu">
                <li class="erp-item"><a href="/erp/crm/pipeline" class="erp-link <?= strpos($uri, '/erp/crm/pipeline') !== false ? 'active' : '' ?>">Pipeline</a></li>
                <li class="erp-item"><a href="/erp/crm" class="erp-link <?= $uri == '/erp/crm' ? 'active' : '' ?>">Clients</a></li>
                <li class="erp-item"><a href="/erp/crm/leads" class="erp-link <?= strpos($uri, '/erp/leads') !== false ? 'active' : '' ?>">Leads</a></li>
                <li class="erp-item"><a href="/erp/crm/opportunities" class="erp-link <?= strpos($uri, '/erp/opportunities') !== false ? 'active' : '' ?>">Opportunities</a></li>
                <li class="erp-item"><a href="/erp/crm/sales" class="erp-link <?= strpos($uri, '/erp/sales') !== false ? 'active' : '' ?>">Sales</a></li>
                <li class="erp-item"><a href="/erp/scheduler" class="erp-link <?= strpos($uri, '/erp/scheduler') !== false ? 'active' : '' ?>">Booking Scheduler</a></li>
                <li class="erp-item"><a href="/erp/whatsapp" class="erp-link <?= strpos($uri, '/erp/whatsapp') !== false ? 'active' : '' ?>">WhatsApp Marketing</a></li>
                <li class="erp-item"><a href="/erp/crm/workflows" class="erp-link <?= strpos($uri, '/erp/crm/workflows') !== false ? 'active' : '' ?>">⚡ Workflows</a></li>
                <li class="erp-item"><a href="/erp/crm/integrations" class="erp-link <?= strpos($uri, '/erp/crm/integrations') !== false ? 'active' : '' ?>">Integrations</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- E-Sign & Documents -->
        <li class="erp-item">
            <a href="/erp/documents" class="erp-link <?= (strpos($uri, '/erp/documents') !== false) ? 'active' : '' ?>">
                <span><ion-icon name="document-text-outline"></ion-icon> E-Sign & Documents</span>
            </a>
        </li>

        <?php if ($hasModule('projects')): ?>
        <!-- Projects -->
        <li class="erp-item">
            <div class="erp-link <?= $proj_active ? 'active-parent' : '' ?>" onclick="toggleMenu('project-menu')">
                <span><ion-icon name="folder-outline"></ion-icon> Projects</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $proj_active ? 'open' : '' ?>" id="project-menu">
                <li class="erp-item"><a href="/erp/projects" class="erp-link <?= $uri == '/erp/projects' ? 'active' : '' ?>">Projects</a></li>
                <li class="erp-item"><a href="/erp/tasks" class="erp-link <?= strpos($uri, '/erp/tasks') !== false ? 'active' : '' ?>">Tasks/Board</a></li>
                <li class="erp-item"><a href="/erp/projects/timesheets" class="erp-link <?= strpos($uri, 'timesheets') !== false ? 'active' : '' ?>">Timesheets</a></li>
                <li class="erp-item"><a href="/erp/calendar" class="erp-link <?= strpos($uri, 'calendar') !== false ? 'active' : '' ?>">Calendar</a></li>
            </ul>
        </li>
        <?php endif; ?>
        
        <li class="erp-item">
            <a href="/erp/my-portal" class="erp-link <?= strpos($uri, 'my-portal') !== false ? 'active-parent' : '' ?>">
                <span><ion-icon name="person-circle-outline"></ion-icon> My Portal</span>
            </a>
        </li>

        <?php if ($hasModule('finance')): ?>
        <!-- Assets & Finance -->
        <li class="erp-item">
            <div class="erp-link <?= $fin_active ? 'active-parent' : '' ?>" onclick="toggleMenu('finance-menu')">
                <span><ion-icon name="wallet-outline"></ion-icon> Finance & Assets</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $fin_active ? 'open' : '' ?>" id="finance-menu">
                <li class="erp-item"><a href="/erp/finance/dashboard" class="erp-link <?= (strpos($uri, '/erp/finance/dashboard') !== false) ? 'active' : '' ?>">Financial Overview</a></li>
                <li class="erp-item"><a href="/erp/transactions" class="erp-link <?= (strpos($uri, 'transactions') !== false) ? 'active' : '' ?>">Transactions</a></li>
                <li class="erp-item"><a href="/erp/finance/expenses" class="erp-link <?= (strpos($uri, 'expenses') !== false) ? 'active' : '' ?>">Expenses</a></li>
                <li class="erp-item"><a href="/erp/finance/invoices" class="erp-link <?= (strpos($uri, 'invoices') !== false) ? 'active' : '' ?>">Invoices</a></li>
                <li class="erp-item"><a href="/erp/finance/estimates" class="erp-link <?= (strpos($uri, 'estimates') !== false) ? 'active' : '' ?>">Estimates</a></li>
                <li class="erp-item"><a href="/erp/finance/vendors" class="erp-link <?= (strpos($uri, 'vendors') !== false) ? 'active' : '' ?>">Vendors</a></li>
                <li class="erp-item"><a href="/erp/inventory" class="erp-link <?= (strpos($uri, 'inventory') !== false) ? 'active' : '' ?>">Stock/Inventory</a></li>
                <li class="erp-item"><a href="/erp/assets" class="erp-link <?= (strpos($uri, 'assets') !== false) ? 'active' : '' ?>">Office Assets</a></li>
                <li class="erp-item"><a href="/erp/wallet" class="erp-link <?= (strpos($uri, 'wallet') !== false) ? 'active' : '' ?>">Employee Wallet</a></li>
                <li class="erp-item"><a href="/erp/finance" class="erp-link <?= ($uri == '/erp/finance') ? 'active' : '' ?>">Chart of Accounts</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if ($isAdmin || $hasModule('admin')): ?>
        <!-- System -->
        <li class="erp-item">
            <div class="erp-link <?= $sys_active ? 'active-parent' : '' ?>" onclick="toggleMenu('system-menu')">
                <span><ion-icon name="settings-outline"></ion-icon> System</span>
                <ion-icon name="chevron-down-outline" class="arrow"></ion-icon>
            </div>
            <ul class="submenu <?= $sys_active ? 'open' : '' ?>" id="system-menu">
                <li class="erp-item"><a href="/erp/chat" class="erp-link <?= strpos($uri, 'chat') !== false ? 'active' : '' ?>">Team Chat</a></li>
                <li class="erp-item"><a href="/erp/announcements" class="erp-link <?= strpos($uri, 'announcements') !== false ? 'active' : '' ?>">Announcements</a></li>
                <li class="erp-item"><a href="/erp/users" class="erp-link <?= strpos($uri, 'users') !== false ? 'active' : '' ?>">Users</a></li>
                <li class="erp-item"><a href="/erp/settings" class="erp-link <?= strpos($uri, 'settings') !== false ? 'active' : '' ?>">Settings</a></li>
                <li class="erp-item"><a href="/erp/activity" class="erp-link <?= strpos($uri, 'activity') !== false ? 'active' : '' ?>">Activity Log</a></li>
            </ul>
        </li>
        <?php else: ?>
        <li class="erp-item">
             <a href="/erp/chat" class="erp-link <?= strpos($uri, 'chat') !== false ? 'active' : '' ?>">
                <span><ion-icon name="chatbubbles-outline"></ion-icon> Team Chat</span>
            </a>
        </li>
        <?php endif; ?>
        
        <li style="margin-top: 30px; margin-bottom: 20px;">
            <a href="/logout" class="erp-link" style="display: flex; align-items: center; color: #fca5a5 !important;">
                <ion-icon name="log-out-outline" style="color: #f87171 !important; margin-right: 10px;"></ion-icon> Secure Logout
            </a>
        </li>
    </ul>

    <script>
        function toggleMenu(id) {
            var menu = document.getElementById(id);
            if (menu.classList.contains('open')) {
                menu.classList.remove('open');
            } else {
                menu.classList.add('open');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('.main-content table');
            tables.forEach(table => {
                if (!table.parentElement.classList.contains('table-responsive')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive';
                    wrapper.style.overflowX = 'auto';
                    wrapper.style.width = '100%';
                    table.parentNode.insertBefore(wrapper, table);
                    wrapper.appendChild(table);
                }
            });
        });
    </script>
</aside>
<?php require_once __DIR__ . '/cori_widget.php'; ?>

