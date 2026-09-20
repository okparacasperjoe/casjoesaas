<?php
// Shared File View
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$isImg = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared File: <?= htmlspecialchars($file['name']) ?> - Casjoe Cloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        :root {
            --bg-deep: #050b18;
            --cloud-glass: rgba(15, 23, 42, 0.85);
            --cloud-border: rgba(255, 255, 255, 0.1);
            --cloud-gold: #FFA600;
        }
        * { box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body {
            margin: 0;
            background: radial-gradient(circle at top right, #0a1931 0%, #050b18 60%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        .shared-card {
            background: var(--cloud-glass);
            border: 1px solid var(--cloud-border);
            border-radius: 24px;
            width: 100%;
            max-width: 680px;
            padding: 36px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(25px);
            position: relative;
        }
        .badge-bar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 166, 0, 0.15);
            color: #FFA600;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 20px;
        }
        .file-preview-area {
            border-radius: 16px;
            overflow: hidden;
            background: rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.08);
            margin: 24px 0;
            max-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .file-preview-area img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
        }
        .download-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--cloud-gold);
            color: #050b18;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 14px 36px;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 8px 25px rgba(255, 166, 0, 0.3);
        }
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 166, 0, 0.45);
        }
    </style>
</head>
<body>

<div class="shared-card">
    <div class="badge-bar">
        <ion-icon name="shield-checkmark"></ion-icon> Authenticated Casjoe Member Share
    </div>

    <h1 style="margin: 0 0 8px 0; font-size: 1.7rem; color: #fff; word-break: break-all;">
        <?= htmlspecialchars($file['name']) ?>
    </h1>
    <p style="margin: 0; color: #94a3b8; font-size: 0.9rem;">
        Size: <strong><?= round($file['size_bytes'] / 1024, 2) ?> KB</strong> &bull; Type: <strong><?= htmlspecialchars(strtoupper($ext ?: 'FILE')) ?></strong>
    </p>

    <?php if ($isImg): ?>
        <div class="file-preview-area">
            <img src="/cloud/preview?id=<?= $file['id'] ?>&share_token=<?= urlencode($_GET['token'] ?? '') ?>" alt="<?= htmlspecialchars($file['name']) ?>">
        </div>
    <?php else: ?>
        <div class="file-preview-area" style="padding: 50px 20px; flex-direction: column; gap: 12px;">
            <ion-icon name="document-text" style="font-size: 5rem; color: #60a5fa;"></ion-icon>
            <div style="font-weight: 700; color: #cbd5e1;"><?= htmlspecialchars(strtoupper($ext ?: 'DOCUMENT')) ?> ASSET</div>
        </div>
    <?php endif; ?>

    <div style="margin-top: 24px; display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
        <a href="/cloud/file/download?token=<?= urlencode($_GET['token'] ?? '') ?>" class="download-btn">
            <ion-icon name="download-outline"></ion-icon> Download File
        </a>
        <a href="/cloud" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.08); color: #fff; font-weight: 600; padding: 14px 24px; border-radius: 14px; text-decoration: none;">
            <ion-icon name="cloud-outline"></ion-icon> Open My Cloud
        </a>
    </div>
</div>

</body>
</html>
