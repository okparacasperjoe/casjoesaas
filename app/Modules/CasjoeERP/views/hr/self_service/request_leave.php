<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Request Leave | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                height: 100%;
                z-index: 1000;
                transition: 0.3s;
                width: 260px;
                background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
        }
        
        .premium-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            border-top: 5px solid #000066;
            margin: auto;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <?php require dirname(__DIR__, 5) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>Request Time Off</h2>
            <a href="/erp/my-portal" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="premium-card">
            <form method="POST" action="/erp/leave/store">
                <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                <input type="hidden" name="source" value="portal">
                
                <div class="form-group">
                    <label>Leave Type</label>
                    <select name="leave_type" class="form-control" required>
                        <option value="annual">Annual Leave</option>
                        <option value="sick">Sick Leave</option>
                        <option value="unpaid">Unpaid Leave</option>
                        <option value="maternity">Maternity/Paternity</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
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
                    <label>Reason (Optional)</label>
                    <textarea name="reason" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Submit Request</button>
                    <p style="text-align: center; margin-top: 10px; font-size: 0.8rem; color: #888;">
                        Your request will be sent to your manager for approval.
                    </p>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
