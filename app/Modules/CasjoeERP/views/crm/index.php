<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>CRM | Casjoe ERP</title>
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
            <li class="erp-item"><a href="/erp" class="erp-link"><ion-icon name="arrow-back-outline"></ion-icon> ERP Dashboard</a></li>
            <li class="erp-item"><a href="/erp/crm" class="erp-link active"><ion-icon name="people-circle-outline"></ion-icon> Customers</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Customer Relationship Management</h2>
            <button class="btn">+ Add Customer</button>
        </div>

        <div class="card">
            <h3>Customers & Leads</h3>
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Name</th>
                        <th style="padding: 10px;">Company</th>
                        <th style="padding: 10px;">Contact</th>
                        <th style="padding: 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="4" style="padding: 20px; text-align: center;">No customers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $cust): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($cust['name']) ?></td>
                                <td style="padding: 10px;"><?= htmlspecialchars($cust['company']) ?></td>
                                <td style="padding: 10px; font-size: 0.9rem;">
                                    <div><?= htmlspecialchars($cust['email']) ?></div>
                                    <div style="color: #666;"><?= htmlspecialchars($cust['phone']) ?></div>
                                </td>
                                <td style="padding: 10px;"><span style="background: #ebf8ff; color: #3182ce; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;"><?= ucfirst($cust['status']) ?></span></td>
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
