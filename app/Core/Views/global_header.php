<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title><?= $title ?? 'Casjoe App' ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .app-container { min-height: 100vh; display: flex; }
        .main-content { flex: 1; padding: 20px; background: #f8f9fa; margin-left: 270px !important; }
        .sidebar { width: 270px !important; min-width: 270px !important; background: rgba(3, 4, 20, 0.85) !important; backdrop-filter: blur(30px) !important; -webkit-backdrop-filter: blur(30px) !important; border-right: 1px solid rgba(255, 166, 0, 0.25) !important; box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important; position: fixed !important; top: 0 !important; bottom: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; padding: 0 !important; display: flex !important; flex-direction: column !important; }
        .sidebar .brand { padding: 24px 20px !important; margin: 0 0 12px 0 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; display: flex !important; align-items: center !important; font-size: 1.3rem !important; font-weight: 700 !important; color: #ffffff !important; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.72) !important; border-radius: 14px !important; margin: 6px 14px !important; padding: 13px 18px !important; font-weight: 600 !important; font-size: 0.95rem !important; transition: all 0.25s ease !important; display: flex !important; align-items: center !important; gap: 12px !important; border: 1px solid transparent !important; border-left: 1px solid transparent !important; background: transparent !important; text-decoration: none !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%) !important; border: 1px solid rgba(255, 166, 0, 0.3) !important; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12) !important; }
        .sidebar .nav-link ion-icon, .sidebar .nav-link i { color: #FFA600 !important; font-size: 1.35rem !important; margin-right: 0 !important; }
        
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -270px !important;
                width: 270px !important;
                height: 100%;
                z-index: 1000;
                transition: 0.3s;
            }
            .sidebar.active { left: 0 !important; }
            .main-content { padding-top: 70px; width: 100%; margin-left: 0 !important; }
            body.sidebar-minimized .main-content { margin-left: 0 !important; }
            body.sidebar-minimized .sidebar { left: -270px !important; }
        }
    
        @media (min-width: 769px) {
            /* Sidebar Minimized Styles */
            body.sidebar-minimized .sidebar { width: 80px !important; min-width: 80px !important; }
            body.sidebar-minimized .main-content { margin-left: 80px !important; }
            body.sidebar-minimized .sidebar .brand .brand-logo { display: none !important; }
            body.sidebar-minimized .sidebar .nav-link .nav-text { display: none !important; }
            body.sidebar-minimized .sidebar .nav-link { justify-content: center !important; padding: 13px 0 !important; border-radius: 50% !important; width: 50px !important; height: 50px !important; margin: 6px auto !important; }
            body.sidebar-minimized .sidebar .brand { padding: 24px 0 !important; justify-content: center !important; }
            body.sidebar-minimized .sidebar-toggle-btn { margin-left: 0 !important; }
            body.sidebar-minimized .sidebar .nav-item strong { display: none !important; }
        }
        
        .sidebar { transition: width 0.3s ease, min-width 0.3s ease !important; }
        .main-content { transition: margin-left 0.3s ease !important; }
        .sidebar-toggle-btn { background: transparent; border: none; color: #FFA600; cursor: pointer; font-size: 1.8rem; display: flex; align-items: center; margin-left: auto; transition: transform 0.3s ease; }
        .sidebar-toggle-btn:hover { transform: scale(1.1); }
</style>
    <!-- Microsoft Clarity Tracking Code -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "xmg6md3utg");
    </script>
    
    <!-- Google Analytics (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-05W6PSDS11"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-05W6PSDS11');
    </script>
</head>
<script>if(localStorage.getItem('sidebar_minimized') === 'true') { document.documentElement.classList.add('sidebar-minimized'); document.body.classList.add('sidebar-minimized'); }</script>
<body>
<?php if (isset($_SESSION['impersonator_id'])): ?>
    <div class="impersonation-banner">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span>
                <ion-icon name="eye-outline"></ion-icon> 
                <strong>IMPERSONATING:</strong> <?= htmlspecialchars($_SESSION['user_email'] ?? 'User') ?>
            </span>
            <a href="/<?= ADMIN_PATH ?>/users/stop-impersonate" class="btn btn-sm btn-light">
                <ion-icon name="exit-outline"></ion-icon> Stop & Return to Admin
            </a>
        </div>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/partials/mobile_nav.php'; ?>
<div class="app-container animate-fade-in">
    <aside class="sidebar">
        <div class="brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;" class="brand-logo"><img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 40px;"></a><button class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Toggle Sidebar"><ion-icon name="menu-outline"></ion-icon></button></div>

        <!-- Dynamic Integrations -->
    <?php
    $db = \App\Core\Database::getInstance();
    $settings = [];
    try {
        $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings");
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (\Exception $e) { /* Ignore if table missing */ }
    ?>

    <!-- Google Analytics -->
    <?php $gaId = defined('GOOGLE_ANALYTICS_ID') && !empty(GOOGLE_ANALYTICS_ID) ? GOOGLE_ANALYTICS_ID : ($settings['google_analytics_id'] ?? ''); ?>
    <?php if (!empty($gaId)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($gaId) ?>');
    </script>
    <?php endif; ?>

    <!-- Pusher -->
    <?php if (!empty($settings['pusher_enabled']) && $settings['pusher_enabled'] == '1'): ?>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        window.pusher = new Pusher('<?= htmlspecialchars($settings['pusher_key'] ?? '') ?>', {
            cluster: '<?= htmlspecialchars($settings['pusher_cluster'] ?? 'mt1') ?>'
        });
    </script>
    <?php endif; ?>

    <!-- Google Translate -->
    <?php if (!empty($settings['google_translate_enabled']) && $settings['google_translate_enabled'] == '1'): ?>
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
        /* Floating Translate Widget */
        #google_translate_element {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 9999;
            background: rgba(255,255,255,0.9);
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .goog-te-gadget-simple { background: transparent !important; border: none !important; }
    
        /* Sidebar Minimized Styles */
        body.sidebar-minimized .sidebar { width: 80px !important; min-width: 80px !important; }
        body.sidebar-minimized .main-content { margin-left: 80px !important; }
        body.sidebar-minimized .sidebar .brand .brand-logo { display: none !important; }
        body.sidebar-minimized .sidebar .nav-link .nav-text { display: none !important; }
        body.sidebar-minimized .sidebar .nav-link { justify-content: center !important; padding: 13px 0 !important; border-radius: 50% !important; width: 50px !important; height: 50px !important; margin: 6px auto !important; }
        body.sidebar-minimized .sidebar .brand { padding: 24px 0 !important; justify-content: center !important; }
        body.sidebar-minimized .sidebar-toggle-btn { margin-left: 0 !important; }
        body.sidebar-minimized .sidebar .nav-item strong { display: none !important; }
        
        .sidebar { transition: width 0.3s ease, min-width 0.3s ease !important; }
        .main-content { transition: margin-left 0.3s ease !important; }
        .sidebar-toggle-btn { background: transparent; border: none; color: #FFA600; cursor: pointer; font-size: 1.8rem; display: flex; align-items: center; margin-left: auto; transition: transform 0.3s ease; }
        .sidebar-toggle-btn:hover { transform: scale(1.1); }
</style>
    <div id="google_translate_element"></div>
    <?php endif; ?>

    <!-- Tawk.to -->
    <?php if (!empty($settings['tawk_enabled']) && $settings['tawk_enabled'] == '1' && !empty($settings['tawk_property_id'])): ?>
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/<?= htmlspecialchars($settings['tawk_property_id']) ?>/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <?php endif; ?>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link">
                    <ion-icon name="grid-outline"></ion-icon> <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/profile" class="nav-link"><ion-icon name="person-circle-outline"></ion-icon> <span class="nav-text">My Profile</span></a>
            </li>
            <li class="nav-item">
                <a href="/billing" class="nav-link"><ion-icon name="card-outline"></ion-icon> <span class="nav-text">Billing</span></a>
            </li>
            <li class="nav-item">
                <a href="/support" class="nav-link"><ion-icon name="help-buoy-outline"></ion-icon> <span class="nav-text">Casjoe Support</span></a>
            </li>
            <li class="nav-item" style="margin-top: 15px;">
                <a href="/ai-office" class="nav-link" style="background: linear-gradient(135deg, rgba(255,166,0,0.1) 0%, rgba(0,0,102,0.3) 100%); border: 1px solid rgba(255,166,0,0.4); color: #FFA600 !important; font-weight: 700 !important;">
                    <ion-icon name="briefcase" style="color: #FFA600 !important;"></ion-icon> <span class="nav-text">The AI Office</span>
                </a>
            </li>
            
            <!-- Contextual Links could be injected here or hardcoded for now -->
            <?php if(strpos($_SERVER['REQUEST_URI'], '/' . ADMIN_PATH . '/shop') !== false): ?>
                <li class="nav-item"><strong class="text-white small text-uppercase mt-3 mb-2 d-block px-2">Shop Admin</strong></li>
                <li class="nav-item"><a href="/<?= ADMIN_PATH ?>/shop" class="nav-link"><ion-icon name="stats-chart-outline"></ion-icon> <span class="nav-text">Overview</span></a></li>
                <li class="nav-item"><a href="/<?= ADMIN_PATH ?>/shop/vendors" class="nav-link"><ion-icon name="people-outline"></ion-icon> <span class="nav-text">Vendors</span></a></li>
            <?php endif; ?>

            <?php if(strpos($_SERVER['REQUEST_URI'], '/shop/vendor') !== false): ?>
                <li class="nav-item"><strong class="text-white small text-uppercase mt-3 mb-2 d-block px-2">Vendor Portal</strong></li>
                <li class="nav-item"><a href="/shop/vendor/dashboard" class="nav-link"><ion-icon name="speedometer-outline"></ion-icon> <span class="nav-text">Dashboard</span></a></li>
                <li class="nav-item"><a href="/shop/vendor/products" class="nav-link"><ion-icon name="cube-outline"></ion-icon> <span class="nav-text">Products</span></a></li>
                <li class="nav-item"><a href="/shop/vendor/orders" class="nav-link"><ion-icon name="cart-outline"></ion-icon> <span class="nav-text">Orders</span></a></li>
                <li class="nav-item"><a href="/shop" target="_blank" class="nav-link"><ion-icon name="open-outline"></ion-icon> <span class="nav-text">Visit Store</span></a></li>
            <?php endif; ?>

            <!-- Push Notification Toggle (Hidden by default, shown by script if not subscribed) -->
            <li class="nav-item" id="enable-push-btn" style="display:none; cursor: pointer;">
                <a onclick="subscribeToPush()" class="nav-link" style="background: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid #28a745;">
                    <ion-icon name="notifications-outline"></ion-icon> <span class="nav-text">Enable Notifications</span>
                </a>
            </li>

            <li class="nav-item" style="margin-top: auto;">
                <a href="/logout" class="nav-link"><ion-icon name="log-out-outline"></ion-icon> <span class="nav-text">Logout</span></a>
            </li>
        </ul>
    
        <script>
        function toggleSidebar() {
            const body = document.body;
            const isMinimized = body.classList.toggle('sidebar-minimized');
            localStorage.setItem('sidebar_minimized', isMinimized);
        }
        // Initialize state on load
        if(localStorage.getItem('sidebar_minimized') === 'true') {
            document.body.classList.add('sidebar-minimized');
        }
        </script>
    </aside>

    
    <!-- Push Notifications Script -->
    <?php require_once __DIR__ . '/partials/push_notifications.php'; ?>
    
    <main class="main-content">
