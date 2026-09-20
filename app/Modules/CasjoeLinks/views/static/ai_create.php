<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Website Generator | Casjoe Links</title>
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
                        <h1>✨ Generate Website with AI</h1>
                        <p>Describe your business or idea, and our AI will build and publish a static website instantly.</p>
                    </div>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="cl-error">
                        <ion-icon name="alert-circle-outline"></ion-icon>
                        <div>
                            <?php if ($_GET['error'] === 'missing_fields'): ?>
                                Please enter both a website URL path and a prompt description.
                            <?php elseif ($_GET['error'] === 'invalid_name'): ?>
                                Website URL path can only contain lowercase letters, numbers, and hyphens.
                            <?php elseif ($_GET['error'] === 'name_taken'): ?>
                                This website URL path is already taken. Please choose another name.
                            <?php else: ?>
                                <?= htmlspecialchars($_GET['error']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="cl-form-card">
                    <form action="/links/static/ai-generate" method="POST" id="aiForm">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">

                        <div class="cl-form-group">
                            <label class="cl-label">Website Name (URL Path)</label>
                            <div class="cl-input-group">
                                <span class="cl-input-prefix">/sites/</span>
                                <input type="text" name="subdomain" required placeholder="e.g. lagos-barber-shop" pattern="[a-z0-9-]+" title="Only lowercase letters, numbers, and hyphens allowed" autofocus>
                            </div>
                            <span class="cl-hint">Your website will be published live at casjoe.com/sites/your-name</span>
                        </div>

                        <div class="cl-form-group">
                            <label class="cl-label">Website Description (Prompt)</label>
                            <textarea name="prompt" rows="5" class="cl-input" required style="resize: vertical;" placeholder="e.g., A luxury barber shop in Ikeja, Lagos offering hair cuts, beard grooming, and facial treatments. Include a price menu, customer reviews, operating hours, and a booking CTA button."></textarea>
                            <span class="cl-hint">Be as specific as you like! Mention services, colors, sections, or business details.</span>
                        </div>

                        <div class="cl-form-group">
                            <label class="cl-label">Design Style</label>
                            <select name="style" class="cl-input">
                                <option value="modern dark glassmorphic">Modern Dark Glassmorphic (Recommended)</option>
                                <option value="clean bright minimalist">Clean Bright Minimalist</option>
                                <option value="elegant corporate navy">Elegant Corporate Navy & Gold</option>
                                <option value="vibrant creative startup">Vibrant Creative Startup</option>
                            </select>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="ai-loading" style="display: none; background: rgba(255, 166, 0, 0.1); border: 1px solid rgba(255, 166, 0, 0.3); padding: 16px 20px; border-radius: var(--cl-radius-sm); margin-bottom: 24px; color: var(--cl-text);">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <ion-icon name="sparkles-outline" class="cl-spin" style="font-size: 24px; color: var(--cl-accent);"></ion-icon>
                                <div>
                                    <strong style="display: block; font-size: 15px;">AI is designing your website...</strong>
                                    <span style="font-size: 13px; color: var(--cl-text-muted);">Writing HTML, styling CSS, and setting up responsive layout. This takes about 10-20 seconds.</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="cl-btn" style="width: 100%; justify-content: center; padding: 14px 24px;">
                            <ion-icon name="sparkles-outline"></ion-icon> Generate & Publish Website
                        </button>

                        <a href="/links/static" class="cl-back">Cancel & Back to Websites</a>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('aiForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').style.opacity = '0.6';
            document.getElementById('submitBtn').innerHTML = '<ion-icon name="sync-outline" class="cl-spin"></ion-icon> Generating Site...';
            document.getElementById('ai-loading').style.display = 'block';
        });
    </script>

    <style>
        @keyframes clSpin { 100% { transform: rotate(360deg); } }
        .cl-spin { animation: clSpin 1.2s linear infinite; }
    </style>
</body>
</html>
