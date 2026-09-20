<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title>My Portal | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        :root {
            --portal-bg: #030014;
            --portal-primary: #000066;
            --portal-gold: #FFA600;
            --portal-text: #ffffff;
            --portal-text-muted: #94a3b8;
            --portal-card-bg: rgba(255, 255, 255, 0.03);
            --portal-card-border: rgba(255, 255, 255, 0.08);
            --portal-card-hover: rgba(255, 255, 255, 0.06);
            --portal-green: #10b981;
            --portal-red: #ef4444;
        }

        body { 
            background: var(--portal-bg); 
            color: var(--portal-text); 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            margin: 0;
        }

        .portal-header {
            background: linear-gradient(135deg, #000044 0%, #000077 50%, #1a1aaa 100%);
            padding: 40px 32px;
            border-radius: 0 0 24px 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--portal-gold);
            margin-bottom: -30px;
        }

        .portal-header::before {
            content: '';
            position: absolute;
            top: -50%; right: 0;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255,166,0,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .portal-title {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 2;
        }

        .portal-card {
            background: var(--portal-card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--portal-card-border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s;
        }

        .portal-card:hover {
            background: var(--portal-card-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .portal-card h3 {
            margin-top: 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--portal-gold);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            padding: 0 32px;
            position: relative;
            z-index: 2;
            margin-top: 50px;
        }

        /* Profile Section */
        .profile-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--portal-primary), var(--portal-gold));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #fff;
            box-shadow: 0 4px 15px rgba(255,166,0,0.3);
            border: 2px solid rgba(255,255,255,0.2);
        }

        .profile-info h2 { margin: 0; font-size: 1.5rem; font-weight: 800; }
        .profile-info p { margin: 4px 0 0; color: var(--portal-text-muted); font-size: 0.95rem; }

        /* Tables */
        .portal-table {
            width: 100%;
            border-collapse: collapse;
        }
        .portal-table th {
            text-align: left;
            padding: 12px;
            color: var(--portal-text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 1px solid var(--portal-card-border);
        }
        .portal-table td {
            padding: 14px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            font-size: 0.95rem;
        }
        .portal-table tr:last-child td { border-bottom: none; }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-success { background: rgba(16,185,129,0.15); color: var(--portal-green); border: 1px solid rgba(16,185,129,0.3); }
        .status-info { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }

        /* Buttons */
        .btn-portal {
            background: linear-gradient(135deg, var(--portal-gold), #ff8c00);
            color: #000;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-portal:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255,166,0,0.4); }
        
        .btn-outline-portal {
            background: rgba(255,255,255,0.05);
            color: #fff;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.2s;
        }
        .btn-outline-portal:hover { background: rgba(255,255,255,0.1); }

        .btn-danger { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .btn-danger:hover { background: rgba(239,68,68,0.25); }

        @media (max-width: 768px) {
            .portal-header { flex-direction: column; align-items: flex-start; gap: 20px; padding: 24px 20px; }
            .portal-grid { padding: 0 20px; margin-top: 30px; }
        }
    </style>
</head>
<body>
<div class="app-container" style="display:flex;">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>
    <main class="main-content" style="flex:1; background: var(--portal-bg); min-height: 100vh;">

        <div class="portal-header">
            <div>
                <div style="font-size:0.8rem; color:var(--portal-gold); text-transform:uppercase; font-weight:700; letter-spacing:1px; margin-bottom:5px; position:relative; z-index:2;">Self Service</div>
                <h1 class="portal-title">My Portal</h1>
            </div>
            <a href="/erp/my-portal/profile" class="btn-outline-portal" style="position:relative; z-index:2;">
                <ion-icon name="create-outline"></ion-icon> Edit Profile
            </a>
        </div>

        <div class="portal-grid">
            <!-- Profile Card -->
            <div class="portal-card" style="grid-column: 1 / -1; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                <div class="profile-section">
                    <div class="profile-avatar">
                        <ion-icon name="person"></ion-icon>
                    </div>
                    <div class="profile-info">
                        <h2><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
                        <p><ion-icon name="mail-outline" style="vertical-align:middle;"></ion-icon> <?= htmlspecialchars($employee['email']) ?> &nbsp;|&nbsp; <ion-icon name="briefcase-outline" style="vertical-align:middle;"></ion-icon> <?= htmlspecialchars($employee['position'] ?? 'Employee') ?></p>
                    </div>
                </div>
                
                <div class="attendance-action">
                    <?php if ($activeAttendance): ?>
                        <form method="POST" action="/erp/my-portal/checkout" style="margin: 0;">
                            <button type="submit" class="btn-outline-portal btn-danger">
                                <ion-icon name="log-out-outline"></ion-icon> Check Out Now
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/erp/my-portal/checkin" style="margin: 0;">
                            <button type="submit" class="btn-portal">
                                <ion-icon name="log-in-outline"></ion-icon> Check In Now
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attendance -->
            <div class="portal-card">
                <h3><ion-icon name="finger-print-outline"></ion-icon> Recent Attendance</h3>
                <div style="overflow-x: auto;">
                    <table class="portal-table">
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
                                    <td><span class="status-badge status-success">Present</span></td>
                                    <td><?= date('H:i', strtotime($att['check_in'])) ?> - <?= $att['check_out'] ? date('H:i', strtotime($att['check_out'])) : 'Active' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($attendance)): ?>
                                <tr><td colspan="3" style="text-align:center; color:var(--portal-text-muted); padding: 20px 0;">No recent records.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payslips -->
            <div class="portal-card">
                <h3><ion-icon name="cash-outline"></ion-icon> Recent Payslips</h3>
                <div style="overflow-x: auto;">
                    <table class="portal-table">
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
                                    <td><?= date('M d, Y', strtotime($pay['pay_period_end'])) ?></td>
                                    <td style="font-weight:600;">$<?= number_format($pay['net_pay'], 2) ?></td>
                                    <td>
                                        <button class="btn-outline-portal" style="padding: 6px 12px; font-size: 0.8rem;">
                                            <ion-icon name="download-outline"></ion-icon>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($payslips)): ?>
                                <tr><td colspan="3" style="text-align:center; color:var(--portal-text-muted); padding: 20px 0;">No payslips found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Leave -->
            <div class="portal-card" style="grid-column: 1 / -1;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:20px;">
                    <h3 style="margin:0;"><ion-icon name="calendar-outline"></ion-icon> Upcoming Leave / Time Off</h3>
                    <a href="/erp/my-portal/request-leave" class="btn-outline-portal">
                        <ion-icon name="add-outline"></ion-icon> Request Time Off
                    </a>
                </div>
                <div style="overflow-x: auto;">
                    <table class="portal-table">
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
                                    <td style="font-weight:600;"><?= htmlspecialchars($leave['leave_type']) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave['start_date'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($leave['end_date'])) ?></td>
                                    <td><span class="status-badge status-info"><?= ucfirst($leave['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($leaves)): ?>
                                <tr><td colspan="4" style="text-align:center; color:var(--portal-text-muted); padding: 20px 0;">No upcoming leave requests.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>
<?php require __DIR__ . '/../../layout/footer.php'; ?>
</body>
</html>
