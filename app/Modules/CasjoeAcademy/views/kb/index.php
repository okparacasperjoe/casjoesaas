<?php 
$title = "Knowledge Base | Casjoe Academy";
$active = 'academy_kb';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .kb-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .kb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .kb-title {
            font-size: 2rem;
            color: #000066;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-edit {
            background: #ffa600;
            color: #000066;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-edit:hover {
            background: #ffb700;
            transform: translateY(-2px);
        }
        .kb-content {
            color: #334155;
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .kb-content h1, .kb-content h2, .kb-content h3 {
            color: #000066;
            margin-top: 30px;
        }
        .kb-content img {
            max-width: 100%;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <aside class="sidebar">
    <?php include __DIR__ . '/../partials/sidebar_acad_css.php'; ?>

        <div class="acad-brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;"><img src="/assets/casjoe_logo.webp" alt="Casjoe Apps" style="height: 40px;"></a></div>
        <?php require __DIR__ . '/../partials/sidebar_academy.php'; ?>
    </aside>

    <main class="main-content">
        <div class="kb-container">
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    Knowledge Base updated successfully!
                </div>
            <?php endif; ?>

            <div class="kb-header">
                <h1 class="kb-title">
                    <ion-icon name="book-outline"></ion-icon>
                    Knowledge Base
                </h1>
                <?php if ($canEdit): ?>
                    <a href="/academy/knowledge-base/edit" class="btn-edit">
                        <ion-icon name="create-outline"></ion-icon> Edit Document
                    </a>
                <?php endif; ?>
            </div>

            <div class="kb-content">
                <?= $content ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
