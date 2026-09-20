<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>My Projects | Casjoe ERP</title>
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
            <li class="erp-item"><a href="/erp/client/dashboard" class="erp-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="erp-item"><a href="/erp/client/projects" class="erp-link active"><ion-icon name="briefcase-outline"></ion-icon> My Projects</a></li>
            <li class="erp-item"><a href="/erp/client/invoices" class="erp-link"><ion-icon name="receipt-outline"></ion-icon> Invoices</a></li>
             <li class="erp-item"><a href="/logout" class="erp-link" style="color:red;"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>My Projects</h2>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Project Name</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Start Date</th>
                        <th style="padding: 10px;">End Date</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr><td colspan="5" style="padding: 20px; text-align: center;">No projects found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($projects as $proj): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($proj['name']) ?></td>
                                <td style="padding: 10px;">
                                    <span style="padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; background: #eee;">
                                        <?= ucfirst(str_replace('_', ' ', $proj['status'])) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px;"><?= $proj['start_date'] ?></td>
                                <td style="padding: 10px;"><?= $proj['end_date'] ?></td>
                                <td style="padding: 10px;">
                                    <a href="/erp/client/projects/view?id=<?= $proj['id'] ?>" class="btn-sm">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
