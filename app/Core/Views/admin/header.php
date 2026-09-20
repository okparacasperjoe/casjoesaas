<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>Admin Panel | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
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
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>Admin Panel | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
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
    
    <style>
    /* ── SUPER ADMIN DARK MODE THEME (OVERRIDES GLOBAL WHITE) ── */
    body.super-admin-dark-mode,
    body.super-admin-dark-mode:has(.main-content) {
        background: #090a0f !important;
        color: #f1f5f9 !important;
    }
    @media (min-width: 992px) {
        body.super-admin-dark-mode .main-content,
        .main-content {
            margin-left: 270px !important;
            width: calc(100% - 270px) !important;
            min-width: calc(100% - 270px) !important;
            flex: 1 !important;
            box-sizing: border-box !important;
            padding: 30px !important;
        }
    }
    body.super-admin-dark-mode .main-content {
        background: #090a0f !important;
        color: #f1f5f9 !important;
        padding: 30px !important;
    }
    body.super-admin-dark-mode .main-content .card,
    body.super-admin-dark-mode .card {
        background: #13141f !important;
        border: 1px solid rgba(255, 166, 0, 0.2) !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4) !important;
        color: #f1f5f9 !important;
    }
    body.super-admin-dark-mode .content-header h1,
    body.super-admin-dark-mode .main-content h1,
    body.super-admin-dark-mode .main-content h2,
    body.super-admin-dark-mode .main-content h3,
    body.super-admin-dark-mode .main-content h4,
    body.super-admin-dark-mode .main-content h5,
    body.super-admin-dark-mode .main-content label,
    body.super-admin-dark-mode .card h1,
    body.super-admin-dark-mode .card h2,
    body.super-admin-dark-mode .card h3,
    body.super-admin-dark-mode .card h4,
    body.super-admin-dark-mode .card h5 {
        color: #ffffff !important;
    }
    body.super-admin-dark-mode .main-content table,
    body.super-admin-dark-mode .card table {
        background: #13141f !important;
        color: #e2e8f0 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    body.super-admin-dark-mode .main-content table th,
    body.super-admin-dark-mode .card table th {
        background: #191b2a !important;
        color: #FFA600 !important;
        border-bottom: 1px solid rgba(255, 166, 0, 0.3) !important;
    }
    body.super-admin-dark-mode .main-content table td,
    body.super-admin-dark-mode .card table td {
        background: #13141f !important;
        color: #e2e8f0 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    body.super-admin-dark-mode .main-content table tr:hover td,
    body.super-admin-dark-mode .card table tr:hover td {
        background: #1d1f30 !important;
    }
    body.super-admin-dark-mode .main-content input,
    body.super-admin-dark-mode .main-content select,
    body.super-admin-dark-mode .main-content textarea {
        background: #0c0d14 !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }
    body.super-admin-dark-mode .main-content input:focus,
    body.super-admin-dark-mode .main-content select:focus,
    body.super-admin-dark-mode .main-content textarea:focus {
        border-color: #FFA600 !important;
        box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.15) !important;
    }
    body.super-admin-dark-mode .badge {
        background: rgba(255, 166, 0, 0.15) !important;
        color: #FFA600 !important;
        border: 1px solid rgba(255, 166, 0, 0.3) !important;
    }
    body.super-admin-dark-mode .stats-grid .stat-card,
    body.super-admin-dark-mode .stat-card {
        background: #13141f !important;
        border: 1px solid rgba(255, 166, 0, 0.2) !important;
    }
    </style>
</head>
<body class="super-admin-dark-mode">
<div class="app-container">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <?php require_once __DIR__ . '/../partials/push_notifications.php'; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <main class="main-content">
