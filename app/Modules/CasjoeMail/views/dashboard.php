<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Marketing Dashboard | Casjoe Apps</title>
    <meta name="description" content="Casjoe Mail Overview">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mail_app.css?v=2.2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Sidebar Toggle -->
        <?php include dirname(__DIR__, 3) . '/Core/Views/partials/mobile_nav.php'; ?>
        
        <!-- Sidebar -->
        <?php $active = 'mail_dashboard'; include __DIR__ . '/partials/sidebar_mail.php'; ?>

        <!-- Main Content -->
        <main class="erp-main">
            
            <!-- Hero Banner -->
            <section class="erp-hero">
                <div class="erp-hero-top">
                    <div>
                        <div class="label" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--erp-gold); margin-bottom: 8px; font-weight: bold;">
                            <ion-icon name="mail-unread" style="vertical-align: middle; margin-right: 5px;"></ion-icon> Email Marketing
                        </div>
                        <h1>Dashboard Overview</h1>
                        <p class="subtitle">Manage your campaigns, track engagement, and grow your audience.</p>
                    </div>
                    <div>
                        <a href="/mail/campaigns/create" class="hero-btn">
                            <ion-icon name="add-circle"></ion-icon> Create Campaign
                        </a>
                    </div>
                </div>
            </section>

            <!-- Dashboard Content -->
            <div class="erp-content">
                
                <div class="section-header" style="margin-bottom: 25px;">
                    <ion-icon name="stats-chart"></ion-icon> Performance Metrics
                </div>

                <div class="dashboard-grid">
                    
                    <div class="stat-card-modern">
                        <div class="stat-top">
                            <div class="stat-icon">
                                <ion-icon name="people-outline"></ion-icon>
                            </div>
                            <div class="stat-title">Total Subscribers</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['subscribers']) ?></div>
                    </div>

                    <div class="stat-card-modern">
                        <div class="stat-top">
                            <div class="stat-icon">
                                <ion-icon name="send-outline"></ion-icon>
                            </div>
                            <div class="stat-title">Emails Sent</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['emails_sent']) ?></div>
                    </div>

                    <div class="stat-card-modern">
                        <div class="stat-top">
                            <div class="stat-icon">
                                <ion-icon name="paper-plane-outline"></ion-icon>
                            </div>
                            <div class="stat-title">Total Campaigns</div>
                        </div>
                        <div class="stat-value"><?= number_format($stats['campaigns']) ?></div>
                    </div>

                </div>

                <div class="section-header" style="margin-top: 40px; margin-bottom: 25px;">
                    <ion-icon name="apps-outline"></ion-icon> Quick Links
                </div>

                <div class="dashboard-grid">
                    <a href="/mail/campaigns" class="quick-link-card">
                        <div class="ql-icon"><ion-icon name="paper-plane-outline"></ion-icon></div>
                        <div class="ql-info">
                            <div class="ql-title">Campaigns</div>
                            <div class="ql-desc">Send targeted emails</div>
                        </div>
                    </a>
                    <a href="/mail/lists" class="quick-link-card">
                        <div class="ql-icon"><ion-icon name="people-outline"></ion-icon></div>
                        <div class="ql-info">
                            <div class="ql-title">Audiences</div>
                            <div class="ql-desc">Manage your contacts</div>
                        </div>
                    </a>
                    <a href="/mail/sequences" class="quick-link-card">
                        <div class="ql-icon"><ion-icon name="git-network-outline"></ion-icon></div>
                        <div class="ql-info">
                            <div class="ql-title">Automations</div>
                            <div class="ql-desc">Behavior-driven flows</div>
                        </div>
                    </a>
                    <a href="/mail/templates" class="quick-link-card">
                        <div class="ql-icon"><ion-icon name="color-palette-outline"></ion-icon></div>
                        <div class="ql-info">
                            <div class="ql-title">Templates</div>
                            <div class="ql-desc">Design beautiful layouts</div>
                        </div>
                    </a>
                    <a href="/mail/forms" class="quick-link-card">
                        <div class="ql-icon"><ion-icon name="reader-outline"></ion-icon></div>
                        <div class="ql-info">
                            <div class="ql-title">Forms</div>
                            <div class="ql-desc">Grow your list</div>
                        </div>
                    </a>
                    <a href="/mail/settings" class="quick-link-card">
                        <div class="ql-icon"><ion-icon name="settings-outline"></ion-icon></div>
                        <div class="ql-info">
                            <div class="ql-title">Settings</div>
                            <div class="ql-desc">Configure your mail</div>
                        </div>
                    </a>
                </div>

            </div>
        </main>
    </div>
</body>
</html>
