<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Quiz Results | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .result-card { 
            max-width: 500px; margin: 60px auto; background: white; padding: 40px; 
            border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            text-align: center; 
        }
        .score-circle {
            width: 150px; height: 150px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem; font-weight: bold; margin: 0 auto 30px;
            border: 8px solid;
        }
        .status-passed { border-color: #2ecc71; color: #2ecc71; }
        .status-failed { border-color: #e74c3c; color: #e74c3c; }
    </style>
</head>
<body style="background: #f5f7fa;">

<div class="result-card">
    <?php if ($passed): ?>
        <div class="score-circle status-passed">
            <?= $score ?>%
        </div>
        <h1 style="color: #2c3e50;">Congratulations!</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">You successfully passed this quiz.</p>
    <?php else: ?>
        <div class="score-circle status-failed">
            <?= $score ?>%
        </div>
        <h1 style="color: #2c3e50;">Keep Trying</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">You didn't reach the passing score. Review the material and try again.</p>
    <?php endif; ?>

    <div style="margin-top: 40px;">
        <a href="javascript:history.go(-2)" class="btn">Back to Course</a>
    </div>
</div>

</body>
</html>
