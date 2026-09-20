<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>New Client | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>Add New Client</h2>
            <a href="/erp/crm" class="btn" style="background: #666;">Cancel</a>
        </div>

        <div class="card" style="max-width: 600px; margin: auto;">
            <form method="POST" action="/erp/clients/store">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn" style="width: 100%;">Create Client</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
