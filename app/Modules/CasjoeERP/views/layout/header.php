<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title><?= $title ?? 'Casjoe ERP' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --primary: #000066;
            --secondary: #FFA600;
        }
        body { 
            font-family: 'Outfit', 'Segoe UI', sans-serif; 
            background: #ffffff !important;
            color: #1e293b !important;
            margin: 0;
            padding: 0;
        }
        @media (min-width: 992px) {
            .main-content {
                margin-left: 270px !important;
            }
        }
        .main-content { 
            background: #ffffff !important;
            color: #1e293b !important;
            min-height: 100vh;
            padding: 35px 40px !important;
        }
        .main-content h1, .main-content h2, .main-content h3, .top-bar h1, .top-bar h2, h1, h2, h3, h4, h5 {
            color: #000066 !important;
            font-weight: 800 !important;
        }
        .main-content .card, .card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #1e293b !important;
        }
        @media (max-width: 991px) {
            .main-content { margin-left: 0 !important; padding: 15px !important; padding-top: 80px !important; padding-bottom: 100px !important; }
        }
    </style>
</head>
<body>
    <?php require dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
