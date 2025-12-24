<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Invoices | Casjoe ERP</title>
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
            <li class="nav-item"><a href="/erp/client/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/erp/client/projects" class="nav-link"><ion-icon name="briefcase-outline"></ion-icon> My Projects</a></li>
            <li class="nav-item"><a href="/erp/client/invoices" class="nav-link active"><ion-icon name="receipt-outline"></ion-icon> Invoices</a></li>
             <li class="nav-item"><a href="/logout" class="nav-link" style="color:red;"><ion-icon name="log-out-outline"></ion-icon> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>My Invoices</h2>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Invoice #</th>
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Amount</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="5" style="padding: 20px; text-align: center;">No invoices found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($inv['number']) ?></td>
                                <td style="padding: 10px;"><?= $inv['issue_date'] ?></td>
                                <td style="padding: 10px;">$<?= number_format($inv['total_amount'], 2) ?></td>
                                <td style="padding: 10px;">
                                    <span style="padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; background: <?= $inv['status'] == 'paid' ? '#c6f6d5' : '#fed7d7' ?>; color: <?= $inv['status'] == 'paid' ? '#2f855a' : '#9b2c2c' ?>;">
                                        <?= ucfirst($inv['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px;">
                                    <?php if ($inv['status'] !== 'paid'): ?>
                                        <a href="/invoice/<?= $inv['uuid'] ?>" target="_blank" class="btn-sm" style="background: #2b6cb0;">Pay Now</a>
                                    <?php else: ?>
                                        <a href="/invoice/<?= $inv['uuid'] ?>" target="_blank" class="btn-sm" style="background: #718096;">View</a>
                                    <?php endif; ?>
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
