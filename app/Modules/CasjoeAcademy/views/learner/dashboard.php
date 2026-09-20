<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/favicon.png">
    <title>Learner Dashboard | Casjoe Academy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'academy_learn'; include __DIR__ . '/../partials/sidebar_academy.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1>My Training</h1>
            </div>

            <?php if (empty($myCourses)): ?>
                <div class="card app-card-white" style="text-align: center; padding: 40px;">
                    <ion-icon name="book-outline" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></ion-icon>
                    <h3>No Courses Assigned</h3>
                    <p style="color: #666;">You haven't been assigned any training yet. Ask your team administrator.</p>
                </div>
            <?php else: ?>
                <div class="dashboard-grid">
                    <?php foreach ($myCourses as $c): ?>
                        <div class="card app-card-white" style="text-align: left; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                            <img src="<?= htmlspecialchars($c['thumbnail']) ?>" style="width: 100%; height: 150px; object-fit: cover;">
                            <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                                <div style="font-size: 11px; color: var(--secondary); font-weight: bold; text-transform: uppercase;">
                                    <?= htmlspecialchars($c['category']) ?>
                                </div>
                                <h3 style="margin: 5px 0 10px; font-size: 1.1rem; color: #333;"><?= htmlspecialchars($c['title']) ?></h3>
                                
                                <div style="margin-top: auto;">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #666; margin-bottom: 5px;">
                                        <span>Progress</span>
                                        <span><?= $c['progress_percent'] ?>%</span>
                                    </div>
                                    <div style="background: #eee; height: 6px; border-radius: 3px; overflow: hidden; margin-bottom: 15px;">
                                        <div style="background: var(--secondary); height: 100%; width: <?= $c['progress_percent'] ?>%;"></div>
                                    </div>
                                    
                                    <a href="/academy/player/<?= $c['id'] ?>" class="btn" style="width: 100%; text-align: center;">Continue Learning</a>
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

