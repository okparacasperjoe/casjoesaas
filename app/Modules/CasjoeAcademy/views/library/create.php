<?php 
$title = "Upload Book | Casjoe Business School";
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
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .upload-card {
            background: rgba(30, 30, 30, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            margin: 40px auto;
        }
        .form-label { color: #ccc; }
        .form-control {
            background: rgba(0,0,0,0.3);
            border: 1px solid #444;
            color: #fff;
        }
        .form-control:focus {
            background: rgba(0,0,0,0.5);
            border-color: var(--secondary, #FF9900);
            color: #fff;
            box-shadow: none;
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
            <h2>Upload Book</h2>
        </div>

        <div class="container">
            <div class="upload-card">
                <form action="/academy/library/store" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Book Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. CEO Excellence">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" name="author" class="form-control" required placeholder="Author Name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cover Image</label>
                        <input type="file" name="cover" class="form-control" accept="image/*">
                        <div class="form-text text-muted">Optional. Recommended size: 400x600px</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Book PDF File (Required)</label>
                        <input type="file" name="pdf" class="form-control" accept="application/pdf" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold">Upload Book</button>
                        <a href="/academy/library" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
