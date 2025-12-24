<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Employee | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Onboard New Employee</h2>
            <a href="/erp/hr" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 800px; margin: auto;">
            <form method="POST" action="/erp/employees/store">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id" class="form-control">
                            <option value="">-- No Department --</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="job_title" class="form-control" placeholder="e.g. Software Engineer">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Salary (Annual)</label>
                        <input type="number" name="salary" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Hire Date</label>
                        <input type="date" name="hire_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>

                <!-- Login Access Section -->
                <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 10px; border: 1px solid #eee;">
                    <div style="margin-bottom: 15px;">
                        <label class="toggle-switch">
                            <input type="checkbox" name="create_login" id="create-login-check" value="1">
                            <span style="font-weight: 600; margin-left:10px;">Enable Login Access</span>
                        </label>
                        <p style="font-size: 0.85rem; color: #666; margin-top:5px;">Check this to create a user account for this employee immediately.</p>
                    </div>

                    <div id="login-fields" style="display: none;">
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                        </div>
                        <div class="form-group" style="margin-top: 15px;">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="user">User (Employee Portal Only)</option>
                                <option value="hr">HR Manager (Access to HR Module)</option>
                                <option value="admin">Admin (Full Access)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('create-login-check').addEventListener('change', function() {
                        document.getElementById('login-fields').style.display = this.checked ? 'block' : 'none';
                    });
                </script>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Save Employee</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
