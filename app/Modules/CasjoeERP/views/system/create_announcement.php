<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>New Announcement | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Post Announcement</h2>
            <a href="/erp/announcements" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: auto;">
            <form method="POST" action="/erp/announcements/store">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Content</label>
                    
                    <!-- Casjoe Visual Editor Component -->
                    <?php 
                        $editorName = 'content';
                        // The initial value is populated directly from PHP since we are in PHP
                        $editorValue = '';
                        require dirname(__DIR__, 4) . '/Views/partials/casjoe_editor.php'; 
                    ?>
    
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Post Announcement</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
