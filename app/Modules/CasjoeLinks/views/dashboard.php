<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Links Dashboard | Casjoe Apps</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <?php $active = 'links_dashboard'; include __DIR__ . '/partials/sidebar_links.php'; ?>
        <main class="main-content">
            <?php include dirname(__DIR__, 3) . '/Core/Views/partials/mobile_nav.php'; ?>
            
            <div id="casjoe-links-app">
                <!-- Hero Banner Section -->
                <div class="cl-dashboard-hero">
                    <div class="cl-hero-content">
                        <div class="cl-hero-badge">
                            <ion-icon name="link-outline"></ion-icon> Links & Growth Suite
                        </div>
                        <h1>Links Dashboard</h1>
                        <p>Manage your bio pages, shortened URLs, QR codes, sales funnels, and static sites in one unified workspace.</p>
                    </div>
                    <div class="cl-hero-actions">
                        <a href="/links/short/create" class="cl-btn">
                            <ion-icon name="add-circle-outline"></ion-icon> Shorten URL
                        </a>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="cl-section">
                    <ion-icon name="stats-chart-outline"></ion-icon>
                    <h2>Asset Overview</h2>
                </div>
                
                <div class="cl-stats-grid">
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Bio Pages</div>
                            <div class="cl-stat-icon icon-blue">
                                <ion-icon name="person-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= htmlspecialchars($bioCount ?? 0) ?></div>
                    </div>
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Short URLs</div>
                            <div class="cl-stat-icon icon-purple">
                                <ion-icon name="link-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= htmlspecialchars($shortCount ?? 0) ?></div>
                    </div>
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">QR Codes</div>
                            <div class="cl-stat-icon icon-pink">
                                <ion-icon name="qr-code-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= htmlspecialchars($qrCount ?? 0) ?></div>
                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="cl-section">
                    <ion-icon name="flash-outline"></ion-icon>
                    <h2>Quick Actions</h2>
                </div>
                <div class="cl-quick-grid">
                    <a href="/links/bio/create" class="cl-quick-card">
                        <div class="cl-quick-icon icon-blue"><ion-icon name="person-add-outline"></ion-icon></div>
                        <div class="cl-quick-text">
                            <div class="cl-quick-title">New Bio Page</div>
                            <div class="cl-quick-desc">Create a branded profile link</div>
                        </div>
                    </a>
                    <a href="/links/short/create" class="cl-quick-card">
                        <div class="cl-quick-icon icon-purple"><ion-icon name="link-outline"></ion-icon></div>
                        <div class="cl-quick-text">
                            <div class="cl-quick-title">Shorten Link</div>
                            <div class="cl-quick-desc">Create a trackable short URL</div>
                        </div>
                    </a>
                    <a href="/links/funnels/create" class="cl-quick-card">
                        <div class="cl-quick-icon icon-green"><ion-icon name="funnel-outline"></ion-icon></div>
                        <div class="cl-quick-text">
                            <div class="cl-quick-title">New Sales Funnel</div>
                            <div class="cl-quick-desc">Build a conversion funnel</div>
                        </div>
                    </a>
                    <a href="/links/qr/create" class="cl-quick-card">
                        <div class="cl-quick-icon icon-pink"><ion-icon name="qr-code-outline"></ion-icon></div>
                        <div class="cl-quick-text">
                            <div class="cl-quick-title">Generate QR Code</div>
                            <div class="cl-quick-desc">Create a custom QR code</div>
                        </div>
                    </a>
                    <a href="/links/static/create" class="cl-quick-card">
                        <div class="cl-quick-icon icon-amber"><ion-icon name="cloud-upload-outline"></ion-icon></div>
                        <div class="cl-quick-text">
                            <div class="cl-quick-title">Host Static Site</div>
                            <div class="cl-quick-desc">Upload a zip to host instantly</div>
                        </div>
                    </a>
                </div>

                <!-- Recent Analytics -->
                <div class="cl-section">
                    <ion-icon name="analytics-outline"></ion-icon>
                    <h2>Recent Analytics</h2>
                </div>
                <div class="cl-info-card">
                    <ion-icon name="analytics-outline" style="font-size: 42px; margin-bottom: 12px; color: var(--cl-text-muted); opacity: 0.5;"></ion-icon>
                    <p style="margin: 0; font-weight: 500;">No analytics data recorded yet.</p>
                    <span style="font-size: 13px; color: var(--cl-text-muted); display: block; margin-top: 4px;">Clicks and visitor statistics will appear here automatically.</span>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
