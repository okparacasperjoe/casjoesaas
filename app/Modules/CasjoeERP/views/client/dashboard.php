<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Portal | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/erp/client/dashboard" class="nav-link active"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/erp/client/projects" class="nav-link"><ion-icon name="briefcase-outline"></ion-icon> My Projects</a></li>
            <li class="nav-item"><a href="/erp/client/invoices" class="nav-link"><ion-icon name="receipt-outline"></ion-icon> Invoices</a></li>
            <li class="nav-item"><a href="/logout" class="nav-link" style="color:red;"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
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
