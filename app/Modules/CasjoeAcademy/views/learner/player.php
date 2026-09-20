<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']) ?> | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .player-container { display: flex; height: 100vh; overflow: hidden; }
        .player-sidebar { width: 350px; background: #fff; border-right: 1px solid #eee; overflow-y: auto; display: flex; flex-direction: column; }
        .player-content { flex: 1; background: #f8f9fa; padding: 40px; overflow-y: auto; position: relative; }
        
        .sidebar-header { padding: 20px; border-bottom: 1px solid #eee; background: #f8f9fa; }
        .section-title { padding: 15px 20px; background: #f0f4f8; font-weight: bold; font-size: 0.9rem; color: #555; text-transform: uppercase; border-bottom: 1px solid #eee; }
        
        .lesson-item { display: flex; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f5f5f5; cursor: pointer; text-decoration: none; color: #333; transition: background 0.2s; }
        .lesson-item:hover { background: #f9f9f9; }
        .lesson-item.active { background: #e3f2fd; border-left: 4px solid var(--primary); color: var(--primary); font-weight: bold; }
        
        .lesson-status { margin-right: 15px; font-size: 1.2rem; color: #ccc; }
        .lesson-status.completed { color: green; }

        .video-wrapper { background: #000; width: 100%; aspect-ratio: 16/9; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; color: #fff; border-radius: 8px; overflow: hidden; }
    </style>
</head>
<body>
    <div class="player-container">
        <!-- Sidebar -->
        <aside class="player-sidebar">
            <div class="sidebar-header">
                <a href="/academy/learn" style="text-decoration: none; color: #666; font-size: 0.9rem; display: block; margin-bottom: 10px;">&larr; Back to Dashboard</a>
                <h2 style="font-size: 1.2rem; margin: 0;"><?= htmlspecialchars($course['title']) ?></h2>
                <div style="font-size: 0.8rem; color: #999; margin-top: 5px;">Progress: NaN% (Calculated via Controller)</div>
            </div>

            <?php foreach ($sections as $section): ?>
                <div class="section-title"><?= htmlspecialchars($section['title']) ?></div>
                <?php foreach ($section['lessons'] as $lesson): ?>
                    <?php 
                        $isCompleted = $lesson['is_completed'] ?? false;
                        $isActive = ($activeLesson && $activeLesson['id'] == $lesson['id']);
                    ?>
                    <a href="?lesson=<?= $lesson['id'] ?>" class="lesson-item <?= $isActive ? 'active' : '' ?>">
                        <ion-icon name="<?= $isCompleted ? 'checkmark-circle' : 'ellipse-outline' ?>" class="lesson-status <?= $isCompleted ? 'completed' : '' ?>"></ion-icon>
                        <span><?= htmlspecialchars($lesson['title']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </aside>

        <!-- Main Content -->
        <main class="player-content">
            <?php if ($activeLesson): ?>
                <h1 style="margin-bottom: 20px;"><?= htmlspecialchars($activeLesson['title']) ?></h1>
                
                <div class="video-wrapper">
                    <!-- Placeholder for video -->
                    <ion-icon name="play-circle" style="font-size: 5rem; opacity: 0.5;"></ion-icon>
                </div>

                <div class="lesson-body" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
                    <?= nl2br(htmlspecialchars($activeLesson['content'])) ?>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Mark Complete Button -->
                    <a href="/academy/learn/complete/<?= $activeLesson['id'] ?>" class="btn btn-primary btn-lg">Mark as Complete</a>
                    
                    <?php if (true): // Check if next lesson exists ?>
                        <a href="#" class="btn btn-secondary">Next Lesson &rarr;</a>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding-top: 100px; color: #999;">
                    <ion-icon name="school-outline" style="font-size: 4rem; margin-bottom: 20px;"></ion-icon>
                    <h2>Select a lesson to start learning</h2>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

