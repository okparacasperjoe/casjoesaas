<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Edit Department | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Edit Department: <?= htmlspecialchars($department['name']) ?></h2>
            <a href="/erp/departments" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px;">
            <form method="POST" action="/erp/departments/update">
                <input type="hidden" name="id" value="<?= $department['id'] ?>">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Department Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($department['name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">Description</label>
                    <textarea name="description" class="form-control" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?= htmlspecialchars($department['description']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="background: var(--primary-color); color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Update Department</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
