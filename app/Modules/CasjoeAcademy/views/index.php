<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academy | Casjoe Apps</title>
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
                <li class="acad-item"><a href="/dashboard" class="acad-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
                <li class="acad-item"><a href="/academy" class="acad-link active"><ion-icon
                            name="book-outline"></ion-icon>My Courses</a></li>
                <li class="acad-item"><a href="#" class="acad-link"><ion-icon
                            name="library-outline"></ion-icon>Catalog</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>My Learning</h1>
                <a href="/academy/create" class="btn"><ion-icon name="add-outline"></ion-icon> New Course</a>
            </div>

            <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php if (empty($courses)): ?>
                    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <ion-icon name="school-outline" style="font-size: 48px; opacity: 0.5;"></ion-icon>
                        <p>No courses available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="card" style="display: flex; flex-direction: column;">
                            <div
                                style="height: 150px; background: rgba(255,255,255,0.05); border-radius: 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                                <ion-icon name="image-outline" style="font-size: 40px; opacity: 0.3;"></ion-icon>
                            </div>
                            <h3 style="margin-bottom: 10px;"><?= htmlspecialchars($course['title']) ?></h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; flex-grow: 1; margin-bottom: 20px;">
                                <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                <span class="status-badge status-active">
                                    <?= $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free' ?>
                                </span>
                                <a href="/academy/course/<?= $course['id'] ?>" class="btn"
                                    style="padding: 8px 15px; font-size: 0.9rem;">Continue</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>
