<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shorten URL | Casjoe Links</title>
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
                    <div>
                        <h1>Create Short Link</h1>
                        <p>Paste a long URL to generate a short, trackable casjoe link.</p>
                    </div>
                </div>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="cl-error">
                        <ion-icon name="alert-circle-outline"></ion-icon>
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="cl-form-card">
                    <form action="/links/short/store" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\Services\CsrfService::generateToken() ?? '') ?>">
                        
                        <div class="cl-form-group">
                            <label class="cl-label" for="long_url">Destination URL</label>
                            <input type="url" name="long_url" id="long_url" class="cl-input" required autofocus placeholder="https://example.com/very-long-url">
                        </div>
                        
                        <div class="cl-form-group">
                            <label class="cl-label" for="short_code">
                                Short Alias <span class="optional">(Optional)</span>
                            </label>
                            <div class="cl-input-group">
                                <span class="cl-input-prefix">casjoe.com/l/</span>
                                <input type="text" name="short_code" id="short_code" class="cl-input">
                            </div>
                            <span class="cl-hint">Leave empty to generate a random string.</span>
                        </div>
                        
                        <div class="cl-form-group">
                            <label class="cl-label" for="password">
                                Password Protection <span class="optional">(Optional)</span>
                            </label>
                            <input type="text" name="password" id="password" class="cl-input">
                            <span class="cl-hint">Require visitors to enter this password before redirecting.</span>
                        </div>
                        
                        <div class="cl-form-group">
                            <label class="cl-label" for="expires_at">
                                Expiration Date <span class="optional">(Optional)</span>
                            </label>
                            <input type="datetime-local" name="expires_at" id="expires_at" class="cl-input">
                            <span class="cl-hint">Link will expire and become inaccessible after this time.</span>
                        </div>
                        
                        <div class="cl-actions">
                            <button type="submit" class="cl-btn">
                                <ion-icon name="flash"></ion-icon>
                                Shorten URL
                            </button>
                            <a href="/links/short" class="cl-back">Back to all links</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
