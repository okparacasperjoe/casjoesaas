<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Short URLs | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <?php $active = 'short_urls'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="cl-header">
                    <h1>Short URLs</h1>
                    <a href="/links/short/create" class="cl-btn">Shorten New URL</a>
                </div>
                
                <div class="cl-panel">
                    <?php if (empty($urls)): ?>
                        <div class="cl-empty">
                            <ion-icon name="link-outline"></ion-icon>
                            <h3>No links created yet</h3>
                            <p>Create your first short URL and start tracking clicks.</p>
                            <a href="/links/short/create" class="cl-btn cl-btn-sm">Create Short URL</a>
                        </div>
                    <?php else: ?>
                        <table class="cl-table">
                            <thead>
                                <tr>
                                    <th>Short Link</th>
                                    <th>Original URL</th>
                                    <th>Clicks</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($urls as $url): ?>
                                    <tr>
                                        <td>
                                            <a href="/l/<?= htmlspecialchars($url['short_code']) ?>" class="cl-link" target="_blank">
                                                casjoe.com/l/<?= htmlspecialchars($url['short_code']) ?>
                                                <ion-icon name="open-outline"></ion-icon>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?= htmlspecialchars($url['long_url']) ?>" class="cl-link-muted" target="_blank" title="<?= htmlspecialchars($url['long_url']) ?>">
                                                <?= htmlspecialchars($url['long_url']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="cl-pill">
                                                <ion-icon name="bar-chart-outline"></ion-icon>
                                                <?= (int)($url['clicks'] ?? 0) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="cl-date"><?= htmlspecialchars(date('M j, Y', strtotime($url['created_at']))) ?></span>
                                        </td>
                                        <td>
                                            <div class="cl-actions">
                                                <a href="/links/short/stats/<?= $url['id'] ?>" class="cl-icon-btn stats" title="Stats">
                                                    <ion-icon name="stats-chart-outline"></ion-icon>
                                                </a>
                                                <form action="/links/short/delete/<?= $url['id'] ?>" method="POST" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\Services\CsrfService::generateToken() ?? '') ?>">
                                                    <button type="submit" class="cl-icon-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this link?');">
                                                        <ion-icon name="trash-outline"></ion-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
