<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Manage Curriculum Tracks | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <h1>Curriculum Tracks</h1>
                <a href="/academy/tracks/create" class="btn">Create New Track</a>
            </div>

            <div class="card">
                <?php if (empty($tracks)): ?>
                    <p style="text-align: center; color: #888; padding: 20px;">No tracks found. Create your first "Mini MBA" bundle.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #eee;">
                                <th style="padding: 10px;">Track Title</th>
                                <th style="padding: 10px;">Price</th>
                                <th style="padding: 10px;">Status</th>
                                <th style="padding: 10px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tracks as $track): ?>
                                <tr style="border-bottom: 1px solid #f5f5f5;">
                                    <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($track['title']) ?></td>
                                    <td style="padding: 10px;"><?= $track['price'] > 0 ? '$' . number_format($track['price'], 2) : 'Free' ?></td>
                                    <td style="padding: 10px;">
                                        <span class="badge badge-<?= $track['is_published'] ? 'success' : 'warning' ?>">
                                            <?= $track['is_published'] ? 'Published' : 'Draft' ?>
                                        </span>
                                    </td>
                                    <td style="padding: 10px;">
                                        <a href="/academy/tracks/edit/<?= $track['id'] ?>" class="btn btn-sm">Edit</a>
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

