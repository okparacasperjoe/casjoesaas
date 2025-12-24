<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']) ?> | Casjoe Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, #000044 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            gap: 30px;
        }
        .hero-content {
            flex: 1;
        }
        .hero-image {
            width: 300px;
            height: 200px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .lesson-item {
            padding: 15px;
            background: white;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .lesson-item ion-icon {
            margin-right: 10px;
            color: var(--secondary);
        }
        .locked {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/academy" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Catalog</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="hero-section">
            <div class="hero-content">
                <h1 style="color: white; margin-top: 0;"><?= htmlspecialchars($course['title']) ?></h1>
                <p style="font-size: 1.1rem; opacity: 0.9; line-height: 1.6;"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                <div style="margin-top: 20px;">
                    <?php if ($isEnrolled): ?>
                        <button class="btn" style="background: var(--secondary); color: white;">Continue Learning</button>
                    <?php else: ?>
                        <a href="/academy/enroll/<?= $course['id'] ?>" class="btn" style="background: var(--secondary); color: white; padding: 15px 40px; font-size: 1.1rem;">
                            Enroll Now - <?= $course['price'] > 0 ? '₦'.number_format($course['price']) : 'Free' ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-image" style="background-image: url('<?= htmlspecialchars($course['thumbnail']) ?>');"></div>
        </div>

        <div class="card">
            <h3>Course Curriculum</h3>
            <?php foreach ($sections as $section): ?>
                <div class="section-title"><?= htmlspecialchars($section['title']) ?></div>
                <?php foreach ($section['lessons'] as $lesson): ?>
                    <div class="lesson-item <?= !$isEnrolled ? 'locked' : '' ?>">
                        <div style="display: flex; align-items: center;">
                            <ion-icon name="<?= $lesson['video_url'] ? 'play-circle-outline' : 'document-text-outline' ?>"></ion-icon>
                            <span><?= htmlspecialchars($lesson['title']) ?></span>
                        </div>
                        <?php if ($isEnrolled): ?>
                            <a href="/academy/learn/<?= $lesson['id'] ?>" class="btn" style="padding: 5px 15px; font-size: 0.8rem;">Start</a>
                        <?php else: ?>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

    </main>
</div>
</body>
</html>
