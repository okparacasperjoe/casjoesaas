<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
</head>
<body>
    <div class="app-container">
        <?php $active = 'qr_codes'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <div id="casjoe-links-app">
                <div class="cl-header">
                    <h1>My QR Codes</h1>
                    <a href="/links/qr/create" class="cl-btn">Create QR Code</a>
                </div>
                
                <div class="cl-panel">
                    <?php if (empty($qrcodes)): ?>
                        <div class="cl-empty">
                            <ion-icon name="qr-code-outline"></ion-icon>
                            <h3>No QR codes yet</h3>
                            <p>Create your first QR code to get started.</p>
                            <a href="/links/qr/create" class="cl-btn cl-btn-sm">Create QR Code</a>
                        </div>
                    <?php else: ?>
                        <div class="cl-card-grid">
                            <?php foreach ($qrcodes as $qr): ?>
                                <div class="cl-card">
                                    <div class="cl-qr-wrap" id="qr-canvas-<?= $qr['id'] ?>"></div>
                                    <h4><?= htmlspecialchars($qr['name']) ?></h4>
                                    <div class="cl-card-type"><?= htmlspecialchars($qr['type']) ?></div>
                                    <div class="cl-card-actions">
                                        <button type="button" class="cl-icon-btn download" title="Download" onclick="window.downloadQr(<?= $qr['id'] ?>)">
                                            <ion-icon name="download-outline"></ion-icon>
                                        </button>
                                        <form action="/links/qr/delete/<?= $qr['id'] ?>" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\Services\CsrfService::generateToken() ?? '') ?>">
                                            <button type="submit" class="cl-icon-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this QR code?');">
                                                <ion-icon name="trash-outline"></ion-icon>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <script>
                                    (function() {
                                        const config = <?= $qr['design_config'] ?: '{}' ?>;
                                        const qrCode = new QRCodeStyling({
                                            width: 150,
                                            height: 150,
                                            type: "svg",
                                            data: "<?= htmlspecialchars($qr['content']) ?>",
                                            dotsOptions: config.dotsOptions || {},
                                            backgroundOptions: config.backgroundOptions || {},
                                            image: config.image || null,
                                            cornersSquareOptions: config.cornersSquareOptions || {},
                                            cornersDotOptions: config.cornersDotOptions || {}
                                        });
                                        qrCode.append(document.getElementById("qr-canvas-<?= $qr['id'] ?>"));
                                        
                                        window.downloadQr = function(id) {
                                             if(id === <?= $qr['id'] ?>) qrCode.download({ name: "qr-code-" + id, extension: "png" });
                                        }
                                    })();
                                    </script>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
