<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Static Websites | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <?php $active = 'static_sites'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="cl-header">
                    <div>
                        <h1>Static Websites</h1>
                    </div>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="/links/static/ai-create" class="cl-btn" style="background: linear-gradient(135deg, #FFA600, #FFC107);">
                            <ion-icon name="sparkles-outline"></ion-icon> Generate with AI
                        </a>
                        <a href="/links/static/create" class="cl-btn" style="background: rgba(255, 255, 255, 0.1); border: 1px solid var(--cl-border); color: var(--cl-text) !important;">
                            <ion-icon name="cloud-upload-outline"></ion-icon> Deploy ZIP
                        </a>
                    </div>
                </div>

                <div class="cl-panel">
                    <?php if (empty($sites)): ?>
                        <div class="cl-empty">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                            <h3>No sites deployed yet</h3>
                            <p>Generate a website instantly with AI or upload a ZIP file to host your static site.</p>
                            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                                <a href="/links/static/ai-create" class="cl-btn cl-btn-sm">✨ Generate with AI</a>
                                <a href="/links/static/create" class="cl-btn cl-btn-sm" style="background: rgba(255, 255, 255, 0.1); color: var(--cl-text) !important;">Upload ZIP</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table class="cl-table">
                                <thead>
                                    <tr>
                                        <th>Website Name</th>
                                        <th>URL</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sites as $site): ?>
                                    <tr>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($site['subdomain']) ?></td>
                                        <td>
                                            <a href="/sites/<?= htmlspecialchars($site['subdomain']) ?>" class="cl-link" target="_blank">
                                                /sites/<?= htmlspecialchars($site['subdomain']) ?> <ion-icon name="open-outline"></ion-icon>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="cl-badge-active"><?= htmlspecialchars($site['status']) ?></span>
                                        </td>
                                        <td>
                                            <span class="cl-date"><?= date('M j, Y', strtotime($site['created_at'])) ?></span>
                                        </td>
                                        <td>
                                            <div class="cl-actions">
                                                <button class="cl-icon-btn stats" onclick="copyToClipboard('/sites/<?= htmlspecialchars($site['subdomain']) ?>/', this)" title="Copy URL">
                                                    <ion-icon name="share-outline"></ion-icon>
                                                </button>
                                                <a href="/links/static/edit/<?= $site['id'] ?>" class="cl-icon-btn edit" title="Edit">
                                                    <ion-icon name="create-outline"></ion-icon>
                                                </a>
                                                <form action="/links/static/delete/<?= $site['id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this site?');" style="display:inline;">
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
            
            <script>
            function copyToClipboard(path, button) {
                const fullUrl = window.location.origin + path;
                const tempInput = document.createElement('input');
                tempInput.value = fullUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                const originalHTML = button.innerHTML;
                button.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon>';
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                }, 2000);
            }
            </script>
        </main>
    </div>
</body>
</html>
