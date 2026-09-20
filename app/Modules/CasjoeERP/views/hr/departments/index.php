<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Departments | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Departments</h2>
            <a href="/erp/departments/create" class="btn"><ion-icon name="add-outline"></ion-icon> New Department</a>
        </div>

        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">Name</th>
                        <th style="padding: 10px;">Description</th>
                        <th style="padding: 10px;">Employees</th>
                        <th style="padding: 10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $dept): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-weight: bold;"><?= htmlspecialchars($dept['name']) ?></td>
                            <td style="padding: 10px; color: #666;"><?= htmlspecialchars($dept['description']) ?></td>
                            <td style="padding: 10px;">
                                <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">
                                    <?= $dept['employee_count'] ?> Employees
                                </span>
                            </td>
                            <td style="padding: 10px;">
                                <button class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #eee; color: #333;">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
    <dialog id="editDeptModal" style="padding: 28px; border: 1px solid #e2e8f0; border-radius: 16px; width: 440px; max-width: 90vw; background: #ffffff; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <form action="/erp/departments/update" method="POST">
            <input type="hidden" name="id" id="edit_dept_id">
            <h3 style="color:#000066; margin-top:0;">Edit Department</h3>
            <div class="form-group"><label>Department Name *</label><input type="text" name="name" id="edit_dept_name" class="form-control" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="edit_dept_desc" class="form-control" rows="3"></textarea></div>
            <div style="margin-top: 20px; text-align: right; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="document.getElementById('editDeptModal').close()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn" style="background:#000066; color:#fff;">Update</button>
            </div>
        </form>
    </dialog>
    <script>
    function editDept(d) {
        document.getElementById('edit_dept_id').value = d.id;
        document.getElementById('edit_dept_name').value = d.name;
        document.getElementById('edit_dept_desc').value = d.description;
        document.getElementById('editDeptModal').showModal();
    }
    </script>
</body>
</html>
