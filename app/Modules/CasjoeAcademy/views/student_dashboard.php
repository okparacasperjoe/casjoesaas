<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>My Learning | Casjoe Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* Netflix-style Dashboard Aesthetics */
        .academy-main {
            background-color: #0f172a; /* Deep dark blue/black */
            color: #f8fafc;
            min-height: 100vh;
            padding-bottom: 50px;
        }
        .academy-header {
            padding: 40px;
            background: linear-gradient(180deg, rgba(15,23,42,1) 0%, rgba(15,23,42,0) 100%);
            margin-bottom: 20px;
        }
        .academy-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            color: #fff;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 0 40px;
        }
        .course-card-modern {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: #1e293b;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            color: white;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .course-card-modern:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
            z-index: 10;
        }
        .course-thumb-modern {
            width: 100%;
            aspect-ratio: 16 / 9;
            background-size: cover;
            background-position: center;
            background-color: #334155;
            position: relative;
        }
        .course-thumb-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(15,23,42,0.9) 0%, rgba(15,23,42,0) 50%);
        }
        .progress-bar-modern {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255,255,255,0.2);
        }
        .progress-fill-modern {
            height: 100%;
            background: #e11d48; /* Premium red accent like Netflix */
            box-shadow: 0 0 10px #e11d48;
        }
        .course-info-modern {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .course-info-modern h3 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
            line-height: 1.4;
        }
        .course-meta {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .play-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }
        .course-card-modern:hover .play-btn {
            background: #e11d48;
            border-color: #e11d48;
            color: white;
        }
        
        /* Gamification Styles */
        .gamification-bar {
            display: flex;
            gap: 20px;
            padding: 0 40px;
            margin-top: -10px;
            margin-bottom: 30px;
        }
        .gamification-card {
            background: #1e293b;
            border-radius: 12px;
            padding: 20px;
            flex: 1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            border: 1px solid #334155;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .rank-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
        }
        .gamification-info h4 {
            margin: 0 0 5px 0;
            color: #94a3b8;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .gamification-info .points-val {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff;
            margin: 0;
        }
        .badge-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .badge-item {
            background: #334155;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #cbd5e1;
            border: 1px solid #475569;
        }
        .cert-item {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-left: 4px solid #10b981;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .academy-header {
                padding: 20px;
                padding-top: 20px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }
            .academy-header-top {
                display: flex;
                align-items: center;
                gap: 15px;
                width: 100%;
                margin-bottom: 10px;
            }
            .academy-header h2 {
                font-size: 1.8rem;
            }
            .gamification-bar {
                flex-direction: column;
                padding: 0 20px;
            }
            .course-grid {
                grid-template-columns: 1fr;
                padding: 0 20px;
            }
            .mobile-toggle {
                display: block !important;
                position: static !important;
                background: rgba(255,255,255,0.1);
                color: white;
                border: none;
                border-radius: 8px;
                padding: 8px 12px;
                font-size: 1.5rem;
                cursor: pointer;
            }
            h3 {
                padding: 0 20px !important;
            }
        }
    </style>
