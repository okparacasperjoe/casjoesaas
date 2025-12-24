<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Timesheets | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>Project Timesheets</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <h3>Log Time</h3>
            <form action="/erp/projects/timesheets/log" method="POST">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">-- General / No Project --</option>
                        <?php foreach ($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>"><?= htmlspecialchars($proj['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Log Time</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Duration</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['employee_name']) ?></td>
                            <td><?= htmlspecialchars($log['project_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($log['task_title'] ?? '-') ?></td>
                            <td><?= floor($log['duration_minutes'] / 60) ?>h <?= $log['duration_minutes'] % 60 ?>m</td>
                            <td><?= date('M d, Y', strtotime($log['start_time'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="5" align="center">No time logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </main>
</div>
</body>
</html>
