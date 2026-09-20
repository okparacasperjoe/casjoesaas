<?php 
$title = "Edit Knowledge Base | Casjoe Academy";
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
        .kb-edit-container {
            max-width: 1000px;
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
        .btn-save {
            background: #ffa600;
            color: #000066;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-save:hover {
            background: #ffb700;
            transform: translateY(-2px);
        }
        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .editor-wrapper {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            min-height: 500px;
            display: flex;
            flex-direction: column;
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
        <div class="kb-edit-container">
            
            <form method="POST" action="/academy/knowledge-base/update">
                
                <div class="kb-header">
                    <h1 class="kb-title">
                        <ion-icon name="create-outline"></ion-icon>
                        Edit Knowledge Base
                    </h1>
                    <div style="display: flex; gap: 15px;">
                        <a href="/academy/knowledge-base" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-save">
                            <ion-icon name="save-outline"></ion-icon> Save Document
                        </button>
                    </div>
                </div>

                <div class="editor-wrapper">
                    <?php 
                        $editorName = 'content';
                        $editorValue = $content;
                        require dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php'; 
                    ?>
                </div>

            </form>

        </div>
    </main>
</div>
</body>
</html>