</head>
<body class="academy-main">
<div class="app-container" style="background: transparent; min-height: 100vh;">
    <?php include dirname(__DIR__, 3) . '/Core/Views/partials/mobile_nav.php'; ?>
    <?php $active = 'academy_learn'; include __DIR__ . '/partials/sidebar_academy.php'; ?>

    <main class="main-content" style="padding: 0;">
        <div class="academy-header">
            <div class="academy-header-top">
                <button class="mobile-toggle d-md-none" onclick="toggleMobileMenu()" style="display: none;">
                    <ion-icon name="menu-outline"></ion-icon>
                </button>
                <h2>Continue Learning</h2>
            </div>
            <p style="color: #94a3b8; font-size: 1.1rem; margin-top: 0;">Welcome back! You're currently a <strong><?= htmlspecialchars($stats['rank'] ?? 'Novice') ?></strong>.</p>
        </div>

        <!-- Gamification Bar -->
        <div class="gamification-bar">
            <!-- Points Card -->
            <div class="gamification-card">
                <div class="rank-icon">
                    <ion-icon name="trophy"></ion-icon>
                </div>
                <div class="gamification-info" style="flex:1;">
                    <h4>Total Points</h4>
                    <p class="points-val"><?= number_format($stats['points'] ?? 0) ?> <span style="font-size: 1rem; color: #94a3b8; font-weight: normal;">pts</span></p>
                    
                    <div style="margin-top: 8px; background: #334155; height: 6px; border-radius: 3px; overflow: hidden; width: 100%;">
                        <div style="background: #f59e0b; height: 100%; width: <?= $stats['next_rank_progress'] ?? 0 ?>%;"></div>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 5px 0 0; text-align: right;"><?= $stats['next_rank_progress'] ?? 0 ?>% to next rank</p>
                </div>
            </div>

            <!-- Badges Card -->
            <div class="gamification-card" style="flex: 2;">
                <div class="gamification-info" style="width: 100%;">
                    <h4>Earned Badges</h4>
                    <?php if (empty($badges)): ?>
                        <p style="color: #64748b; margin: 10px 0 0; font-size: 0.9rem;">Complete lessons and courses to earn badges!</p>
                    <?php else: ?>
                        <div class="badge-list" style="margin-top: 10px;">
                            <?php foreach($badges as $b): ?>
                                <div class="badge-item">
                                    <ion-icon name="<?= $b['badge_id'] === 'first_lesson' ? 'star' : ($b['badge_id'] === 'quick_learner' ? 'flash' : 'ribbon') ?>" style="color: #fcd34d;"></ion-icon>
                                    <?= ucwords(str_replace('_', ' ', $b['badge_id'])) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h3 style="padding: 0 40px; color: white; margin-bottom: 20px;">My Courses</h3>

        <?php if (empty($courses)): ?>
            <div style="text-align: center; padding: 100px 50px; color: #94a3b8;">
                <ion-icon name="film-outline" style="font-size: 5rem; opacity: 0.3;"></ion-icon>
                <p style="font-size: 1.2rem; margin-top: 20px;">Your learning list is empty.</p>
                <a href="/academy" class="btn" style="margin-top: 15px; background: #e11d48; border: none; color: white;">Browse Catalog</a>
            </div>
        <?php else: ?>
            <div class="course-grid">
                <?php foreach ($courses as $course): ?>
                <a href="/academy/course/<?= $course['id'] ?>" class="course-card-modern">
                    <div class="course-thumb-modern" style="background-image: url('<?= htmlspecialchars($course['thumbnail']) ?>');">
                        <div class="course-thumb-overlay"></div>
                        <div class="progress-bar-modern">
                            <div class="progress-fill-modern" style="width: <?= $course['progress_percent'] ?>%;"></div>
                        </div>
                    </div>
                    <div class="course-info-modern">
                        <h3><?= htmlspecialchars($course['title']) ?></h3>
                        <div class="course-meta">
                            <span><?= $course['progress_percent'] ?>% Complete</span>
                            <div class="play-btn"><ion-icon name="play"></ion-icon></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Certificates Section -->
        <?php if (!empty($certificates)): ?>
        <div style="padding: 40px; margin-top: 20px;">
            <h3 style="color: white; margin-bottom: 20px;"><ion-icon name="document-text" style="color: #10b981; vertical-align: middle;"></ion-icon> My Certificates</h3>
            <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155;">
                <?php foreach ($certificates as $cert): ?>
                    <div class="cert-item">
                        <div>
                            <strong style="color: #f8fafc; display: block; font-size: 1.1rem;"><?= htmlspecialchars($cert['course_title']) ?></strong>
                            <span style="color: #94a3b8; font-size: 0.85rem;">Issued: <?= date('M d, Y', strtotime($cert['issued_at'])) ?> | ID: <?= htmlspecialchars($cert['certificate_code']) ?></span>
                        </div>
                        <a href="/academy/certificate/<?= htmlspecialchars($cert['certificate_code']) ?>" class="btn" style="background: #10b981; color: white; border: none; padding: 8px 15px; text-decoration: none; border-radius: 6px; font-weight: 500;">View PDF</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>
</body>
</html>
