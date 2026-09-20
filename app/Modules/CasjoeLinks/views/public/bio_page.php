<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($bio['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($bio['description'] ?? '') ?>">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            background-color: <?= htmlspecialchars($theme['bgColor'] ?? '#f8f9fc') ?>;
            color: <?= htmlspecialchars($theme['textColor'] ?? '#333') ?>;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }
        .bio-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .bio-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .bio-header h1 {
            font-size: 2rem;
            margin: 0 0 10px;
        }
        .bio-header p {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-bottom: 30px;
        }
        .bio-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .bio-link {
            display: block;
            padding: 15px;
            background: <?= htmlspecialchars($theme['btnColor'] ?? '#ffffff') ?>;
            color: <?= htmlspecialchars($theme['btnTextColor'] ?? '#000066') ?>;
            text-decoration: none;
            border-radius: <?= htmlspecialchars($theme['btnRadius'] ?? '8px') ?>;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bio-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .casjoe-badge {
            margin-top: 50px;
            display: inline-block;
            font-size: 0.8rem;
            text-decoration: none;
            color: inherit;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="bio-container">
        <div class="bio-header">
            <?php if(!empty($theme['avatar'])): ?>
                <img src="<?= htmlspecialchars($theme['avatar']) ?>" alt="Avatar">
            <?php endif; ?>
            <h1><?= htmlspecialchars($bio['title']) ?></h1>
            <p><?= htmlspecialchars($bio['description']) ?></p>
        </div>
        
        <div class="bio-links">
            <?php foreach($blocks as $block): ?>
                <?php if($block['type'] === 'link'): ?>
                    <a href="<?= htmlspecialchars($block['url']) ?>" class="bio-link" target="_blank" rel="noopener">
                        <?= htmlspecialchars($block['title']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        
        <a href="https://casjoe.com" target="_blank" class="casjoe-badge">
            Powered by <strong>Casjoe</strong>
        </a>
    </div>
</body>
</html>
