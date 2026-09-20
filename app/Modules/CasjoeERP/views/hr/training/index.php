<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Training | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Training Programs</h2>
            <button onclick="document.getElementById('newProgramModal').showModal()" class="btn">Schedule Training</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Program Name</th>
                        <th>Instructor</th><th>Action</th>
                        <th>Dates</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programs as $prog): ?>
                    <tr>
                        <td><?= htmlspecialchars($prog['name']) ?></td>
                        <td><?= htmlspecialchars($prog['instructor']) ?></td>
                     <td>
                        <form action="/erp/training/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete training program?');">
                            <input type="hidden" name="id" value="<?= $prog['id'] ?>">
                            <button type="submit" class="btn btn-danger-outline" style="padding: 2px 6px; font-size: 0.75rem;">Del</button>
                        </form>
                     </td>
                        <td><?= $prog['start_date'] ?> - <?= $prog['end_date'] ?></td>
                        <td><?= ucfirst($prog['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <dialog id="newProgramModal" style="padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <form action="/erp/training/store" method="POST">
            <h3>Schedule New Training</h3>
            <div class="form-group"><label>Program Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label>Instructor</label><input type="text" name="instructor" class="form-control" required></div>
            <div class="form-group"><label>Start Date</label><input type="date" name="start_date" class="form-control" required></div>
            <div class="form-group"><label>End Date</label><input type="date" name="end_date" class="form-control" required></div>
            <div style="margin-top: 10px; text-align: right;">
                <button type="button" onclick="document.getElementById('newProgramModal').close()" class="btn" style="background: #999;">Cancel</button>
                <button type="submit" class="btn">Schedule</button>
            </div>
        </form>
    </dialog>
</div>
</body>
</html>
