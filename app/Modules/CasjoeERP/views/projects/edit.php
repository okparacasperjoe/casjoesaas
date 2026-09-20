<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Edit Project | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Edit Project</h2>
            <a href="/erp/projects" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: auto;">
            <form method="POST" action="/erp/projects/update">
                <input type="hidden" name="id" value="<?= $project['id'] ?>">
                
                <div class="form-group">
                    <label>Project Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($project['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Customer (Client)</label>
                    <select name="client_id" class="form-control">
                        <option value="">-- Select Customer --</option>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($c['id'] == $project['client_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($project['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="not_started" <?= ($project['status'] == 'not_started') ? 'selected' : '' ?>>Not Started</option>
                        <option value="in_progress" <?= ($project['status'] == 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                        <option value="on_hold" <?= ($project['status'] == 'on_hold') ? 'selected' : '' ?>>On Hold</option>
                        <option value="completed" <?= ($project['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($project['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Update Project</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
