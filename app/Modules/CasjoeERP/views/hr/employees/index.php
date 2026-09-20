<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Employees | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; text-transform: uppercase; font-weight: bold; }
        .status-active { background: #dcfce7; color: #15803d; }
        .status-resigned { background: #fef9c3; color: #854d0e; }
        .status-terminated { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Employees</h2>
            <div style="display:flex; gap:10px;">
                <a href="/erp/employees/create" class="btn" style="background: #f1f5f9; color:#0f172a !important; border:1px solid #cbd5e1;"><ion-icon name="document-text-outline" style="vertical-align:middle;"></ion-icon> Full Form</a>
                <button class="btn" onclick="openAddEmployeeModal()" style="background: #000066; color: #ffffff !important;"><ion-icon name="person-add-outline" style="vertical-align:middle;"></ion-icon> Onboard Employee</button>
            </div>
        </div>

        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td>
                                <div class="emp-name"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                                <div class="emp-email"><?= htmlspecialchars($emp['email']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($emp['job_title'] ?? '-') ?></td>
                            <td>
                                <?php if ($emp['department_name']): ?>
                                    <span style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 500;">
                                        <?= htmlspecialchars($emp['department_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #64748b;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $emp['status'] ?>">
                                    <?= ucfirst($emp['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <a href="/erp/employees/edit?id=<?= $emp['id'] ?>" class="btn" style="padding: 5px 12px; font-size: 0.8rem; background: #f1f5f9; color: #0f172a !important; border: 1px solid #cbd5e1; text-decoration:none;">View/Edit</a>
                                    <form method="POST" action="/erp/employees/delete" onsubmit="return confirm('Are you sure you want to delete this employee? This will also remove their login access.');" style="margin: 0;">
                                        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                        <button type="submit" class="btn" style="padding: 5px 12px; font-size: 0.8rem; background: #fee2e2; color: #991b1b !important; border: 1px solid #fca5a5;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Onboard Employee Modal -->
<div class="cancel-modal" id="addEmployeeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 10000; align-items: center; justify-content: center; overflow-y: auto;">
    <div class="modal-content" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 28px; width: 90%; max-width: 560px; color: #1e293b; margin: 30px auto; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <h3 style="color:#000066; margin-top:0; font-size:1.35rem; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
            <ion-icon name="person-add-outline" style="font-size:1.5rem; color:#FFA600; vertical-align:middle;"></ion-icon> Onboard New Staff
        </h3>
        <form method="POST" action="/erp/employees/store">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">First Name *</label>
                    <input type="text" name="first_name" class="form-control" placeholder="e.g. Casper" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" placeholder="e.g. Joe" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="casper@example.com" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;" required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Department</label>
                    <select name="department_id" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box; height:40px;">
                        <option value="">-- No Department --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Job Title</label>
                    <input type="text" name="job_title" class="form-control" placeholder="e.g. Software Engineer" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Salary (Annual)</label>
                    <input type="number" name="salary" class="form-control" placeholder="0.00" step="0.01" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Hire Date</label>
                    <input type="date" name="hire_date" class="form-control" value="<?= date('Y-m-d') ?>" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Status</label>
                <select name="status" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box; height:40px;">
                    <option value="active">Active</option>
                    <option value="on_leave">On Leave</option>
                </select>
            </div>

            <!-- Login Access Section -->
            <div style="margin-top:15px; margin-bottom:20px; padding:16px; background:#f8fafc; border-radius:10px; border:1px solid #cbd5e1;">
                <div style="margin-bottom: 12px; display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" name="create_login" id="modal-create-login-check" value="1" style="width:20px; height:20px; accent-color:#000066; cursor:pointer;">
                    <span style="font-weight:600; color:#0f172a;">Enable Login Access</span>
                </div>
                <div id="modal-login-fields" style="display: none;">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Password</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
                    </div>
                    <div class="form-group">
                        <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Role</label>
                        <select name="role" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box; height:40px;">
                            <option value="user">User (Employee Portal Only)</option>
                            <option value="hr">HR Manager (Access to HR Module)</option>
                            <option value="admin">Admin (Full Access)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn" style="background:#f1f5f9; color:#334155 !important; border:1px solid #cbd5e1; padding:10px 18px; border-radius:8px; cursor:pointer;" onclick="closeAddEmployeeModal()">Dismiss</button>
                <button type="submit" class="btn" style="background:#000066; color:#ffffff !important; font-weight:600; border:none; padding:10px 22px; border-radius:8px; cursor:pointer;"><ion-icon name="checkmark-circle-outline" style="margin-right:6px;"></ion-icon> Onboard</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modal-create-login-check').addEventListener('change', function() {
        document.getElementById('modal-login-fields').style.display = this.checked ? 'block' : 'none';
    });

    function openAddEmployeeModal() {
        document.getElementById('addEmployeeModal').style.display = 'flex';
    }

    function closeAddEmployeeModal() {
        document.getElementById('addEmployeeModal').style.display = 'none';
    }
</script>
</body>
</html>
