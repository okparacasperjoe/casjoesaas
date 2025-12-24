<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($lesson['title']) ?> | <?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .learn-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .learn-sidebar {
            width: 350px;
            background: white;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .learn-content {
            flex: 1;
            background: #f5f7fa;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-section {
            background: #f9f9f9;
            padding: 10px 20px;
            font-weight: bold;
            font-size: 0.9rem;
            color: #666;
            border-bottom: 1px solid #eee;
        }
        .sidebar-lesson {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            gap: 10px;
            align-items: center;
            font-size: 0.95rem;
            color: #444;
            transition: background 0.2s;
            text-decoration: none;
        }
        .sidebar-lesson:hover {
            background: #f0f4ff;
        }
        .sidebar-lesson.active {
            background: #eef2ff;
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }
        .video-container {
            width: 100%;
            max-width: 900px;
            background: black;
            aspect-ratio: 16/9;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .lesson-text {
            width: 100%;
            max-width: 900px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            line-height: 1.6;
        }
        .nav-btn {
            background: transparent;
            border: 1px solid #ccc;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }
    </style>
</head>
<body style="margin: 0;">

<div class="learn-container">
    <div class="learn-sidebar">
        <div class="sidebar-header">
            <a href="/academy/course/<?= $course['id'] ?>" style="color: white; font-size: 1.5rem;"><ion-icon name="arrow-back-outline"></ion-icon></a>
            <div>
                <div style="font-size: 0.8rem; opacity: 0.8;">Course</div>
                <div style="font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;"><?= htmlspecialchars($course['title']) ?></div>
            </div>
        </div>

        <?php foreach ($sections as $sec): ?>
            <div class="sidebar-section"><?= htmlspecialchars($sec['title']) ?></div>
            <?php foreach ($sec['lessons'] as $l): ?>
                <a href="/academy/learn/<?= $l['id'] ?>" class="sidebar-lesson <?= $l['id'] == $lesson['id'] ? 'active' : '' ?>">
                    <ion-icon name="<?= $l['video_url'] ? 'play-circle-outline' : 'document-text-outline' ?>"></ion-icon>
                    <?= htmlspecialchars($l['title']) ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div class="learn-content">
        <div style="width: 100%; max-width: 900px; margin-bottom: 20px;">
            <h1 style="margin: 0; color: #333;"><?= htmlspecialchars($lesson['title']) ?></h1>
        </div>

        <?php if ($lesson['video_url']): ?>
            <div class="video-container">
                <!-- Simple/Naive YouTube Embed Handling or Placeholder -->
                <?php if (strpos($lesson['video_url'], 'youtube') !== false): ?>
                    <iframe width="100%" height="100%" src="<?= str_replace('watch?v=', 'embed/', $lesson['video_url']) ?>" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                    <div style="text-align: center;">
                        <ion-icon name="videocam-off-outline" style="font-size: 4rem; opacity: 0.5;"></ion-icon>
                        <p>Video Source Not Supported in Preview</p>
                        <a href="<?= $lesson['video_url'] ?>" target="_blank" style="color: var(--secondary);">Open Video</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($lesson['content']): ?>
            <div class="lesson-text">
                <?= nl2br(htmlspecialchars($lesson['content'])) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
