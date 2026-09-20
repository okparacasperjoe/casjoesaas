<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Edit Employee & Module Permissions | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body, .app-container, .main-content {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        select.form-control, input.form-control {
            color: #0f172a !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
        }
        select.form-control option {
            color: #0f172a !important;
            background-color: #ffffff !important;
        }
        .form-group label {
            color: #334155 !important;
            font-weight: 700 !important;
            font-size: 0.92rem !important;
            margin-bottom: 6px !important;
            display: block !important;
        }
        .top-bar {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 20px 30px !important;
        }
        .top-bar h2 {
            color: #000066 !important;
            font-weight: 800 !important;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2 style="margin: 0; font-size: 1.6rem; color: #000066;">Edit Employee Profile & Access</h2>
                <p style="margin: 4px 0 0; font-size: 0.9rem; color: #64748b;">Manage personal details, compensation, role level, and authorized sidebar modules</p>
            </div>
            <a href="/erp/hr" class="btn" style="background: #f1f5f9; color: #0f172a !important; border: 1px solid #cbd5e1; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600;">Back to Staff List</a>
        </div>

        <div style="padding: 30px; max-width: 920px; margin: auto;">
            <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); padding: 28px;">
                <form method="POST" action="/erp/employees/update">
                    <input type="hidden" name="id" value="<?= $employee['id'] ?>">
                    
                    <h3 style="margin: 0 0 18px; color: #000066; font-size: 1.15rem; font-weight: 800; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <ion-icon name="person-outline" style="vertical-align: -2px; margin-right: 6px;"></ion-icon> Personal Information
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($employee['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($employee['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 18px;">
                        <label>Email Address *</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" required>
                    </div>

                    <h3 style="margin: 28px 0 18px; color: #000066; font-size: 1.15rem; font-weight: 800; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                        <ion-icon name="briefcase-outline" style="vertical-align: -2px; margin-right: 6px;"></ion-icon> Employment Details
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">-- No Department Assigned --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>" <?= ($employee['department_id'] ?? null) == $dept['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="job_title" class="form-control" value="<?= htmlspecialchars($employee['job_title'] ?? '') ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 18px;">
                        <div class="form-group">
                            <label>Annual Salary ($)</label>
                            <input type="number" name="salary" class="form-control" step="0.01" value="<?= htmlspecialchars($employee['salary'] ?? 0) ?>">
                        </div>
                        <div class="form-group">
                            <label>Hire Date</label>
                            <input type="date" name="hire_date" class="form-control" value="<?= htmlspecialchars($employee['hire_date'] ?? date('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <div class="form-group">
                            <label>Employment Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?= ($employee['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="on_leave" <?= ($employee['status'] ?? '') == 'on_leave' ? 'selected' : '' ?>>On Leave</option>
                                <option value="terminated" <?= ($employee['status'] ?? '') == 'terminated' ? 'selected' : '' ?>>Terminated</option>
                                <option value="resigned" <?= ($employee['status'] ?? '') == 'resigned' ? 'selected' : '' ?>>Resigned</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Annual Leave Balance (Days)</label>
                            <input type="number" name="leave_balance" class="form-control" value="<?= htmlspecialchars($employee['leave_balance'] ?? 20) ?>" min="0">
                        </div>
                    </div>

                    <!-- Granular Module Permissions & Login Access Section -->
                    <div style="margin-top: 30px; padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #cbd5e1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 20px;">
                            <div>
                                <h3 style="margin: 0; color: #000066; font-size: 1.15rem; font-weight: 800;">
                                    <ion-icon name="shield-checkmark-outline" style="vertical-align: -2px; margin-right: 6px;"></ion-icon> Portal Access & Granular Module Permissions
                                </h3>
                                <p style="margin: 4px 0 0; font-size: 0.86rem; color: #64748b;">Configure what modules this staff member can view and operate inside the Casjoe ERP sidebar.</p>
                            </div>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" onclick="selectModules('all')" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; color: #0f172a;">Select All</button>
                                <button type="button" onclick="selectModules('default')" style="background: rgba(0,0,102,0.08); border: 1px solid rgba(0,0,102,0.2); padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; color: #000066;">Default (Projects/Tasks)</button>
                                <button type="button" onclick="selectModules('none')" style="background: #fef2f2; border: 1px solid #fecaca; padding: 5px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; color: #991b1b;">Clear All</button>
                            </div>
                        </div>

                        <?php if (isset($user) && $user): ?>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="color: #000066 !important;">System Role Level (Current: <span style="text-transform: uppercase; color: #0f172a;"><?= htmlspecialchars($user['role']) ?></span>)</label>
                            <select name="role" class="form-control" style="background: #ffffff !important;">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User (Standard Employee Staff)</option>
                                <option value="hr" <?= $user['role'] === 'hr' ? 'selected' : '' ?>>HR Manager (Personnel & Payroll Administration)</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Full System & All Modules Access)</option>
                            </select>
                        </div>
                        <?php else: ?>
                        <div style="padding: 12px 16px; background: #fffbeb; border: 1px solid #fef08a; border-radius: 8px; margin-bottom: 20px; color: #92400e; font-size: 0.88rem;">
                            <ion-icon name="information-circle-outline" style="vertical-align: -2px; font-size: 1.1rem;"></ion-icon>
                            <strong>Note:</strong> No login credentials have been created for this employee yet. You can still pre-configure their allowed modules below so they apply when their account is created.
                        </div>
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
                            <?php 
                            $assignedModules = $userModules ?? [];
                            foreach ($modules as $mod): 
                                $isChecked = in_array($mod['slug'], $assignedModules) ? 'checked' : '';
                            ?>
                            <label style="display: flex; align-items: flex-start; gap: 12px; background: #ffffff; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.02);" onmouseover="this.style.borderColor='#000066'; this.style.boxShadow='0 2px 8px rgba(0,0,102,0.06)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.02)'">
                                <input type="checkbox" name="accessible_modules[]" value="<?= $mod['slug'] ?>" class="module-check" <?= $isChecked ?> style="margin-top: 3px; width: 17px; height: 17px; cursor: pointer;">
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 0.94rem;"><?= htmlspecialchars(explode(' (', $mod['name'])[0]) ?></div>
                                    <?php if (strpos($mod['name'], '(') !== false): ?>
                                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 3px; line-height: 1.3;"><?= htmlspecialchars('(' . explode(' (', $mod['name'])[1]) ?></div>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <script>
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

                    <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 14px;">
                        <a href="/erp/hr" class="btn" style="background: #f1f5f9; color: #0f172a !important; border: 1px solid #cbd5e1; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600;">Cancel</a>
                        <button type="submit" class="btn" style="background: #000066; color: #ffffff !important; padding: 12px 32px; border-radius: 8px; font-weight: 700; font-size: 0.95rem; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,102,0.2);">Save Employee & Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
