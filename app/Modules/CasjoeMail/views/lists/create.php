<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create List | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php $active = 'mail_audiences'; include dirname(__DIR__) . '/partials/sidebar_mail.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1>Create Subscriber List</h1>
                <a href="/mail/lists" class="btn-sm" style="color: var(--text-color);"><ion-icon name="arrow-back-outline"></ion-icon> Back</a>
            </div>

            <div style="max-width: 600px; padding: 0;">
                <div class="card app-card-white" style="align-items: stretch; text-align: left;">
                     <form action="/mail/lists/store" method="POST">
                        <div class="form-group">
                            <label>List Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Newsletter Subscribers" required style="color: #333; background: #fff; border: 1px solid #ddd;">
                        </div>
                        <button type="submit" class="btn">Create List</button>
                     </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
