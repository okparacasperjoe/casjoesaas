<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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

                <!-- Login Access & Granular Module Permissions -->
                <div style="margin-top: 30px; padding: 24px; background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                    <div style="margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
                        <label class="toggle-switch" style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="create_login" id="create-login-check" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                            <span style="font-weight: 800; font-size: 1.08rem; color: #000066; margin-left:12px;">Enable Portal Login & Assign Module Permissions</span>
                        </label>
                        <p style="font-size: 0.88rem; color: #64748b; margin: 6px 0 0 30px;">
                            Check this box to create login credentials and hand-pick the exact Casjoe ERP modules this employee is authorized to access.
                        </p>
                    </div>

                    <div id="login-fields" style="display: none;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="font-weight: 700; color: #334155;">Initial Account Password *</label>
                                <input type="password" name="password" class="form-control" autocomplete="new-password" placeholder="Enter secure initial password...">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 700; color: #334155;">System Role Level</label>
                                <select name="role" class="form-control" id="role-select-box">
                                    <option value="user">User (Standard Employee Staff)</option>
                                    <option value="hr">HR Manager (Personnel & Payroll Administration)</option>
                                    <option value="admin">Admin (Full System & All Modules Access)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Granular Module Permissions Selection Board -->
                        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
                                <div>
                                    <h4 style="margin: 0; font-size: 1rem; color: #000066; font-weight: 800;">Granular Module Access Selection</h4>
                                    <p style="margin: 4px 0 0; font-size: 0.84rem; color: #64748b;">Select specific sidebar modules this employee can view and interact with:</p>
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" onclick="selectModules('all')" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; color: #0f172a;">Select All</button>
                                    <button type="button" onclick="selectModules('default')" style="background: rgba(0,0,102,0.08); border: 1px solid rgba(0,0,102,0.2); padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; color: #000066;">Default (Projects/Tasks)</button>
                                    <button type="button" onclick="selectModules('none')" style="background: #fef2f2; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; color: #991b1b;">Clear All</button>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; background: #f8fafc; padding: 16px; border-radius: 10px; border: 1px solid #cbd5e1;">
                                <?php if (!empty($modules)): ?>
                                    <?php foreach ($modules as $mod): ?>
                                    <label style="display: flex; align-items: flex-start; gap: 10px; background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#000066'" onmouseout="this.style.borderColor='#e2e8f0'">
                                        <input type="checkbox" name="accessible_modules[]" value="<?= $mod['slug'] ?>" class="module-check" <?= $mod['slug'] === 'projects' ? 'checked' : '' ?> style="margin-top: 3px; width: 16px; height: 16px;">
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 0.92rem;"><?= htmlspecialchars(explode(' (', $mod['name'])[0]) ?></div>
                                            <?php if (strpos($mod['name'], '(') !== false): ?>
                                                <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px;"><?= htmlspecialchars('(' . explode(' (', $mod['name'])[1]) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="color: #64748b; font-size: 0.9rem;">No module options available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('create-login-check').addEventListener('change', function() {
                        document.getElementById('login-fields').style.display = this.checked ? 'block' : 'none';
                    });

                    function selectModules(mode) {
                        const checks = document.querySelectorAll('.module-check');
                        checks.forEach(chk => {
                            if (mode === 'all') chk.checked = true;
                            else if (mode === 'none') chk.checked = false;
                            else if (mode === 'default') {
                                chk.checked = (chk.value === 'projects');
                            }
                        });
                    }
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
