<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support | Casjoe Apps</title>
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
            <li class="nav-item"><a href="/support" class="nav-link active"><ion-icon name="headset-outline"></ion-icon> My Tickets</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Support Center</h2>
            <a href="/support/create" class="btn">+ New Ticket</a>
        </div>

        <div class="card">
            <h3>My Tickets</h3>
            <?php if (empty($tickets)): ?>
                <div style="text-align: center; padding: 40px; color: #666;">
                    <ion-icon name="happy-outline" style="font-size: 3rem; margin-bottom: 15px;"></ion-icon>
                    <p>No tickets yet. Need help? Create a ticket!</p>
                </div>
            <?php else: ?>
                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #eee;">
                            <th style="padding: 10px;">Subject</th>
                            <th style="padding: 10px;">Status</th>
                            <th style="padding: 10px;">Created</th>
                            <th style="padding: 10px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($t['subject']) ?></td>
                                <td style="padding: 10px;">
                                    <span style="padding: 2px 8px; border-radius: 10px; font-size: 0.8rem; background: <?= $t['status']=='closed'?'#fee2e2':'#e6fffa' ?>; color: <?= $t['status']=='closed'?'#991b1b':'#047857' ?>">
                                        <?= ucfirst($t['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; color: #666;"><?= date('M j, Y', strtotime($t['created_at'])) ?></td>
                                <td style="padding: 10px;">
                                    <a href="/support/view?id=<?= $t['id'] ?>" class="btn" style="padding: 5px 15px; font-size: 0.8rem;">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>