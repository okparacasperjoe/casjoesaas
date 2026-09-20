<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
                        <div style="display:flex; gap:5px;">
                 <button onclick='editProject(<?= json_encode($proj) ?>)' class="btn btn-outline" style="padding: 2px 6px; font-size: 0.75rem;">Edit</button>
                 <form action="/erp/projects/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete project?');">
                    <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                    <button type="submit" class="btn btn-danger-outline" style="padding: 2px 6px; font-size: 0.75rem;">Del</button>
                 </form>
                 <a href="#" style="color: var(--secondary); text-decoration: none; font-size: 0.9rem;">View Details</a>
             </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
    <dialog id="editProjectModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/projects/update" method="POST">
            <input type="hidden" name="id" id="edit_proj_id">
            <h3 style="color:#000066; margin-top:0;">Edit Project</h3>
            <div class="form-group"><label>Project Name *</label><input type="text" name="name" id="edit_proj_name" class="form-control" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="edit_proj_desc" class="form-control" rows="3"></textarea></div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_proj_status" class="form-control">
                    <option value="planning">Planning</option>
                    <option value="active">Active</option>
                    <option value="on_hold">On Hold</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('editProjectModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn" style="background:#000066; color:#fff;">Update</button>
            </div>
        </form>
    </dialog>
    <script>
    function editProject(p) {
        document.getElementById('edit_proj_id').value = p.id;
        document.getElementById('edit_proj_name').value = p.name;
        document.getElementById('edit_proj_desc').value = p.description;
        document.getElementById('edit_proj_status').value = p.status;
        document.getElementById('editProjectModal').showModal();
    }
    </script>
</body>
</html>
