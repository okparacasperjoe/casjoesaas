<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Knowledge Base | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .card h3 { color: #e0e0e0; border-bottom: 1px solid #444; padding-bottom: 10px; margin-bottom: 15px; }
        .card ul { list-style: none; padding: 0; }
        .card ul li { margin-bottom: 10px; }
        .card ul li a { color: var(--secondary); text-decoration: none; font-size: 1.05rem; transition: color 0.3s; }
        .card ul li a:hover { color: #fff; text-decoration: underline; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>Knowledge Base</h2>
            <input type="text" placeholder="Search help articles..." style="padding: 10px; width: 300px;">
        </div>
        <div class="card">
            <h3>Getting Started</h3>
            <ul>
                <li><a href="#">How to reset your password</a></li>
                <li><a href="#">Navigating the ERP Dashboard</a></li>
                <li><a href="#">Updating your Employee Profile</a></li>
            </ul>
        </div>
        <div class="card">
            <h3>Policies</h3>
            <ul>
                <li><a href="#">Leave Policy 2025</a></li>
                <li><a href="#">Remote Work Guidelines</a></li>
            </ul>
        </div>
    </main>
</div>
</body>
</html>
