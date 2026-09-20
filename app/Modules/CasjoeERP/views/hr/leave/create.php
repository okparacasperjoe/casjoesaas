<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Request Leave | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Request Leave</h2>
        </div>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <form action="/erp/leave/store" method="POST">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Leave Type</label>
                    <select name="leave_type" class="form-control" required>
                        <option value="annual">Annual Leave</option>
                        <option value="sick">Sick Leave</option>
                        <option value="unpaid">Unpaid Leave</option>
                        <option value="maternity">Maternity Leave</option>
                        <option value="paternity">Paternity Leave</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn">Submit Request</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
