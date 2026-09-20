<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lesson['title']) ?> | Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <?php include __DIR__ . '/partials/sidebar_acad_css.php'; ?>

            <div class="acad-brand">
                <ion-icon name="school"></ion-icon>
                Casjoe<span>Academy</span>
            </div>
            <ul class="acad-menu">
                <li class="acad-item"><a href="/" class="acad-link"><ion-icon name="apps-outline"></ion-icon>Back to
                        Apps</a></li>
                <li class="acad-item"><a href="/academy/course/<?= $lesson['course_id'] ?>" class="acad-link"><ion-icon
                            name="arrow-back-outline"></ion-icon>Back to Course</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1><?= htmlspecialchars($lesson['title']) ?></h1>
            </div>

            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 30px;">
                <?php if ($lesson['video_url']): ?>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; background: black;">
                        <iframe src="<?= htmlspecialchars($lesson['video_url']) ?>"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                            allowfullscreen></iframe>
                    </div>
                <?php else: ?>
                    <div style="padding: 50px; text-align: center; background: #000;">
                        <ion-icon name="videocam-off-outline" style="font-size: 48px; opacity: 0.5;"></ion-icon>
                        <p>No video content available.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>Lesson Notes</h3>
                <div style="line-height: 1.6; margin-top: 15px;">
                    <?= nl2br(htmlspecialchars($lesson['content'])) ?>
                </div>
                <div style="margin-top: 30px; text-align: right;">
                    <button class="btn" style="background: var(--secondary);">Mark as Complete</button>
                    <a href="/academy/course/<?= $lesson['course_id'] ?>" class="btn"
                        style="background: transparent; border: 1px solid rgba(255,255,255,0.2);">Next Lesson</a>
                </div>
            </div>

        </main>
    </div>
</body>

</html>
