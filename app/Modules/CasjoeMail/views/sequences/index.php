<?php $active = 'mail_automations'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Sequences | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mail_app.css?v=2.1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .metric-group {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            padding: 15px 0;
            border-top: 1px solid var(--erp-border);
            border-bottom: 1px solid var(--erp-border);
        }
        .metric-item {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .metric-label {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--erp-text-muted);
            margin-bottom: 4px;
            font-weight: 600;
        }
        .metric-value {
            font-size: 18px;
            font-weight: 700;
            color: white;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar_mail.php'; ?>

        <main class="erp-main">
            <!-- Mobile Nav -->
            <?php include __DIR__ . '/../../../../Core/Views/partials/mobile_nav.php'; ?>

            <div class="erp-hero">
                <div class="erp-hero-top">
                    <div>
                        <h1>Smart Sequences</h1>
                        <p class="subtitle">Automate your follow-ups with behavior-driven email flows.</p>
                    </div>
                    <a href="/mail/sequences/create" class="hero-btn">
                        <ion-icon name="add-outline"></ion-icon> New Sequence
                    </a>
                </div>
            </div>

            <div class="erp-content">
                <?php if (isset($_GET['success'])): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 10px;">
                        <ion-icon name="checkmark-circle"></ion-icon>
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sequences)): ?>
                    <div class="section-header" style="margin-bottom: 25px;">
                        <ion-icon name="stats-chart"></ion-icon> Sequence Metrics
                    </div>
                    
                    <div class="dashboard-grid">
                        
                        <div class="stat-card-modern">
                            <div class="stat-top">
                                <div class="stat-icon">
                                    <ion-icon name="git-network-outline"></ion-icon>
                                </div>
                                <div class="stat-title">Active Sequences</div>
                            </div>
                            <div class="stat-value"><?= count($sequences) ?></div>
                        </div>
                        
                        <?php $totalEnrollments = array_sum(array_column($sequences, 'enrolled_count')); ?>
                        <div class="stat-card-modern">
                            <div class="stat-top">
                                <div class="stat-icon">
                                    <ion-icon name="people-outline"></ion-icon>
                                </div>
                                <div class="stat-title">Total Enrolled</div>
                            </div>
                            <div class="stat-value"><?= $totalEnrollments ?></div>
                        </div>

                        <div class="stat-card-modern">
                            <div class="stat-top">
                                <div class="stat-icon">
                                    <ion-icon name="flash-outline"></ion-icon>
                                </div>
                                <div class="stat-title">Processing Mode</div>
                            </div>
                            <div class="stat-value" style="font-size: 2rem;">Auto</div>
                        </div>
                    </div>

                    <div class="section-header">
                        <ion-icon name="git-network-outline"></ion-icon> All Automations
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">
                        <?php foreach ($sequences as $seq): ?>
                            <?php
                            $completionRate = $seq['enrolled_count'] > 0 
                                ? round(($seq['completed_count'] / $seq['enrolled_count']) * 100) 
                                : 0;
                            $statusClass = $seq['is_active'] ? 'badge-active' : 'badge-pending';
                            $statusText = $seq['is_active'] ? 'Active' : 'Paused';
                            ?>
                            <div class="module-card" style="padding: 25px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <h3 style="margin: 0; font-size: 18px; color: white; text-transform:none; letter-spacing:0; font-weight:700;">
                                        <?= htmlspecialchars($seq['name']) ?>
                                    </h3>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </div>
                                
                                <p style="color: var(--erp-text-muted); font-size: 14px; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($seq['description'] ?: 'No description provided.') ?>
                                </p>

                                <div class="metric-group">
                                    <div class="metric-item">
                                        <span class="metric-label">Steps</span>
                                        <span class="metric-value"><?= $seq['step_count'] ?></span>
                                    </div>
                                    <div class="metric-item">
                                        <span class="metric-label">Enrolled</span>
                                        <span class="metric-value"><?= $seq['enrolled_count'] ?></span>
                                    </div>
                                    <div class="metric-item">
                                        <span class="metric-label">Completion</span>
                                        <span class="metric-value" style="color: <?= $completionRate > 50 ? '#10b981' : '#f5a623' ?>;">
                                            <?= $completionRate ?>%
                                        </span>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                    <div style="display: flex; gap: 10px;">
                                        <a href="/mail/sequences/edit?id=<?= $seq['id'] ?>" class="btn-icon" title="Edit Sequence">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </a>
                                        <a href="/mail/sequences/enrollments?id=<?= $seq['id'] ?>" class="btn-icon" title="View Enrollments">
                                            <ion-icon name="people-outline"></ion-icon>
                                        </a>
                                        <form action="/mail/sequences/duplicate" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $seq['id'] ?>">
                                            <button type="submit" class="btn-icon" title="Duplicate">
                                                <ion-icon name="copy-outline"></ion-icon>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <form action="/mail/sequences/delete" method="POST" onsubmit="return confirm('Are you sure? This will stop all active enrollments.');">
                                        <input type="hidden" name="id" value="<?= $seq['id'] ?>">
                                        <button type="submit" class="btn-icon danger" title="Delete Sequence">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="module-card" style="text-align: center; padding: 60px 20px; align-items: center;">
                        <div class="module-icon-wrap" style="background: rgba(255,166,0,0.1); color: var(--erp-gold); font-size: 3rem; width: 80px; height: 80px; margin: 0 auto 25px;">
                            <ion-icon name="git-network-outline"></ion-icon>
                        </div>
                        <h2 style="color: white; margin-bottom: 15px; font-size: 1.5rem;">Create Your First Sequence</h2>
                        <p style="color: var(--erp-text-muted); max-width: 450px; margin: 0 auto 30px; line-height: 1.6;">
                            Turn subscribers into customers with automated follow-up emails. Set it up once, and let it run forever.
                        </p>
                        <a href="/mail/sequences/create" class="hero-btn">
                            Start from Scratch
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
