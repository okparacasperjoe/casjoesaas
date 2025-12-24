<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mail Marketing | Casjoe Apps</title>
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
            <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/mail" class="nav-link active"><ion-icon name="mail-outline"></ion-icon> Mail Overview</a></li>
            <li class="nav-item"><a href="/mail/campaigns" class="nav-link"><ion-icon name="paper-plane-outline"></ion-icon> Campaigns</a></li>
            <li class="nav-item"><a href="#" class="nav-link"><ion-icon name="people-outline"></ion-icon> Subscribers</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Email Marketing</h2>
            <a href="/mail/campaigns/create" class="btn">+ New Campaign</a>
        </div>

        <div class="grid-3">
             <div class="card">
                <h3>Total Subscribers</h3>
                <div style="font-size: 2rem; font-weight: bold;"><?= number_format($stats['subscribers']) ?></div>
             </div>
             <div class="card">
                <h3>Emails Sent</h3>
                <div style="font-size: 2rem; font-weight: bold;"><?= number_format($stats['emails_sent']) ?></div>
             </div>
             <div class="card">
                <h3>Campaigns</h3>
                <div style="font-size: 2rem; font-weight: bold;"><?= number_format($stats['campaigns']) ?></div>
             </div>
        </div>

    </main>
</div>
</body>
</html>