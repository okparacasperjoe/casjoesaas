<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create QR Code | Casjoe Links</title>
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
                    <div>
                        <h1>Create QR Code</h1>
                        <p>Design a custom QR code with live preview.</p>
                    </div>
                </div>

                <div class="cl-split">
                    <div class="cl-split-form">
                        <div class="cl-form-card">
                            <form action="/links/qr/store" method="POST" id="qrForm">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Core\Services\CsrfService::generateToken() ?? '') ?>">
                                <input type="hidden" name="design_config" id="designConfig">
                                <input type="hidden" name="type" value="url">

                                <div class="cl-form-group">
                                    <label class="cl-label" for="qrName">Name</label>
                                    <input type="text" class="cl-input" name="name" id="qrName" required placeholder="e.g. My Website QR">
                                </div>

                                <div class="cl-form-group">
                                    <label class="cl-label" for="qrContent">Website URL</label>
                                    <input type="text" class="cl-input" name="content" id="qrContent" required placeholder="https://example.com">
                                </div>

                                <div class="cl-form-group">
                                    <label class="cl-label" for="dotsColor">Dots Color</label>
                                    <input type="color" class="cl-input" id="dotsColor" value="#000000" style="padding: 0; height: 40px; cursor: pointer;">
                                </div>

                                <div class="cl-form-group">
                                    <label class="cl-label" for="bgColor">Background Color</label>
                                    <input type="color" class="cl-input" id="bgColor" value="#ffffff" style="padding: 0; height: 40px; cursor: pointer;">
                                </div>

                                <div style="display: flex; gap: 16px; margin-top: 24px; align-items: center;">
                                    <button type="submit" class="cl-btn">Save QR Code</button>
                                    <a href="/links/qr" class="cl-back">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="cl-split-preview">
                        <div class="cl-preview-box">
                            <h3>Live Preview</h3>
                            <div id="qr-canvas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const qrCode = new QRCodeStyling({
        width: 250,
        height: 250,
        type: "svg",
        data: "https://casjoe.com",
        dotsOptions: { color: "#000000", type: "rounded" },
        backgroundOptions: { color: "#ffffff" }
    });
    qrCode.append(document.getElementById("qr-canvas"));
    document.getElementById('qrContent').addEventListener('input', function(e) {
        qrCode.update({ data: e.target.value });
    });
    document.getElementById('dotsColor').addEventListener('input', function(e) {
        qrCode.update({ dotsOptions: { color: e.target.value } });
    });
    document.getElementById('bgColor').addEventListener('input', function(e) {
        qrCode.update({ backgroundOptions: { color: e.target.value } });
    });
    document.getElementById('qrForm').addEventListener('submit', function(e) {
        const config = {
            dotsOptions: { color: document.getElementById('dotsColor').value },
            backgroundOptions: { color: document.getElementById('bgColor').value }
        };
        document.getElementById('designConfig').value = JSON.stringify(config);
    });
    </script>
</body>
</html>
