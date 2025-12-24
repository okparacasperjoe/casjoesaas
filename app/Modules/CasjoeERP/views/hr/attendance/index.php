<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Attendance Log</h2>
        </div>

        <div style="display: grid; gap: 20px;">
            <!-- Check In Form -->
            <div class="card">
                <h3>Record Attendance</h3>
                <form method="POST" action="/erp/attendance/checkin" style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label>Employee</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $e): ?>
                                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex: 2;">
                        <label>Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional notes...">
                    </div>
                    <div>
                        <button type="submit" class="btn">Check In</button>
                    </div>
                </form>
            </div>

            <!-- Log Table -->
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Duration</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                            <td><?= date('M d, H:i', strtotime($record['check_in'])) ?></td>
                            <td>
                                <?php if ($record['check_out']): ?>
                                    <?= date('M d, H:i', strtotime($record['check_out'])) ?>
                                <?php else: ?>
                                    <span style="color: green;">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ($record['check_out']) {
                                    $diff = strtotime($record['check_out']) - strtotime($record['check_in']);
                                    echo round($diff / 3600, 1) . ' hrs';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($record['notes']) ?></td>
                            <td>
                                <?php if (!$record['check_out']): ?>
                                    <form method="POST" action="/erp/attendance/checkout">
                                        <input type="hidden" name="id" value="<?= $record['id'] ?>">
                                        <button type="submit" class="btn btn-sm" style="background: #d9534f;">Check Out</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #999;">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
