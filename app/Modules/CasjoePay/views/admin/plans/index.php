<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Subscription Plans | Casjoe Pay Admin</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../../../Views/layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <h1>Subscription Plans</h1>
                <a href="/pay/plans/create" class="btn btn-primary">+ Create Plan</a>
            </div>

            <div class="card app-card-white">
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Interval</th>
                            <th>Features</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                                <td>$<?= htmlspecialchars($p['price']) ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($p['billing_interval']) ?></span></td>
                                <td><?= count(json_decode($p['features'] ?? '[]')) ?> Features</td>
                                <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($plans)): ?>
                            <tr><td colspan="5" style="text-align:center; color:#999;">No plans created yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

