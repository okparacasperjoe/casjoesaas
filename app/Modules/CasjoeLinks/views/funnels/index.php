<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Funnels | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <?php $active = 'funnels'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="cl-header">
                    <div>
                        <h1>Sales Funnels</h1>
                    </div>
                    <div>
                        <a href="/links/funnels/create" class="cl-btn">Create New Funnel</a>
                    </div>
                </div>

                <div class="cl-stats-grid">
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Total Funnels</div>
                            <div class="cl-stat-icon" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                                <ion-icon name="funnel-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= count($funnels) ?></div>
                    </div>
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Active</div>
                            <div class="cl-stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                                <ion-icon name="checkmark-circle-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= count(array_filter($funnels, fn($f) => $f['status'] === 'active')) ?></div>
                    </div>
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Total Sessions</div>
                            <div class="cl-stat-icon" style="background: rgba(168, 85, 247, 0.1); color: #a855f7;">
                                <ion-icon name="people-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= array_sum(array_column($funnels, 'total_sessions')) ?></div>
                    </div>
                </div>

                <div class="cl-panel">
                    <?php if (empty($funnels)): ?>
                        <div class="cl-empty">
                            <ion-icon name="funnel-outline"></ion-icon>
                            <h3>No funnels created yet</h3>
                            <p>Create your first sales funnel to start capturing leads and driving revenue.</p>
                            <a href="/links/funnels/create" class="cl-btn cl-btn-sm">Create New Funnel</a>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table class="cl-table">
                                <thead>
                                    <tr>
                                        <th>Funnel Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Sessions</th>
                                        <th>Owner</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($funnels as $funnel): ?>
                                    <tr>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($funnel['name']) ?></td>
                                        <td>
                                            <?php 
                                            $typeClass = 'cl-pill';
                                            if ($funnel['type'] === 'lead') $typeClass = 'cl-pill-info';
                                            if ($funnel['type'] === 'product') $typeClass = 'cl-pill-success';
                                            ?>
                                            <span class="<?= $typeClass ?>"><?= htmlspecialchars(ucfirst($funnel['type'])) ?></span>
                                        </td>
                                        <td>
                                            <span class="<?= $funnel['status'] === 'active' ? 'cl-badge-active' : 'cl-badge-draft' ?>">
                                                <?= htmlspecialchars(ucfirst($funnel['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="cl-pill"><?= htmlspecialchars($funnel['total_sessions']) ?></span>
                                        </td>
                                        <td>
                                            <span class="cl-date"><?= htmlspecialchars($funnel['owner_name']) ?></span>
                                        </td>
                                        <td>
                                            <span class="cl-date"><?= date('M j, Y', strtotime($funnel['created_at'])) ?></span>
                                        </td>
                                        <td>
                                            <div class="cl-actions">
                                                <a href="/links/funnels/edit/<?= $funnel['id'] ?>" class="cl-icon-btn edit" title="Edit">
                                                    <ion-icon name="create-outline"></ion-icon>
                                                </a>
                                                <form action="/links/funnels/delete/<?= $funnel['id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this funnel?');" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?? '' ?>">
                                                    <button type="submit" class="cl-icon-btn delete" title="Delete">
                                                        <ion-icon name="trash-outline"></ion-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
