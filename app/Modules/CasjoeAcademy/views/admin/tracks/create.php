<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Create Track | Casjoe Business School</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../../layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="top-bar">
                <h1>Create New Track</h1>
            </div>

            <div class="card" style="max-width: 800px;">
                <form action="/academy/tracks/store" method="POST">
                    
                    <div class="form-group">
                        <label>Track Title</label>
                        <input type="text" name="title" required placeholder="e.g. Executive Leadership MBA" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" name="price" step="0.01" min="0" value="0.00" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Select Courses (Order Matters)</label>
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                            <?php foreach ($courses as $course): ?>
                                <label style="display: block; margin-bottom: 8px; padding: 5px; background: #f9f9f9; border-radius: 4px;">
                                    <input type="checkbox" name="courses[]" value="<?= $course['id'] ?>"> 
                                    <?= htmlspecialchars($course['title']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <small style="color: #666;">Check courses to include in this track.</small>
                    </div>

                    <div class="form-actions" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Create Track</button>
                        <a href="/academy/tracks" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

