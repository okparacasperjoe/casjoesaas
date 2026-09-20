<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Final Assessment | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
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
                <h1><?= htmlspecialchars($quiz['title']) ?></h1>
            </div>

            <div class="card app-card-white" style="max-width: 800px; margin: 0 auto; text-align: left;">
                <p style="margin-bottom: 20px; color: #666;">Passing Score: <?= $quiz['passing_score'] ?>%</p>
                
                <form action="/academy/quiz/submit" method="POST">
                    <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                    
                    <?php foreach ($questions as $index => $q): ?>
                        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee;">
                            <h4 style="margin-top: 0; margin-bottom: 15px; color: #333;">
                                <?= ($index + 1) ?>. <?= htmlspecialchars($q['question_text']) ?>
                            </h4>
                            
                            <?php 
                                $options = json_decode($q['options'], true);
                                if (!is_array($options)) $options = [];
                            ?>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <?php foreach ($options as $optIndex => $optText): ?>
                                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #555;">
                                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $optIndex ?>" required>
                                        <span><?= htmlspecialchars(preg_replace('/\s*\(Correct\)/', '', $optText)) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn" onclick="return confirm('Submit Assessment?');">Submit Answers</button>
                    <a href="/academy/player/<?= $quiz['course_id'] ?>" class="btn" style="background: #eee; color: #333;">Cancel</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

