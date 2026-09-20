<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($quiz['title']) ?> | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .quiz-container { max-width: 800px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .question-card { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .question-text { font-size: 1.2rem; font-weight: bold; margin-bottom: 15px; color: #2c3e50; }
        .option-label { display: flex; align-items: center; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: 0.2s; }
        .option-label:hover { background: #f8f9fa; border-color: #3498db; }
        .option-input { margin-right: 15px; width: 18px; height: 18px; }
    </style>
</head>
<body style="background: #f5f7fa;">

<div class="quiz-container">
    <div style="margin-bottom: 30px; text-align: center;">
        <h1 style="margin: 0; color: #2c3e50;"><?= htmlspecialchars($quiz['title']) ?></h1>
        <p style="color: #7f8c8d;">Pass Mark: <?= $quiz['passing_score'] ?>%</p>
    </div>

    <form action="/academy/quiz/submit/<?= $quiz['id'] ?>" method="POST">
        <?php foreach ($questions as $index => $q): ?>
            <div class="question-card">
                <div class="question-text"><?= ($index + 1) . '. ' . htmlspecialchars($q['question_text']) ?></div>
                <div class="options-list">
                    <?php 
                        $opts = json_decode($q['options'], true) ?: []; 
                        foreach ($opts as $optIndex => $optText):
                    ?>
                        <label class="option-label">
                            <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $optIndex ?>" required class="option-input">
                            <span><?= htmlspecialchars($optText) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div style="text-align: center; margin-top: 40px;">
            <button type="submit" class="btn" style="padding: 15px 50px; font-size: 1.1rem;">Submit Quiz</button>
        </div>
    </form>
</div>

</body>
</html>
