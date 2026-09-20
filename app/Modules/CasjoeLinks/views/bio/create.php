<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bio Page | Casjoe Links</title>
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
                        <h1>Create Bio Page</h1>
                        <p>Set up your personalized link-in-bio page.</p>
                    </div>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="cl-error">
                        <ion-icon name="alert-circle"></ion-icon>
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="cl-form-card">
                    <form action="/links/bio/store" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\Services\CsrfService::generateToken() ?? '') ?>">
                        
                        <div style="background: var(--cl-accent-subtle, #f0f4ff); border: 1px solid var(--cl-accent-border, #d9e2ff); border-radius: var(--cl-radius-sm, 6px); padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <span style="font-weight: 600; font-size: 14px; color: var(--cl-accent, #2563eb);">✨ AI Name Generator</span>
                            <button type="button" onclick="generateAiBio()" style="background: var(--cl-accent, #2563eb); color: var(--cl-text-btn, #fff); border: none; padding: 6px 14px; border-radius: var(--cl-radius-xs, 4px); font-weight: 600; cursor: pointer; font-size: 13px;">✨ Suggest</button>
                        </div>

                        <div class="cl-form-group">
                            <label class="cl-label" for="bioTitle">Page Name</label>
                            <input type="text" class="cl-input" name="title" id="bioTitle" required placeholder="e.g. My Socials">
                        </div>

                        <div class="cl-form-group">
                            <label class="cl-label" for="bioSlug">URL Slug</label>
                            <div class="cl-input-group">
                                <span class="cl-input-prefix">/@</span>
                                <input type="text" class="cl-input" name="slug" id="bioSlug" required placeholder="myname">
                            </div>
                        </div>

                        <div style="display: flex; gap: 16px; margin-top: 24px; align-items: center;">
                            <button type="submit" class="cl-btn">Create Page</button>
                            <a href="/links/bio" class="cl-back">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    function generateAiBio() {
        const name = prompt("What is your creator or business name? (e.g. 'Casjoe Tech Solutions')");
        if (!name) return;
        document.querySelector('input[name="title"]').value = name;
        const slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        document.querySelector('input[name="slug"]').value = slug;
    }
    </script>
</body>
</html>
