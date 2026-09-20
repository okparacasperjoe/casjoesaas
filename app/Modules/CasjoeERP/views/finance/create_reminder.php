<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice Reminder | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
                width: 260px !important;
                background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/../layout/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">

        <div class="content-header">
            <h1>Create Reminder</h1>
            <a href="/erp/finance/reminders" class="btn btn-secondary">Back</a>
        </div>

        <div class="card">
            <form action="/erp/finance/reminders/store" method="POST">
                
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Sequence Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. 7 Days Overdue Reminder" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Timing (Days from Due Date)</label>
                        <input type="number" name="days_offset" class="form-control" value="0" required>
                        <small style="color: #666;">Example: "7" means 7 days after due date. "-3" means 3 days before.</small>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Repeat Frequency</label>
                    <select name="frequency_days" class="form-control" required>
                        <option value="0">Don't Repeat (Once)</option>
                        <option value="1">Every Day</option>
                        <option value="3">Every 3 Days</option>
                        <option value="7">Every Week</option>
                        <option value="30">Every Month</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Email Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Action Required: Unpaid Invoice" required>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Email Body Template</label>
                    <textarea name="body" class="form-control" rows="8" required>Hi {client_name},

This is a reminder that your invoice for {invoice_amount} is currently unpaid.

You can view and pay your invoice securely here:
{invoice_link}

Thank you!</textarea>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        <strong>Available Variables:</strong> 
                        <code>{client_name}</code>, <code>{invoice_amount}</code>, <code>{invoice_link}</code>, <code>{due_date}</code>
                    </small>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>
                        <input type="checkbox" name="is_active" checked> Activate this reminder immediately
                    </label>
                </div>

                <button type="submit" class="btn btn-success" style="margin-top: 20px;">Save Reminder</button>
            </form>
        </div>

    </main>
</div>
</body>
</html>
