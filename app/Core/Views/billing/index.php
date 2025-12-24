<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/billing" class="nav-link active"><ion-icon name="card-outline"></ion-icon> Billing</a></li>
            <li class="nav-item"><a href="/security" class="nav-link"><ion-icon name="shield-checkmark-outline"></ion-icon> Security</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Subscription & Billing</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="padding: 15px; background: #e6fffa; color: #047857; border-radius: 10px; margin-bottom: 20px;">
                Success! Your subscription is now active.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'subscription_required'): ?>
             <div style="padding: 15px; background: #fff5f5; color: #c0392b; border-radius: 10px; margin-bottom: 20px; border: 1px solid #c0392b;">
                <strong>Access Denied:</strong> You need an active subscription to access that feature. Please upgrade below.
            </div>
        <?php endif; ?>

        <div class="card">
            <h3>Current Plan</h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);">
                        <?= ucfirst($sub['plan']) ?> Plan
                    </div>
                    <div style="margin-top: 5px;">
                        Status: 
                        <span style="font-weight: bold; color: <?= $isActive ? 'green' : 'red' ?>">
                            <?= ucfirst($sub['status']) ?>
                        </span>
                    </div>
                    <?php if ($sub['status'] === 'trial'): ?>
                        <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">
                            Trial Ends: <?= date('M j, Y', strtotime($sub['trial_ends_at'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!$isActive || $sub['status'] === 'trial'): ?>
                    <a href="/billing/upgrade" class="btn" style="padding: 15px 30px;">Upgrade to Premium</a>
                <?php else: ?>
                    <form method="POST" action="/billing/cancel" onsubmit="return confirm('Are you sure?')">
                        <button class="btn" style="background: white; color: red; border: 1px solid red;">Cancel Subscription</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Invoice History</h3>
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Reference</th>
                        <th style="padding: 10px;">Amount</th>
                        <th style="padding: 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;"><?= date('M j, Y', strtotime($inv['date'])) ?></td>
                            <td style="padding: 10px;"><?= $inv['reference'] ?></td>
                            <td style="padding: 10px;">₦<?= number_format($inv['amount'], 2) ?></td>
                            <td style="padding: 10px;"><?= ucfirst($inv['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</body>
</html>