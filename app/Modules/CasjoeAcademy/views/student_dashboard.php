<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Learning | Casjoe Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .course-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            align-items: center;
        }
        .course-thumb {
            width: 120px;
            height: 80px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            flex-shrink: 0;
        }
        .progress-bar {
            background: #eee;
            height: 10px;
            border-radius: 5px;
            width: 100%;
            margin-top: 10px;
            overflow: hidden;
        }
        .progress-fill {
            background: var(--secondary);
            height: 100%;
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
            <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
            <li class="nav-item"><a href="/academy" class="nav-link"><ion-icon name="library-outline"></ion-icon> Catalog</a></li>
            <li class="nav-item"><a href="/academy/my-courses" class="nav-link active"><ion-icon name="school-outline"></ion-icon> My Learning</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>My Learning</h2>
        </div>

        <?php if (empty($courses)): ?>
            <div style="text-align: center; padding: 50px; color: #666;">
                <ion-icon name="book-outline" style="font-size: 4rem; opacity: 0.5;"></ion-icon>
                <p>You haven't enrolled in any courses yet.</p>
                <a href="/academy" class="btn">Browse Catalog</a>
            </div>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <div class="course-thumb" style="background-image: url('<?= htmlspecialchars($course['thumbnail']) ?>');"></div>
                <div style="flex: 1;">
                    <div style="font-weight: bold; font-size: 1.1rem;"><?= htmlspecialchars($course['title']) ?></div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $course['progress_percent'] ?>%;"></div>
                    </div>
                    <div style="font-size: 0.8rem; margin-top: 5px; color: #666;"><?= $course['progress_percent'] ?>% Complete</div>
                </div>
                <a href="/academy/course/<?= $course['id'] ?>" class="btn">Continue</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>
</div>
</body>
</html>
