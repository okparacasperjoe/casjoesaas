<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Portal | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>
    <main class="main-content">

<div class="content-header">
    <h1>My Portal</h1>
    <button class="btn btn-outline" onclick="alert('Demo: Edit Profile Modal')">Edit Profile</button>
</div>

<!-- Profile Card -->
<div class="card mb-4">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div style="width: 80px; height: 80px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #888;">
            <ion-icon name="person"></ion-icon>
        </div>
        <div>
            <h2 style="margin: 0;"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
            <p style="margin: 5px 0; color: #666;"><?= htmlspecialchars($employee['email']) ?> | <?= htmlspecialchars($employee['position'] ?? 'Employee') ?></p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Attendance -->
    <div class="col-md-6">
        <div class="card">
            <h3>Recent Attendance</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Check In/Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $att): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($att['check_in'])) ?></td>
                            <td><span class="badge badge-success">Present</span></td>
                            <td><?= date('H:i', strtotime($att['check_in'])) ?> - <?= $att['check_out'] ? date('H:i', strtotime($att['check_out'])) : 'Active' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($attendance)): ?>
                        <tr><td colspan="3">No recent records.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payslips -->
    <div class="col-md-6">
        <div class="card">
            <h3>Recent Payslips</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Period End</th>
                        <th>Net Pay</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payslips as $pay): ?>
                        <tr>
                            <td><?= $pay['pay_period_end'] ?></td>
                            <td>$<?= number_format($pay['net_pay'], 2) ?></td>
                            <td><button class="btn btn-sm btn-outline">Download</button></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($payslips)): ?>
                        <tr><td colspan="3">No payslips found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <h3>Upcoming Leave / Time Off</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leaves as $leave): ?>
                <tr>
                    <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                    <td><?= $leave['start_date'] ?></td>
                    <td><?= $leave['end_date'] ?></td>
                    <td><span class="badge badge-info"><?= ucfirst($leave['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($leaves)): ?>
                <tr><td colspan="4">No upcoming leave.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button class="btn btn-sm btn-primary mt-2" onclick="alert('Demo: Request Leave Modal')">Request Time Off</button>
</div>

    </main>
</div>
</body>
</html>
