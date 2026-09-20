<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>New Lead | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Add New Lead</h2>
            <a href="/erp/leads" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: auto;">
            <form method="POST" action="/erp/leads/store">
                <div class="row" style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="form-group">
                    <label>Source</label>
                    <select name="source" class="form-control">
                        <option value="website">Website</option>
                        <option value="referral">Referral</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="cold_call">Cold Call</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Add Lead</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
