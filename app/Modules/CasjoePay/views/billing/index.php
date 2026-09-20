<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>My Subscription | Casjoe Pay</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_pay_css.php'; ?>

            <!-- Simplified Sidebar for Billing -->
            <div class="sidebar-header">
                <a href="/" style="color: #fff; text-decoration: none;">&larr; Back to App</a>
            </div>
            <nav class="sidebar-nav">
                <a href="/billing" class="pay-item active">
                    <ion-icon name="card-outline"></ion-icon> Subscription
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <h1>Billing & Subscription</h1>
            </div>

            <!-- Active Subscription -->
            <div class="card app-card-white" style="margin-bottom: 30px;">
                <h2>Current Plan</h2>
                <?php if ($subscription): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                        <div>
                            <h3 style="font-size: 1.5rem; color: var(--primary); margin: 0;"><?= htmlspecialchars($subscription['plan_name']) ?></h3>
                            <p style="color: #666; margin: 5px 0;">$<?= $subscription['price'] ?> / <?= $subscription['billing_interval'] ?></p>
                            <span class="badge badge-success">Active</span>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin-bottom: 10px;">Renews on: <strong><?= date('M d, Y', strtotime($subscription['current_period_end'])) ?></strong></p>
                            <a href="/billing/cancel/<?= $subscription['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel?');">Cancel Subscription</a>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color: #666;">You are currently on the <strong>Free Plan</strong>.</p>
                <?php endif; ?>
            </div>

            <!-- Available Plans -->
            <h2 style="margin-bottom: 20px;">Available Plans</h2>
            <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 40px;">
                <?php foreach ($plans as $p): ?>
                    <!-- Highlight current plan -->
                    <?php $isCurrent = ($subscription && $subscription['plan_id'] == $p['id']); ?>
                    
                    <div class="card app-card-white" style="position: relative; <?= $isCurrent ? 'border: 2px solid var(--primary);' : '' ?>">
                        <?php if ($isCurrent): ?>
                            <div style="position: absolute; top: 10px; right: 10px; color: var(--primary); font-weight: bold; font-size: 0.8rem;">CURRENT</div>
                        <?php endif; ?>

                        <h3 style="color: #333; margin-top: 0;"><?= htmlspecialchars($p['name']) ?></h3>
                        <div style="font-size: 2rem; font-weight: bold; color: var(--primary); margin: 10px 0;">
                            $<?= htmlspecialchars($p['price']) ?> <span style="font-size: 1rem; color: #999; font-weight: normal;">/ <?= $p['billing_interval'] ?></span>
                        </div>
                        
                        <ul style="list-style: none; padding: 0; margin: 20px 0; color: #666;">
                            <?php foreach (json_decode($p['features'] ?? '[]') as $f): ?>
                                <li style="margin-bottom: 8px;"><ion-icon name="checkmark-circle-outline" style="color: green; vertical-align: middle;"></ion-icon> <?= htmlspecialchars($f) ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <?php if ($isCurrent): ?>
                            <button class="btn btn-secondary" disabled style="width: 100%;">Current Plan</button>
                        <?php else: ?>
                            <a href="/billing/subscribe/<?= $p['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Upgrade</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Invoice History -->
            <?php if (!empty($invoices)): ?>
                <div class="card app-card-white">
                    <h2>Invoice History</h2>
                    <table class="table" style="width: 100%; margin-top: 20px;">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $inv): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($inv['issued_date'])) ?></td>
                                    <td>$<?= number_format($inv['amount'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $inv['status'] == 'paid' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($inv['status']) ?>
                                        </span>
                                    </td>
                                    <td>INV-<?= str_pad($inv['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>

