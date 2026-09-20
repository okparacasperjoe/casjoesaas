<!DOCTYPE html>
<html>
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Unavailable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FFA600 0%, #000066 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .unavailable-container {
            max-width: 600px;
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #FFA600, #000066);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .icon-wrapper svg {
            width: 50px;
            height: 50px;
            fill: white;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        .form-title {
            font-size: 20px;
            color: #000066;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .message {
            color: #666;
            line-height: 1.8;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #FFA600;
            padding: 20px;
            border-radius: 8px;
            margin-top: 25px;
            text-align: left;
        }
        .info-box strong {
            color: #333;
            display: block;
            margin-bottom: 10px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        @media (max-width: 768px) {
            .unavailable-container { padding: 35px 25px; }
            h1 { font-size: 24px; }
            .form-title { font-size: 18px; }
        }
    </style>
</head>
<body>
    <div class="unavailable-container">
        <div class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
        </div>
        
        <h1>Form Currently Unavailable</h1>
        
        <?php if (!empty($form['title'])): ?>
            <div class="form-title"><?= htmlspecialchars($form['title']) ?></div>
        <?php endif; ?>
        
        <p class="message">
            <?= htmlspecialchars($form['closed_message'] ?? 'This form is temporarily not accepting new responses. The form owner has paused submissions for the time being.') ?>
        </p>
        
        <div class="info-box">
            <strong>What does this mean?</strong>
            <p>The form administrator has temporarily disabled new submissions. This could be due to maintenance, capacity reached, or the response period has ended.</p>
        </div>
        
        <div class="info-box" style="margin-top: 15px;">
            <strong>Need access?</strong>
            <p>If you believe this is a mistake or you need urgent access, please contact the form owner directly.</p>
        </div>
    </div>
</body>
</html>
