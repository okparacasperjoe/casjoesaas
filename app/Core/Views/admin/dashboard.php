<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Admin Dashboard | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* ── ENFORCE DEEP DARK BACKGROUND OVERRIDING GLOBAL .main-content WHITE THEME ── */
        body, main.main-content, body:has(.main-content) {
            background: #030413 !important;
            background-image: radial-gradient(circle at 15% 20%, rgba(0, 0, 102, 0.35) 0%, transparent 40%),
                              radial-gradient(circle at 85% 80%, rgba(255, 166, 0, 0.12) 0%, transparent 40%) !important;
            color: #eeeeee !important;
            min-height: 100vh;
        }

        .top-bar {
            background: rgba(5, 6, 20, 0.85) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            padding: 18px 30px !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar h2 {
            color: #ffffff !important;
            margin: 0 !important;
            font-weight: 700 !important;
            font-size: 1.5rem !important;
        }
        .user-profile span {
            color: #cbd5e1 !important;
            font-weight: 500;
        }

        @media screen and (min-width: 992px) {
            main.main-content {
                margin-left: 270px !important;
                width: calc(100% - 270px) !important;
                flex: 1 !important;
                box-sizing: border-box !important;
            }
        }

        /* ── ORIGINAL SUPER ADMIN CARD GRID WITH SLEEK DARK GLASSMORPHISM (COMPACT SIZING) ── */
        .admin-dashboard { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); 
            gap: 20px; 
            padding: 24px 30px; 
            width: 100%;
            box-sizing: border-box;
        }
        .admin-card { 
            background: rgba(18, 20, 38, 0.7) !important; 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 18px 14px !important; 
            border-radius: 14px !important; 
            border: 1px solid rgba(255, 255, 255, 0.08) !important; 
            text-align: center; 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            text-decoration: none; 
            color: #eeeeee !important; 
            display: block; 
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3) !important;
            position: relative;
        }
        .admin-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5), 0 0 16px rgba(255, 166, 0, 0.15) !important; 
            border-color: rgba(255, 166, 0, 0.4) !important; 
            background: rgba(24, 27, 50, 0.85) !important;
        }
        .admin-icon { 
            font-size: 2.2rem; 
            margin-bottom: 10px; 
            display: inline-block;
            transition: transform 0.3s ease;
        }
        .admin-card:hover .admin-icon {
            transform: scale(1.1);
        }
        .admin-card h3 { 
            margin: 0 0 6px 0 !important; 
            color: #ffffff !important; 
            font-size: 0.95rem !important;
            font-weight: 700 !important;
        }
        .admin-card p { 
            color: #94a3b8 !important; 
            font-size: 0.78rem !important; 
            margin: 0 !important; 
        }
        .stat-value { 
            font-size: 1.45rem !important; 
            font-weight: 800 !important; 
            color: #ffffff !important; 
            margin: 6px 0 !important; 
            display: block;
            letter-spacing: -0.5px;
        }

        /* ── RECENT SIGNUPS & DEPOSITS BOTTOM SECTION ── */
        .recent-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 24px; 
            margin: 10px 30px 50px; 
        }
        .recent-card {
            background: rgba(18, 20, 38, 0.7) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 16px !important;
            padding: 26px !important;
            text-align: left !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
        }
        .recent-card h3 {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important; 
            padding-bottom: 14px !important; 
            margin: 0 0 16px 0 !important;
            color: #ffffff !important;
            font-size: 1.2rem !important;
            font-weight: 700 !important;
        }
        .recent-list {
            list-style: none; 
            padding: 0; 
            margin: 0;
        }
        .recent-item {
            padding: 12px 0; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.06); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        .recent-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .recent-item strong {
            color: #ffffff !important;
            font-size: 0.95rem;
        }
        .recent-item small {
            color: #94a3b8 !important;
            font-size: 0.82rem;
        }
        /* ── PULSING DOT FOR ONLINE INDICATOR ── */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.4); }
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>Super Admin Dashboard</h2>
            <div class="user-profile">
                <span>Welcome, <?= htmlspecialchars($adminName ?? 'Admin') ?></span>
            </div>
        </div>

        <div class="admin-dashboard">
            <!-- User Management -->
            <a href="/<?= ADMIN_PATH ?>/users" class="admin-card">
                <ion-icon name="people-circle-outline" class="admin-icon" style="color: #6c5ce7;"></ion-icon>
                <h3>Total Users</h3>
                <span class="stat-value"><?= number_format($stats['users']) ?></span>
                <p>Registered Accounts</p>
            </a>

            <!-- Revenue -->
            <div class="admin-card">
                <ion-icon name="cash-outline" class="admin-icon" style="color: #00b894;"></ion-icon>
                <h3>Total Revenue</h3>
                <span class="stat-value">₦<?= number_format($stats['revenue'], 2) ?></span>
                <p>Successful Transactions</p>
            </div>

            <!-- MRR -->
            <div class="admin-card">
                <ion-icon name="trending-up-outline" class="admin-icon" style="color: #0984e3;"></ion-icon>
                <h3>Est. MRR</h3>
                <span class="stat-value">₦<?= number_format($stats['mrr'], 2) ?></span>
                <p>Monthly Recurring Revenue</p>
            </div>

            <!-- Workspaces -->
            <div class="admin-card">
                <ion-icon name="briefcase-outline" class="admin-icon" style="color: #e84393;"></ion-icon>
                <h3>Workspaces</h3>
                <span class="stat-value"><?= number_format($stats['tenants']) ?></span>
                <p>Active Tenant Accounts</p>
            </div>

            <!-- AI Usage -->
            <div class="admin-card">
                <ion-icon name="hardware-chip-outline" class="admin-icon" style="color: #6c5ce7;"></ion-icon>
                <h3>AI Usage</h3>
                <span class="stat-value"><?= number_format($stats['ai_tokens']) ?></span>
                <p>Tokens Consumed</p>
            </div>

            <!-- Support Tickets -->
            <div class="admin-card">
                <ion-icon name="help-buoy-outline" class="admin-icon" style="color: #d63031;"></ion-icon>
                <h3>Support Tickets</h3>
                <span class="stat-value"><?= number_format($stats['support_tickets']) ?></span>
                <p>Open Requests</p>
            </div>

            <!-- Paid Subscribers -->
            <div class="admin-card">
                <ion-icon name="checkmark-done-circle-outline" class="admin-icon" style="color: #10ac84;"></ion-icon>
                <h3>Paid Subscribers</h3>
                <span class="stat-value"><?= number_format($stats['paid_subscribers']) ?></span>
                <p>Active Premium Plans</p>
            </div>

            <!-- Unpaid Subscribers -->
            <div class="admin-card">
                <ion-icon name="close-circle-outline" class="admin-icon" style="color: #ff7675;"></ion-icon>
                <h3>Unpaid Subscribers</h3>
                <span class="stat-value"><?= number_format($stats['unpaid_subscribers']) ?></span>
                <p>Trial / Inactive Plans</p>
            </div>

            <!-- Virtual Cards -->
            <a href="/<?= ADMIN_PATH ?>/cards" class="admin-card">
                <ion-icon name="card-outline" class="admin-icon" style="color: #0984e3;"></ion-icon>
                <h3>Virtual Cards</h3>
                <span class="stat-value"><?= number_format($stats['cards']) ?></span>
                <p>Active Virtual Cards</p>
            </a>

            <!-- Pending Deposits -->
            <a href="/<?= ADMIN_PATH ?>/deposits" class="admin-card">
                <ion-icon name="arrow-down-circle-outline" class="admin-icon" style="color: #eccc68;"></ion-icon>
                <h3>Pending Deposits</h3>
                <span class="stat-value"><?= number_format($stats['pending_deposits']) ?></span>
                <p>Awaiting Approval</p>
            </a>

            <!-- Pending Withdrawals -->
            <a href="/<?= ADMIN_PATH ?>/withdrawals" class="admin-card">
                <ion-icon name="arrow-up-circle-outline" class="admin-icon" style="color: #ff6b6b;"></ion-icon>
                <h3>Pending Withdrawals</h3>
                <span class="stat-value"><?= number_format($stats['pending_withdrawals']) ?></span>
                <p>Awaiting Processing</p>
            </a>

            <!-- Active Apps -->
            <div class="admin-card">
                <ion-icon name="grid-outline" class="admin-icon" style="color: #fdcb6e;"></ion-icon>
                <h3>Active Apps</h3>
                <span class="stat-value"><?= number_format($stats['apps']) ?></span>
                <p>Modules Installed</p>
            </div>

            <!-- System Settings -->
            <a href="/<?= ADMIN_PATH ?>/settings" class="admin-card">
                <ion-icon name="settings-outline" class="admin-icon" style="color: #636e72;"></ion-icon>
                <h3>Settings</h3>
                <p style="margin-top: 15px;">Configure Platform</p>
            </a>

            <!-- Broadcasts -->
            <a href="/<?= ADMIN_PATH ?>/broadcast" class="admin-card">
                <ion-icon name="megaphone-outline" class="admin-icon" style="color: #e17055;"></ion-icon>
                <h3>Broadcasts</h3>
                <p style="margin-top: 15px;">Announcements</p>
            </a>

            <!-- System Health -->
            <div class="admin-card" style="opacity: 0.75;">
                <ion-icon name="pulse-outline" class="admin-icon" style="color: #b2bec3;"></ion-icon>
                <h3>System Health</h3>
                <span class="stat-value" style="font-size: 1.5rem; color: #2ed573;">Operational</span>
            </div>

            <!-- ── VISITOR ANALYTICS ── -->
            <div class="admin-card">
                <ion-icon name="eye-outline" class="admin-icon" style="color: #00cec9;"></ion-icon>
                <h3>Total Visitors</h3>
                <span class="stat-value"><?= number_format($stats['total_visitors']) ?></span>
                <p>All-time page views</p>
            </div>

            <div class="admin-card">
                <ion-icon name="today-outline" class="admin-icon" style="color: #ffeaa7;"></ion-icon>
                <h3>Today's Visitors</h3>
                <span class="stat-value"><?= number_format($stats['today_visitors']) ?></span>
                <p>Page views today</p>
            </div>

            <div class="admin-card">
                <ion-icon name="finger-print-outline" class="admin-icon" style="color: #a29bfe;"></ion-icon>
                <h3>Unique Visitors</h3>
                <span class="stat-value"><?= number_format($stats['unique_visitors']) ?></span>
                <p>Distinct IPs (all-time)</p>
            </div>

            <div class="admin-card" style="border-color: rgba(46, 213, 115, 0.3) !important;">
                <ion-icon name="radio-outline" class="admin-icon" style="color: #2ed573;"></ion-icon>
                <h3>Online Now <span style="display:inline-block;width:8px;height:8px;background:#2ed573;border-radius:50%;margin-left:6px;animation:pulse-dot 1.5s ease infinite;vertical-align:middle;"></span></h3>
                <span class="stat-value" style="color: #2ed573;"><?= number_format($stats['online_now']) ?></span>
                <p>Active in last 5 min</p>
            </div>
        </div>

        <div class="recent-grid">
            <div class="recent-card">
                <h3>Recent Signups</h3>
                <?php if (empty($recentUsers)): ?>
                    <p style="color: #94a3b8; margin: 0;">No recent signups.</p>
                <?php else: ?>
                    <ul class="recent-list">
                        <?php foreach ($recentUsers as $ru): ?>
                            <li class="recent-item">
                                <div>
                                    <strong><?= htmlspecialchars($ru['name'] ?? '') ?></strong><br>
                                    <small><?= htmlspecialchars($ru['email'] ?? '') ?></small>
                                </div>
                                <span style="font-size: 0.8rem; color: var(--secondary); font-weight: 600;"><?= date('M j, Y', strtotime($ru['created_at'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="recent-card">
                <h3>Recent Deposits</h3>
                <?php if (empty($recentDeposits)): ?>
                    <p style="color: #94a3b8; margin: 0;">No recent deposits.</p>
                <?php else: ?>
                    <ul class="recent-list">
                        <?php foreach ($recentDeposits as $rd): ?>
                            <li class="recent-item">
                                <div>
                                    <strong><?= htmlspecialchars(($rd['currency'] ?? 'NGN') . ' ' . number_format($rd['amount'], 2)) ?></strong><br>
                                    <small style="color: <?= $rd['status'] === 'successful' ? '#2ed573' : '#ffa502' ?> !important; font-weight: 600;"><?= ucfirst(htmlspecialchars($rd['status'])) ?></small>
                                </div>
                                <span style="font-size: 0.8rem; color: var(--secondary); font-weight: 600;"><?= date('M j, h:i A', strtotime($rd['created_at'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
