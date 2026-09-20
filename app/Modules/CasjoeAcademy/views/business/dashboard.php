<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Business Dashboard | Casjoe Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* Netflix-style Enterprise Dashboard Aesthetics */
        body, html {
            background-color: #0f172a; /* Deep dark blue/black */
            color: #f8fafc;
        }
        .main-content { 
            margin-left: 270px; 
            padding: 40px;
        }
        .sidebar {
            background: #0f172a;
            border-right: 1px solid #1e293b;
        }
        .top-bar h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }
        .top-bar {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Dashboard Grid & Cards */
        .dashboard-grid { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card-modern {
            background: #1e293b;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #334155;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .stat-card-modern:hover {
            transform: translateY(-5px);
        }
        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, #10b981 0%, #3b82f6 100%);
        }
        .stat-card-modern:nth-child(2)::before { background: linear-gradient(90deg, #f59e0b 0%, #ef4444 100%); }
        .stat-card-modern:nth-child(3)::before { background: linear-gradient(90deg, #8b5cf6 0%, #ec4899 100%); }

        .stat-val { font-size: 2.5rem; font-weight: bold; color: white; margin-bottom: 5px; }
        .stat-lbl { color: #94a3b8; font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; }
        .stat-icon { position: absolute; right: 20px; bottom: 20px; font-size: 4rem; opacity: 0.1; color: white; }

        /* Tables */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .dark-table-container {
            background: #1e293b;
            border-radius: 12px;
            border: 1px solid #334155;
            overflow: hidden;
            margin-bottom: 50px;
        }
        .dark-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .dark-table th {
            background: #0f172a;
            padding: 15px 20px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            border-bottom: 1px solid #334155;
        }
        .dark-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #334155;
            color: #e2e8f0;
            vertical-align: middle;
        }
        .dark-table tr:last-child td { border-bottom: none; }
        .dark-table tr:hover { background: rgba(255,255,255,0.02); }

        /* Progress Bar */
        .progress-wrapper { background: #334155; height: 8px; width: 120px; border-radius: 4px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 10px; }
        .progress-fill { height: 100%; background: #3b82f6; border-radius: 4px; }
        .progress-fill.high { background: #10b981; }
        .progress-fill.low { background: #ef4444; }

        .btn-modern {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-modern:hover { background: #2563eb; color: white; }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar { left: -270px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
        }
    </style>
</head>
<body>
    <div class="app-container" style="background: transparent;">
        <!-- Mobile Sidebar Toggle -->
        <?php include dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
        
        <!-- Sidebar -->
        <?php $active = 'academy_business'; include __DIR__ . '/../partials/sidebar_academy.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1>B2B Enterprise Training</h1>
                <div class="user-profile">
                    <div class="user-avatar" style="background: #3b82f6;">AD</div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="dashboard-grid">
                <div class="stat-card-modern">
                    <div class="stat-val"><?= $totalStaff ?></div>
                    <div class="stat-lbl">Total Staff</div>
                    <ion-icon name="people" class="stat-icon"></ion-icon>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-val"><?= $totalLicenses ?></div>
                    <div class="stat-lbl">Licenses Owned</div>
                    <ion-icon name="albums" class="stat-icon"></ion-icon>
                </div>
                <div class="stat-card-modern">
                    <div class="stat-val"><?= $activeLearners ?></div>
                    <div class="stat-lbl">Certified Staff</div>
                    <ion-icon name="medal" class="stat-icon"></ion-icon>
                </div>
            </div>

            <!-- Team Leaderboard / Progress -->
            <div class="section-header">
                <ion-icon name="podium" style="color: #f59e0b;"></ion-icon> Team Leaderboard
            </div>
            
            <div class="dark-table-container">
                <?php if (empty($team)): ?>
                    <div style="padding: 40px; text-align: center; color: #94a3b8;">No team members found.</div>
                <?php else: ?>
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>Staff Member</th>
                                <th>Active Courses</th>
                                <th>Avg Completion</th>
                                <th>Total Points</th>
                                <th>Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($team as $member): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: white;"><?= htmlspecialchars($member['name']) ?></div>
                                        <div style="font-size: 0.85rem; color: #94a3b8;"><?= htmlspecialchars($member['email']) ?></div>
                                    </td>
                                    <td><?= $member['active_courses'] ?></td>
                                    <td>
                                        <?php 
                                            $prog = round($member['avg_progress']); 
                                            $progClass = $prog >= 80 ? 'high' : ($prog <= 30 && $prog > 0 ? 'low' : '');
                                        ?>
                                        <div class="progress-wrapper">
                                            <div class="progress-fill <?= $progClass ?>" style="width: <?= $prog ?>%;"></div>
                                        </div>
                                        <span style="font-size: 0.9rem; color: #cbd5e1;"><?= $prog ?>%</span>
                                    </td>
                                    <td style="font-weight: bold; color: #f59e0b;"><?= number_format($member['total_points']) ?></td>
                                    <td>
                                        <?php
                                            $rank = \App\Modules\CasjoeAcademy\Services\GamificationService::getUserStats($member['id'], $this->tenantId)['rank'];
                                        ?>
                                        <span style="background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;"><?= $rank ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- License Management -->
            <div class="section-header" style="justify-content: space-between; display: flex;">
                <div><ion-icon name="library" style="color: #3b82f6;"></ion-icon> Training Inventory</div>
                <a href="/academy/business/marketplace" class="btn-modern" style="background: #e11d48;"><ion-icon name="cart"></ion-icon> Buy More Seats</a>
            </div>

            <div class="dark-table-container">
                <?php if (empty($licenses)): ?>
                    <div style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                        <ion-icon name="albums-outline" style="font-size: 4rem; opacity: 0.5; margin-bottom: 15px;"></ion-icon>
                        <p style="font-size: 1.1rem;">You don't own any training licenses yet.</p>
                        <a href="/academy/business/marketplace" class="btn-modern" style="margin-top: 15px;">Browse Catalog</a>
                    </div>
                <?php else: ?>
                    <table class="dark-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Seats (Used / Total)</th>
                                <th>Utilization</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($licenses as $l): ?>
                                <?php 
                                    $utilization = $l['seats_total'] > 0 ? round(($l['seats_used'] / $l['seats_total']) * 100) : 0; 
                                    $utilClass = $utilization >= 90 ? 'low' : 'high'; // Red if almost full
                                ?>
                                <tr>
                                    <td style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 50px; height: 50px; border-radius: 8px; background-image: url('<?= htmlspecialchars($l['thumbnail']) ?>'); background-size: cover; background-position: center; border: 1px solid #334155;"></div>
                                        <span style="font-weight: 600; font-size: 1.05rem;"><?= htmlspecialchars($l['title']) ?></span>
                                    </td>
                                    <td>
                                        <span style="font-weight: bold; color: <?= $l['seats_used'] >= $l['seats_total'] ? '#ef4444' : '#10b981' ?>;">
                                            <?= $l['seats_used'] ?>
                                        </span> 
                                        <span style="color: #94a3b8;">/ <?= $l['seats_total'] ?></span>
                                    </td>
                                    <td>
                                        <div class="progress-wrapper" style="width: 150px;">
                                            <div class="progress-fill <?= $utilClass ?>" style="width: <?= $utilization ?>%;"></div>
                                        </div>
                                        <span style="font-size: 0.9rem; color: #cbd5e1;"><?= $utilization ?>%</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if ($l['seats_used'] >= $l['seats_total']): ?>
                                            <span style="color: #ef4444; font-size: 0.9rem; font-weight: bold; padding: 8px 15px; background: rgba(239, 68, 68, 0.1); border-radius: 6px;">Fully Assigned</span>
                                        <?php else: ?>
                                            <a href="/academy/business/assign?license_id=<?= $l['id'] ?>" class="btn-modern"><ion-icon name="person-add"></ion-icon> Assign Staff</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
