<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bio Pages | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <?php $active = 'bio_pages'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="cl-header">
                    <div>
                        <h1>Bio Pages</h1>
                    </div>
                    <a href="/links/bio/create" class="cl-btn">Create New Page</a>
                </div>

                <div class="cl-panel">
                    <?php if (empty($pages)): ?>
                        <div class="cl-empty">
                            <ion-icon name="person-outline"></ion-icon>
                            <h3>No bio pages yet</h3>
                            <p>Create your first bio link page to share all your links in one place.</p>
                            <a href="/links/bio/create" class="cl-btn">Create New Page</a>
                        </div>
                    <?php else: ?>
                        <table class="cl-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($page['title'] ?? '') ?></td>
                                        <td>
                                            <a href="/@<?= htmlspecialchars($page['slug'] ?? '') ?>" class="cl-link" target="_blank">
                                                /@<?= htmlspecialchars($page['slug'] ?? '') ?> <ion-icon name="open-outline"></ion-icon>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="<?= ($page['status'] ?? '') === 'active' ? 'cl-badge-active' : 'cl-badge-draft' ?>">
                                                <?= ucfirst(htmlspecialchars($page['status'] ?? '')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="cl-pill"><?= number_format($page['views'] ?? 0) ?></span>
                                        </td>
                                        <td>
                                            <div class="cl-actions">
                                                <a href="/links/bio/edit/<?= $page['id'] ?>" class="cl-icon-btn edit" title="Edit">
                                                    <ion-icon name="pencil"></ion-icon>
                                                </a>
                                                <form action="/links/bio/delete/<?= $page['id'] ?>" method="POST" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\Services\CsrfService::generateToken() ?? '') ?>">
                                                    <button type="submit" class="cl-icon-btn delete" onclick="return confirm('Delete this bio page?');" title="Delete">
                                                        <ion-icon name="trash"></ion-icon>
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
