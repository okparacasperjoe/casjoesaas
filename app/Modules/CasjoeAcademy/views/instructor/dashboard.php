<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Instructor Dashboard | Casjoe Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .studio-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .stats-stripe {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: 1px solid #eee;
        }
        .stat-icon-bg {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        .course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eee;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-color: var(--secondary-light, #ddd);
        }
        .card-thumb {
            height: 180px;
            width: 100%;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }
        .card-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 10px 0;
            line-height: 1.4;
        }
        .badge-overlay {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            backdrop-filter: blur(4px);
        }
        .badge-draft { color: #f39c12; }
        .badge-published { color: #2ecc71; }
        
        .card-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #f5f5f5;
            display: flex;
            gap: 10px;
        }
        .btn-edit {
            flex: 1;
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 8px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-edit:hover { background: #e2e6ea; }
        .btn-primary-action {
            background: var(--primary);
            color: white;
            border: none;
        }
        .btn-primary-action:hover { background: var(--primary-dark, #004); }
    </style>
    <style>
        .main-content { margin-left: 270px; padding: 40px; }
        @media (max-width: 992px) {
            .sidebar { left: -270px !important; transition: left 0.3s ease; }
            .sidebar.active { left: 0 !important; }
            .main-content { margin-left: 0 !important; width: 100%; padding: 70px 20px 20px !important; }
            .card, .table-container { overflow-x: auto; }
            table, .data-table { min-width: 600px; }
            .row { flex-direction: column; }
            .grid-container, .course-grid { grid-template-columns: 1fr !important; }
            .stats-stripe { flex-direction: column; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'academy_instructor'; include __DIR__ . '/../partials/sidebar_academy.php'; ?>

        <main class="main-content">
            <div class="studio-header">
                <div>
                    <h1 style="margin: 0;">Instructor Studio</h1>
                    <p style="color: var(--text-muted, #aaa); margin: 5px 0 0;">Manage your curriculum, track earnings, and publish courses.</p>
                </div>
                <a href="/academy/instructor/create" class="btn btn-primary" style="padding: 10px 20px; font-weight: 600; color: white !important; border: 1px solid var(--primary, #000066);">
                    <ion-icon name="add-circle"></ion-icon> New Course
                </a>
            </div>

            <!-- Dashboard Stats (Mock Data) -->
            <div class="stats-stripe">
                <div class="stat-box">
                    <div class="stat-icon-bg" style="background: #e3f2fd; color: #1976d2;">
                        <ion-icon name="library"></ion-icon>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem; color: #333;"><?= count($courses) ?></h3>
                        <span style="color: #666; font-size: 0.9rem;">Total Courses</span>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon-bg" style="background: #e8f5e9; color: #2e7d32;">
                        <ion-icon name="people"></ion-icon>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem; color: #333;">0</h3>
                        <span style="color: #666; font-size: 0.9rem;">Active Students</span>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon-bg" style="background: #fff3e0; color: #f57c00;">
                        <ion-icon name="cash"></ion-icon>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem; color: #333;"><?= (isset($_SESSION['currency']) && $_SESSION['currency'] === 'NGN') ? '₦' : '$' ?>0.00</h3>
                        <span style="color: #666; font-size: 0.9rem;">Revenue</span>
                    </div>
                </div>
            </div>

            <!-- Course Grid -->
            <?php if (empty($courses)): ?>
                <div class="empty-state" style="text-align: center; padding: 60px; background: white; border-radius: 12px; border: 2px dashed #eee;">
                    <div style="background: #f8f9fa; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <ion-icon name="school" style="font-size: 3rem; color: #ccc;"></ion-icon>
                    </div>
                    <h3 style="color: #333;">Create Your First Course</h3>
                    <p style="color: #666; max-width: 400px; margin: 10px auto 30px;">Share your knowledge with your team. Create high-quality training content in minutes.</p>
                    <a href="/academy/instructor/create" class="btn btn-primary" style="color: white !important; font-weight: 600;">Get Started</a>
                </div>
            <?php else: ?>
                <h3 style="margin-bottom: 20px; color: #444;">Your Courses</h3>
                <div class="course-grid">
                    <?php foreach($courses as $course): ?>
                        <div class="course-card">
                            <span class="badge-overlay badge-<?= $course['status'] ?>">
                                <?= $course['status'] === 'published' ? 'PUBLISHED' : 'DRAFT' ?>
                            </span>
                            <img src="<?= htmlspecialchars($course['thumbnail'] && !empty($course['thumbnail']) ? $course['thumbnail'] : '/assets/course_placeholder.jpg') ?>" class="card-thumb" alt="Cover" onerror="this.src='/assets/course_placeholder.jpg'">
                            
                            <div class="card-body">
                                <div class="card-meta">
                                    <span><?= htmlspecialchars($course['category'] ?? 'Uncategorized') ?></span>
                                    <span>$<?= number_format($course['price_per_seat'] ?? 0, 2) ?></span>
                                </div>
                                <h3 class="card-title"><?= htmlspecialchars($course['title']) ?></h3>
                                <p style="font-size: 0.9rem; color: #666; line-height: 1.5; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars(strip_tags($course['description'] ?? '')) ?>
                                </p>
                                
                                <div class="card-footer">
                                    <a href="/academy/instructor/edit/<?= $course['id'] ?>" class="btn-edit btn-primary-action">
                                        <ion-icon name="construct"></ion-icon> Edit Builder
                                    </a>
                                    <?php if($course['status'] !== 'published'): ?>
                                        <a href="/academy/instructor/publish/<?= $course['id'] ?>" class="btn-edit" style="color: #2ecc71; border-color: #2ecc71; background: transparent;">
                                            <ion-icon name="cloud-upload"></ion-icon> Publish
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
