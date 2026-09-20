<?php
// Dependencies: $month, $startDate, $attendance, $chartLabels, $chartData
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Attendance Report | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ── Brand Colours ───────────────────────────── */
        /* Navy #000066 | Amber #ffa600 | White #ffffff  */

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .report-card {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,102,0.10);
            margin-bottom: 24px;
            border: 1px solid #dde0f5;
        }
        .report-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #000066;
            margin: 0 0 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #ffa600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Table */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .attendance-table thead th {
            background: #000066;
            color: #ffffff;
            font-weight: 600;
            padding: 12px 14px;
            text-align: left;
            white-space: nowrap;
        }
        .attendance-table thead th:first-child { border-radius: 8px 0 0 0; }
        .attendance-table thead th:last-child  { border-radius: 0 8px 0 0; }
        .attendance-table tbody td {
            padding: 11px 14px;
            border-bottom: 1px solid #e8e8f7;
            color: #1a1a4e;
            vertical-align: middle;
        }
        .attendance-table tbody tr:hover { background: #f5f5fc; }
        .attendance-table tbody tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge-late {
            display: inline-block;
            background: #ffa600;
            color: #000066;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 6px;
            vertical-align: middle;
        }
        .badge-active {
            display: inline-block;
            background: #000066;
            color: #ffffff;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        /* Print button */
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #000066;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.1s;
        }
        .btn-print:hover {
            background: #ffa600;
            color: #000066;
            transform: translateY(-1px);
        }

        /* Month picker */
        .month-picker { display: flex; align-items: center; gap: 10px; }
        .month-picker label {
            font-weight: 700;
            color: #000066;
            font-size: 0.875rem;
        }
        .month-picker input[type="month"] {
            padding: 8px 12px;
            border: 2px solid #000066;
            border-radius: 8px;
            font-size: 0.875rem;
            background: #ffffff;
            color: #000066;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }
        .month-picker input[type="month"]:focus {
            border-color: #ffa600;
            box-shadow: 0 0 0 3px rgba(255,166,0,0.25);
        }

        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #000066;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { color: #ffa600; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 48px;
            color: #6b72b8;
            font-size: 0.9rem;
        }
        .empty-state ion-icon {
            font-size: 3rem;
            margin-bottom: 12px;
            display: block;
            color: #ffa600;
        }

        /* Print media */
        @media print {
            .sidebar, .top-bar, .btn-print, form, .back-link { display: none !important; }
            .app-container { display: block !important; }
            .main-content { margin: 0 !important; padding: 10px !important; }
            body { background: #fff !important; }
            .report-card { box-shadow: none; border: 1px solid #ccc; }
            .attendance-table thead th {
                background: #000066 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar { position: fixed; top: 0; left: -100%; height: 100%; z-index: 1000; transition: left 0.3s; width: 260px !important; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
            .attendance-table-wrap { overflow-x: auto; }
            .report-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2 style="color:#ffffff;">Attendance Report</h2>
            <div class="user-profile">
                <a href="/erp/attendance" class="back-link" style="color:#ffa600;">
                    <ion-icon name="arrow-back-outline"></ion-icon> Back to Attendance
                </a>
            </div>
        </div>

        <div class="report-header">
            <form method="GET" action="/erp/attendance/report" class="month-picker">
                <label for="month-input" style="color:#ffffff;">Month:</label>
                <input type="month" id="month-input" name="month"
                       value="<?= htmlspecialchars($month ?? date('Y-m')) ?>"
                       onchange="this.form.submit()">
            </form>
            <button onclick="window.print()" class="btn-print">
                <ion-icon name="print-outline"></ion-icon> Print Report
            </button>
        </div>

        <!-- Bar Chart -->
        <div class="report-card">
            <h3>
                <ion-icon name="bar-chart-outline" style="color:#ffa600;"></ion-icon>
                Average Check-in Time &mdash; <?= date('F Y', strtotime($startDate ?? 'now')) ?>
            </h3>
            <?php if (empty($chartLabels)): ?>
                <div class="empty-state">
                    <ion-icon name="calendar-outline"></ion-icon>
                    No attendance data recorded for this month.
                </div>
            <?php else: ?>
                <canvas id="attendanceChart" height="80"></canvas>
            <?php endif; ?>
        </div>

        <!-- Detailed Logs Table -->
        <div class="report-card">
            <h3>
                <ion-icon name="list-outline" style="color:#ffa600;"></ion-icon>
                Detailed Logs
            </h3>
            <?php if (empty($attendance)): ?>
                <div class="empty-state">
                    <ion-icon name="people-outline"></ion-icon>
                    No check-in records found for this month.
                </div>
            <?php else: ?>
            <div class="attendance-table-wrap">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Duration</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance as $record): ?>
                            <?php
                                $checkIn   = $record['check_in']  ?? null;
                                $checkOut  = $record['check_out'] ?? null;
                                $notes     = (string)($record['notes'] ?? '');
                                $firstName = $record['first_name'] ?? '';
                                $lastName  = $record['last_name']  ?? '';
                                $isLate    = $checkIn && (int)date('G', strtotime($checkIn)) > 9;
                                $duration  = '-';
                                if ($checkIn && $checkOut) {
                                    $diff = strtotime($checkOut) - strtotime($checkIn);
                                    $duration = round($diff / 3600, 1) . ' hrs';
                                }
                            ?>
                            <tr>
                                <td><?= $checkIn ? date('M d, Y', strtotime($checkIn)) : '-' ?></td>
                                <td><?= htmlspecialchars(trim($firstName . ' ' . $lastName)) ?></td>
                                <td>
                                    <?= $checkIn ? date('h:i A', strtotime($checkIn)) : '-' ?>
                                    <?php if ($isLate): ?>
                                        <span class="badge-late">Late</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($checkOut): ?>
                                        <?= date('h:i A', strtotime($checkOut)) ?>
                                    <?php else: ?>
                                        <span class="badge-active">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($duration) ?></td>
                                <td><?= htmlspecialchars($notes) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<script>
    const labels = <?= json_encode($chartLabels ?? []) ?>;
    const data   = <?= json_encode($chartData ?? []) ?>;

    if (labels.length > 0) {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Avg Check-in Time',
                    data: data,
                    backgroundColor: 'rgba(0, 0, 102, 0.80)',
                    borderColor: '#000066',
                    borderWidth: 0,
                    borderRadius: 6,
                    hoverBackgroundColor: '#ffa600'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 7,
                        max: 13,
                        ticks: {
                            color: '#000066',
                            font: { weight: '600' },
                            callback: function(value) {
                                const hrs  = Math.floor(value);
                                const mins = Math.round((value - hrs) * 60);
                                const ampm = hrs >= 12 ? 'PM' : 'AM';
                                const disp = hrs > 12 ? hrs - 12 : (hrs === 0 ? 12 : hrs);
                                return disp + ':' + (mins < 10 ? '0' + mins : mins) + ' ' + ampm;
                            }
                        },
                        grid: { color: 'rgba(0,0,102,0.07)' },
                        title: {
                            display: true,
                            text: 'Check-in Time',
                            color: '#000066',
                            font: { weight: '700', size: 12 }
                        }
                    },
                    x: {
                        ticks: { color: '#000066', font: { weight: '600' } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#000066',
                        titleColor: '#ffa600',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                let hrs  = Math.floor(value);
                                let mins = Math.round((value - hrs) * 60);
                                if (mins < 10) mins = '0' + mins;
                                let ampm = hrs >= 12 ? 'PM' : 'AM';
                                let disp = hrs > 12 ? hrs - 12 : (hrs === 0 ? 12 : hrs);
                                return 'Avg Check-in: ' + disp + ':' + mins + ' ' + ampm;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
</body>
</html>
