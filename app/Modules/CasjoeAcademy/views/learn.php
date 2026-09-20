<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($lesson['title']) ?> | <?= htmlspecialchars($course['title']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #020617; /* Very dark background for theater mode */
            color: #f8fafc;
        }
        .learn-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .learn-sidebar {
            width: 320px;
            background: #0f172a;
            border-right: 1px solid #1e293b;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        .learn-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar-header {
            padding: 20px;
            background: #020617;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 15px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .sidebar-header a {
            color: #94a3b8;
            transition: color 0.2s;
        }
        .sidebar-header a:hover {
            color: white;
        }
        .sidebar-section {
            background: #1e293b;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 0.85rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #0f172a;
        }
        .sidebar-lesson {
            padding: 16px 20px;
            border-bottom: 1px solid #1e293b;
            cursor: pointer;
            display: flex;
            gap: 12px;
            align-items: center;
            font-size: 0.95rem;
            color: #cbd5e1;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .sidebar-lesson:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        .sidebar-lesson.active {
            background: rgba(225, 29, 72, 0.1);
            color: #e11d48;
            border-left: 4px solid #e11d48;
        }
        .video-container {
            width: 100%;
            max-width: 1000px;
            background: black;
            aspect-ratio: 16/9;
            border-radius: 12px;
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            overflow: hidden;
            position: relative;
        }
        .lesson-header {
            width: 100%;
            max-width: 1000px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .lesson-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
        }
        .lesson-text {
            width: 100%;
            max-width: 1000px;
            background: #0f172a;
            padding: 40px;
            border-radius: 12px;
            border: 1px solid #1e293b;
            line-height: 1.7;
            color: #cbd5e1;
            font-size: 1.05rem;
        }
        
        /* Scrollbar styling for dark mode */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #020617; 
        }
        ::-webkit-scrollbar-thumb {
            background: #334155; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569; 
        }
        
        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
        }
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Mobile Responsiveness */
        .mobile-back-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .learn-container {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }
            .learn-sidebar {
                width: 100%;
                border-right: none;
                height: auto;
                overflow: visible;
                border-top: 1px solid #1e293b;
                order: 2; /* Put sidebar below content on mobile */
            }
            .learn-content {
                padding: 20px;
                overflow-y: visible;
                order: 1; /* Content (video) on top */
            }
            .mobile-back-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255,255,255,0.1);
                color: white;
                border: none;
                border-radius: 8px;
                padding: 8px 12px;
                font-size: 1.5rem;
                cursor: pointer;
                text-decoration: none;
            }
            .lesson-header {
                align-items: flex-start;
            }
            .lesson-header h1 {
                font-size: 1.5rem;
            }
            .lesson-text {
                padding: 20px;
            }
            .sidebar-header {
                display: none; /* Hide standard sidebar header on mobile since video is above it now */
            }
        }
    </style>
</head>
<body>

<div class="learn-container">
    <div class="learn-sidebar">
        <div class="sidebar-header">
            <a href="/academy/course/<?= $course['id'] ?>" style="font-size: 1.5rem;"><ion-icon name="arrow-back-outline"></ion-icon></a>
            <div style="flex: 1; min-width: 0;">
                <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 3px;">Course</div>
                <div style="font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: white;"><?= htmlspecialchars($course['title']) ?></div>
            </div>
        </div>

        <?php foreach ($sections as $sec): ?>
            <div class="sidebar-section"><?= htmlspecialchars($sec['title']) ?></div>
            <?php foreach ($sec['lessons'] as $l): ?>
                <a href="/academy/learn/<?= $l['id'] ?>" class="sidebar-lesson <?= $l['id'] == $lesson['id'] ? 'active' : '' ?>">
                    <ion-icon name="<?= $l['video_url'] ? 'play-circle' : 'document-text' ?>" style="font-size: 1.2rem;"></ion-icon>
                    <span style="flex: 1; line-height: 1.3;"><?= htmlspecialchars($l['title']) ?></span>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div class="learn-content">
        <div class="lesson-header">
            <a href="/academy/course/<?= $course['id'] ?>" class="mobile-back-btn d-md-none">
                <ion-icon name="arrow-back-outline"></ion-icon>
            </a>
            <h1><?= htmlspecialchars($lesson['title']) ?></h1>
        </div>

        <?php if ($lesson['video_url']): ?>
            <div class="video-container">
                <!-- Simple/Naive YouTube Embed Handling or Placeholder -->
                <?php if (strpos($lesson['video_url'], 'youtube') !== false): ?>
                    <iframe width="100%" height="100%" src="<?= str_replace('watch?v=', 'embed/', $lesson['video_url']) ?>" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                    <div style="text-align: center; color: #64748b;">
                        <ion-icon name="videocam-off-outline" style="font-size: 4rem; opacity: 0.5; margin-bottom: 10px;"></ion-icon>
                        <p style="margin: 0 0 15px 0;">Video Source Not Supported in Preview</p>
                        <a href="<?= $lesson['video_url'] ?>" target="_blank" style="color: #e11d48; text-decoration: none; font-weight: bold; padding: 8px 16px; border: 1px solid #e11d48; border-radius: 6px; display: inline-block;">Open External Video</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($lesson['content']): ?>
            <div class="lesson-text" style="color: white !important;">
                <?= $lesson['content'] ?>
            </div>
        <?php endif; ?>

        <!-- Mark Complete Button -->
        <div style="margin-top: 40px; text-align: center; width: 100%; max-width: 1000px; padding-bottom: 50px;">
            <?php if (!$isCompleted): ?>
                <form action="/academy/learner/complete-lesson" method="POST">
                    <input type="hidden" name="lessonId" value="<?= $lesson['id'] ?>">
                    <button type="submit" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); color: white; border: none; padding: 15px 30px; font-size: 1.1rem; font-weight: bold; border-radius: 8px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 15px rgba(225, 29, 72, 0.4);">
                        <ion-icon name="checkmark-circle" style="vertical-align: middle; margin-right: 5px; font-size: 1.3rem;"></ion-icon> Mark Lesson Complete
                    </button>
                </form>
            <?php else: ?>
                <div style="color: #10b981; font-size: 1.1rem; font-weight: bold; background: rgba(16, 185, 129, 0.1); padding: 15px 30px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <ion-icon name="checkmark-done-circle" style="font-size: 1.5rem;"></ion-icon> Lesson Completed
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['gamification_toast'])): ?>
    <div class="toast" id="gamificationToast">
        <ion-icon name="<?= $_SESSION['gamification_toast']['icon'] ?>" style="font-size: 1.5rem; color: #fcd34d;"></ion-icon>
        <?= htmlspecialchars($_SESSION['gamification_toast']['message']) ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('gamificationToast');
            setTimeout(() => { toast.classList.add('show'); }, 100);
            setTimeout(() => { toast.classList.remove('show'); }, 4000);
        });
    </script>
    <?php unset($_SESSION['gamification_toast']); ?>
<?php endif; ?>

</body>
</html>
