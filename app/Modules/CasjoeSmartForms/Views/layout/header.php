<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <title><?= $title ?? 'Casjoe Smart Forms' ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <!-- Bootstrap 5 for Builder UI -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .app-container { min-height: 100vh; display: block; }
        .main-content { padding: 20px; }
        
        /* Dark Theme Overrides for Bootstrap */
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
        }
        .card-header {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .text-dark { color: white !important; }
        .text-white { color: white !important; }
        .text-white-50 { color: rgba(255, 255, 255, 0.6) !important; }
        .text-muted { color: rgba(255, 255, 255, 0.6) !important; }
        .bg-white { background: transparent !important; }
        .bg-light { background: rgba(255, 255, 255, 0.05) !important; }
        
        /* Tables */
        .table { color: white; }
        .table-hover tbody tr:hover { color: white; background-color: rgba(255, 255, 255, 0.1); }
        .table thead th { border-bottom: 1px solid rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.7); }
        .table td, .table th { border-color: rgba(255, 255, 255, 0.1); }
        
        /* Forms */
        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #FFA600;
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(255, 166, 0, 0.25);
        }
        .form-label { color: rgba(255, 255, 255, 0.9); }

        /* Buttons */
        .btn-outline-secondary { color: rgba(255, 255, 255, 0.8); border-color: rgba(255, 255, 255, 0.3); }
        .btn-outline-secondary:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .btn-outline-light { color: #f8f9fa; border-color: #f8f9fa; }
        .btn-outline-light:hover { background: #f8f9fa; color: #000; }
        
        /* List Groups */
        .list-group-item { background: transparent; border-color: rgba(255, 255, 255, 0.1); color: white; }
        
        /* Unified Executive Sidebar */
        .sidebar { width: 270px !important; min-width: 270px !important; background: rgba(3, 4, 20, 0.85) !important; backdrop-filter: blur(30px) !important; -webkit-backdrop-filter: blur(30px) !important; border-right: 1px solid rgba(255, 166, 0, 0.25) !important; box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important; position: fixed !important; top: 0 !important; bottom: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; padding: 0 !important; display: flex !important; flex-direction: column !important; }
        .sidebar .brand { padding: 24px 20px !important; margin: 0 0 12px 0 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; display: flex !important; align-items: center !important; font-size: 1.3rem !important; font-weight: 700 !important; color: #ffffff !important; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.72) !important; border-radius: 14px !important; margin: 6px 14px !important; padding: 13px 18px !important; font-weight: 600 !important; font-size: 0.95rem !important; transition: all 0.25s ease !important; display: flex !important; align-items: center !important; gap: 12px !important; border: 1px solid transparent !important; border-left: 1px solid transparent !important; background: transparent !important; text-decoration: none !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%) !important; border: 1px solid rgba(255, 166, 0, 0.3) !important; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12) !important; }
        .sidebar .nav-link ion-icon { color: #FFA600 !important; font-size: 1.35rem !important; margin-right: 0 !important; }

        .main-content { 
            margin-left: 270px; 
            padding: 30px; 
            width: calc(100% - 270px);
        }

        @media (max-width: 991px) {
            .sidebar { 
                transform: translateX(-100%); 
            }
            .sidebar.active { 
                transform: translateX(0); 
            }
            .main-content { 
                margin-left: 0; 
                width: 100%; 
                padding: 15px 15px 100px 15px;
            }
        }
    </style>
</head>
<body>
<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../layout/sidebar_forms_css.php'; ?>

        <div class="forms-brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;"><img src="/assets/casjoe_logo.webp" alt="Casjoe" style="height: 40px;"></a></div>

        <ul class="forms-menu">
            <li class="forms-item">
                <a href="/smart-forms" class="forms-link <?= strpos($_SERVER['REQUEST_URI'], '/smart-forms') !== false && strpos($_SERVER['REQUEST_URI'], '/create') === false ? 'active' : '' ?>">
                    <ion-icon name="grid-outline"></ion-icon> Forms
                </a>
            </li>
            <li class="forms-item">
                <a href="/smart-forms/create" class="forms-link <?= strpos($_SERVER['REQUEST_URI'], '/create') !== false ? 'active' : '' ?>">
                    <ion-icon name="add-circle-outline"></ion-icon> Create New
                </a>
            </li>
            <li class="forms-item">
                <a href="/smart-forms/templates" class="forms-link <?= strpos($_SERVER['REQUEST_URI'], '/templates') !== false ? 'active' : '' ?>">
                    <ion-icon name="copy-outline"></ion-icon> Templates
                </a>
            </li>
            
            <li class="forms-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <a href="/dashboard" class="forms-link">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back to Dashboard
                </a>
            </li>
            <li class="forms-item">
                <a href="/logout" class="forms-link">
                    <ion-icon name="log-out-outline"></ion-icon> Logout
                </a>
            </li>
        </ul>
    </aside>
    <main class="main-content">

