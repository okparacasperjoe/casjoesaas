<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Assessment Results | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_acad_css.php'; ?>

            <div class="acad-brand">
                <span>Casjoe Business School</span>
            </div>
             <ul class="acad-menu">
                <li class="acad-item"><a href="/academy/learn" class="acad-link">Back to Dashboard</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Assessment Result</h1>
            </div>

            <div class="card app-card-white" style="text-align: center; padding: 40px; max-width: 600px; margin: 40px auto;">
                <?php if ($passed): ?>
                    <ion-icon name="trophy-outline" style="font-size: 4rem; color: #2ed573; margin-bottom: 20px;"></ion-icon>
                    <h2 style="color: #2ed573;">Congratulations! You Passed!</h2>
                    <p style="font-size: 1.2rem;">You scored <strong><?= round($percent) ?>%</strong></p>
                    <p style="color: #666; margin-bottom: 30px;">You have successfully completed this course and earned your certificate.</p>
                    
                    <a href="/academy/certificate/<?= isset($code) ? $code : $existing['certificate_code'] ?? 'view' ?>" class="btn" target="_blank">Download Certificate</a>
                    <a href="/academy/learn" class="btn" style="background: #eee; color: #333; margin-left: 10px;">Back to Dashboard</a>
                
                <?php else: ?>
                    <ion-icon name="alert-circle-outline" style="font-size: 4rem; color: #ff4757; margin-bottom: 20px;"></ion-icon>
                    <h2 style="color: #ff4757;">Assessment Failed</h2>
                    <p style="font-size: 1.2rem;">You scored <strong><?= round($percent) ?>%</strong></p>
                    <p style="color: #666; margin-bottom: 30px;">You need <?= $quiz['passing_score'] ?>% to pass. Review the material and try again.</p>
                    
                    <a href="/academy/quiz/<?= $quiz['course_id'] ?>" class="btn">Retake Quiz</a>
                     <a href="/academy/learn" class="btn" style="background: #eee; color: #333; margin-left: 10px;">Back to Course</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

