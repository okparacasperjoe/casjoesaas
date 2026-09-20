<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Company Profile | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <h2>Company Profile</h2>
        <div class="card">
            <dl>
                <dt><strong>Company Name</strong></dt>
                <dd><?= htmlspecialchars($settings['company_name'] ?? 'Not Set') ?></dd>
                
                <dt><strong>Address</strong></dt>
                <dd><?= htmlspecialchars($settings['company_address'] ?? 'Not Set') ?></dd>
                
                <dt><strong>Tax ID</strong></dt>
                <dd><?= htmlspecialchars($settings['company_tax_id'] ?? 'Not Set') ?></dd>
            </dl>
            <a href="/erp/settings" class="btn">Edit in Settings</a>
        </div>
    </main>
</div>
</body>
</html>
