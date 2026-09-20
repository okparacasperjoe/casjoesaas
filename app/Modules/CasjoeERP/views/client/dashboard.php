<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Client Portal | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../layout/sidebar_erp_css.php'; ?>

        <div class="erp-brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="erp-menu">
            <li class="erp-item"><a href="/erp/client/dashboard" class="erp-link active"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="erp-item"><a href="/erp/client/projects" class="erp-link"><ion-icon name="briefcase-outline"></ion-icon> My Projects</a></li>
            <li class="erp-item"><a href="/erp/client/invoices" class="erp-link"><ion-icon name="receipt-outline"></ion-icon> Invoices</a></li>
            <li class="erp-item"><a href="/logout" class="erp-link" style="color:red;"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Client Portal</h2>
            <div class="user-profile">
                <span>Welcome, <?= htmlspecialchars($this->user['name']) ?></span>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Active Projects</h3>
                <div class="value"><?= $countProjects ?></div>
            </div>
            <div class="stat-card">
                <h3>Unpaid Invoices</h3>
                <div class="value" style="color: #e53e3e;"><?= $countInvoices ?></div>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Quick Actions</h3>
            <div style="display: flex; gap: 10px;">
                <a href="/erp/client/projects" class="btn">View Projects</a>
                <a href="/erp/client/invoices" class="btn" style="background: #2b6cb0;">Pay Invoices</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
