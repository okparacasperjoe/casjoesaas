<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Projects</h2>
            <a href="/erp/projects/create" class="btn"><ion-icon name="add-outline"></ion-icon> New Project</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php foreach ($projects as $proj): ?>
                <div class="card" style="border-top: 4px solid var(--primary);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <h3 style="margin: 0; font-size: 1.1rem;"><?= htmlspecialchars($proj['name']) ?></h3>
                        <?php 
                        $statusColors = [
                            'not_started' => ['bg' => '#f0f0f0', 'text' => '#333'],
                            'in_progress' => ['bg' => '#e3f2fd', 'text' => '#0277bd'], 
                            'completed'   => ['bg' => '#e8f5e9', 'text' => '#2e7d32'], 
                            'on_hold'     => ['bg' => '#fff3e0', 'text' => '#f57c00'], 
                            'cancelled'   => ['bg' => '#ffebee', 'text' => '#c62828'], 
                        ];
                        $st = $proj['status'];
                        $cols = $statusColors[$st] ?? ['bg' => '#eee', 'text' => '#333'];
                        ?>
                        <span style="font-size: 0.8rem; padding: 4px 8px; background: <?= $cols['bg'] ?>; color: <?= $cols['text'] ?>; border-radius: 4px; font-weight: 500;">
                            <?= ucfirst(str_replace('_', ' ', $st)) ?>
                        </span>
                    </div>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;"><?= htmlspecialchars($proj['description']) ?></p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 10px;">
                        <span style="font-size: 0.8rem; color: #888;">Tasks: -</span>
                        <a href="#" style="color: var(--secondary); text-decoration: none; font-size: 0.9rem;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>
