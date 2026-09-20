<?php 
$title = "Edit Book | Casjoe Business School";
$active = 'academy_library';
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
        .form-container {
            background: var(--glass-bg, #fff);
            border: 1px solid var(--glass-border, #eee);
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            margin: 40px auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .form-group { margin-bottom: 20px; }
        .form-label { font-weight: 600; color: #00004D; margin-bottom: 8px; display: block; }
        .form-control { border-radius: 8px; border: 1px solid #ccc; padding: 10px; width: 100%; }
        .btn-submit { background: #FFA600; color: #000; font-weight: bold; border: none; padding: 12px 25px; border-radius: 8px; width: 100%; cursor: pointer;}
        .btn-submit:hover { background: #e69500; }
        .current-file { font-size: 0.85rem; color: #666; margin-top: 5px; }

        @media (max-width: 992px) {
            .main-content { padding: 1rem; }
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
        <div class="top-bar">
            <h2>Edit Book</h2>
            <a href="/academy/library" class="btn" style="background: #f1f3f5; color:#333; padding: 8px 15px; border-radius: 8px; text-decoration:none;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Library
            </a>
        </div>

        <div class="form-container">
            <form action="/academy/library/update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($book['id']) ?>">
                
                <div class="form-group">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Author Name</label>
                    <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book['author'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Cover Image (Optional)</label>
                    <input type="file" name="cover" class="form-control" accept="image/*">
                    <?php if($book['cover_path']): ?>
                        <div class="current-file">Current: <a href="javascript:void(0)" onclick="window.open('<?= htmlspecialchars($book['cover_path']) ?>', '_blank')">View Image</a></div>
                    <?php endif; ?>
                    <small class="text-muted d-block mt-1">Leave blank to keep existing cover.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Upload PDF Book (Optional)</label>
                    <input type="file" name="pdf" class="form-control" accept="application/pdf">
                    <?php if($book['pdf_path']): ?>
                        <div class="current-file">Current PDF: <i>Active</i></div>
                    <?php endif; ?>
                    <small class="text-muted d-block mt-1">Select a new PDF to replace the current file. Leave empty to keep existing.</small>
                </div>

                <button type="submit" class="btn-submit">Update Book</button>
            </form>
        </div>

    </main>
</div>
</body>
</html>
