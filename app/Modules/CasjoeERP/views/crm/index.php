<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRM | Casjoe ERP</title>
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
            <li class="nav-item"><a href="/erp" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> ERP Dashboard</a></li>
            <li class="nav-item"><a href="/erp/crm" class="nav-link active"><ion-icon name="people-circle-outline"></ion-icon> Customers</a></li>
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
