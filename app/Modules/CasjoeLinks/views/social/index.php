<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Planner &amp; Calendar | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/casjoe_links.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .channel-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13.5px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
            color: var(--cl-text);
            border: 1px solid var(--cl-border);
            transition: all 0.2s;
        }
        html.light-theme .channel-chip {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        .channel-chip.active {
            border-color: rgba(16, 185, 129, 0.4);
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
        }
        html.light-theme .channel-chip.active {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        .platform-icon-sm {
            font-size: 17px;
        }
        .platform-icon-sm.facebook { color: #1877F2; }
        .platform-icon-sm.instagram { color: #E1306C; }
        .platform-icon-sm.twitter { color: #f8fafc; }
        html.light-theme .platform-icon-sm.twitter { color: #000000; }
        .platform-icon-sm.linkedin { color: #0A66C2; }
        .platform-icon-sm.tiktok { color: #f8fafc; }
        html.light-theme .platform-icon-sm.tiktok { color: #000000; }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            display: inline-block;
        }
        
        .post-content-preview {
            font-weight: 500;
            color: var(--cl-text) !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 320px;
        }
        .post-date-cell {
            font-size: 13px;
            color: var(--cl-text-muted);
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            #casjoe-links-app .cl-dashboard-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
                padding: 24px 20px;
            }
            #casjoe-links-app .cl-dashboard-hero .cl-hero-actions {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            #casjoe-links-app .cl-dashboard-hero .cl-hero-actions a {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php $active = 'social_planner'; include dirname(__DIR__) . '/partials/sidebar_links.php'; ?>
        
        <main class="main-content">
            <div id="casjoe-links-app">
                <!-- Hero Banner Section -->
                <div class="cl-dashboard-hero">
                    <div class="cl-hero-content">
                        <div class="cl-hero-badge">
                            <ion-icon name="share-social-outline"></ion-icon> Social Planner &amp; Studio
                        </div>
                        <h1>Social Planner &amp; Content Calendar</h1>
                        <p>Plan, auto-publish, and schedule high-converting campaigns across Facebook, Instagram, Twitter/X, LinkedIn, and TikTok with built-in AI copywriting.</p>
                    </div>
                    <div class="cl-hero-actions" style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="/links/social/calendar" class="cl-btn" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff !important; box-shadow: none;">
                            <ion-icon name="calendar-outline"></ion-icon> Content Calendar
                        </a>
                        <a href="/links/social/composer" class="cl-btn">
                            <ion-icon name="create-outline"></ion-icon> Compose Post
                        </a>
                    </div>
                </div>

                <?php if (!empty($_GET['success'])): ?>
                    <div class="cl-pill cl-pill-success" style="width: 100%; box-sizing: border-box; padding: 14px 20px; font-size: 14px; margin-bottom: 24px; border-radius: 12px;">
                        <ion-icon name="checkmark-circle-outline" style="font-size: 20px;"></ion-icon>
                        <?= $_GET['success'] === 'posted' ? 'Social campaign saved and queued successfully!' : 'Action completed successfully.' ?>
                    </div>
                <?php endif; ?>

                <!-- Stats Overview -->
                <div class="cl-section">
                    <ion-icon name="stats-chart-outline"></ion-icon>
                    <h2>Performance Overview</h2>
                </div>

                <div class="cl-stats-grid">
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Scheduled Posts</div>
                            <div class="cl-stat-icon icon-amber">
                                <ion-icon name="time-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= (int)$stats['total_scheduled'] ?></div>
                    </div>
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Published Posts</div>
                            <div class="cl-stat-icon icon-green">
                                <ion-icon name="paper-plane-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= (int)$stats['total_published'] ?></div>
                    </div>
                    <div class="cl-stat-card">
                        <div class="cl-stat-top">
                            <div class="cl-stat-title">Active Channels</div>
                            <div class="cl-stat-icon icon-blue">
                                <ion-icon name="share-social-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="cl-stat-value"><?= (int)$stats['connected_accounts'] ?></div>
                    </div>
                </div>

                <!-- Active Channels Bar -->
                <div class="cl-panel" style="margin-bottom: 30px; padding: 20px 24px !important;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span style="font-size: 13px; font-weight: 700; color: var(--cl-text-muted); text-transform: uppercase; letter-spacing: 1px;">
                                Active Channels:
                            </span>
                            <?php foreach ($connectedAccounts as $account): ?>
                                <div class="channel-chip active">
                                    <ion-icon name="logo-<?= $account['platform'] === 'twitter' ? 'twitter' : $account['platform'] ?>" class="platform-icon-sm <?= $account['platform'] ?>"></ion-icon>
                                    <span><?= htmlspecialchars($account['account_name']) ?></span>
                                    <span class="pulse-dot"></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/links/social/composer" class="cl-link">
                            <ion-icon name="add-circle-outline"></ion-icon> Create New Campaign
                        </a>
                    </div>
                </div>

                <!-- Recent Posts List -->
                <div class="cl-section" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <ion-icon name="list-outline"></ion-icon>
                        <h2>Scheduled &amp; Published Posts</h2>
                    </div>
                    <a href="/links/social/composer" class="cl-btn cl-btn-sm">
                        <ion-icon name="add-outline"></ion-icon> New Post
                    </a>
                </div>

                <div class="cl-panel" style="padding: 0 !important; overflow: hidden;">
                    <?php if (empty($recentPosts)): ?>
                        <div class="cl-empty">
                            <ion-icon name="calendar-outline"></ion-icon>
                            <h3>No posts scheduled yet</h3>
                            <p>Create high-converting AI social campaigns and auto-publish across all your accounts.</p>
                            <a href="/links/social/composer" class="cl-btn">
                                <ion-icon name="sparkles-outline"></ion-icon> Create First AI Post
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="cl-table">
                                <thead>
                                    <tr>
                                        <th>Post Content</th>
                                        <th>Channels</th>
                                        <th>Status</th>
                                        <th>Date &amp; Time</th>
                                        <th>Attached Link</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPosts as $p): ?>
                                        <tr>
                                            <td style="max-width: 320px;">
                                                <div class="post-content-preview">
                                                    <?= htmlspecialchars(mb_strimwidth($p['content'], 0, 85, '...')) ?>
                                                </div>
                                                <?php if (!empty($p['ai_generated'])): ?>
                                                    <div style="font-size: 11px; color: #a855f7; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                                                        <ion-icon name="sparkles"></ion-icon> AI Generated Copy
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                    <?php 
                                                    $platforms = is_array($p['platforms']) ? $p['platforms'] : [];
                                                    foreach ($platforms as $pl): ?>
                                                        <span title="<?= ucfirst($pl) ?>">
                                                            <ion-icon name="logo-<?= $pl === 'twitter' ? 'twitter' : $pl ?>" class="platform-icon-sm <?= $pl ?>"></ion-icon>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($p['status'] === 'published'): ?>
                                                    <span class="cl-pill cl-pill-success">Published</span>
                                                <?php elseif ($p['status'] === 'scheduled'): ?>
                                                    <span class="cl-pill cl-pill-info">Scheduled</span>
                                                <?php else: ?>
                                                    <span class="cl-pill">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="post-date-cell">
                                                <?= !empty($p['scheduled_at']) ? date('M d, Y h:i A', strtotime($p['scheduled_at'])) : date('M d, Y h:i A', strtotime($p['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($p['link_url'])): ?>
                                                    <a href="<?= htmlspecialchars($p['link_url']) ?>" target="_blank" class="cl-link" style="font-size: 13.5px;">
                                                        <?= ucfirst($p['link_type'] ?: 'Link') ?> <ion-icon name="open-outline"></ion-icon>
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: var(--cl-text-muted); font-size: 13px;">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <div class="cl-actions" style="justify-content: flex-end;">
                                                    <form method="POST" action="/links/social/delete" onsubmit="return confirm('Delete this post?');" style="margin: 0;">
                                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                        <button type="submit" class="cl-icon-btn delete" title="Delete Post">
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
        </main>
    </div>
</body>
</html>
