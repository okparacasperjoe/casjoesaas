<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    <main class="main-content">
        <h2>Support Center</h2>
        <div class="card">
            <h3>Contact Support</h3>
            <p>For assistance, please contact the IT department.</p>
            <form>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" rows="5"></textarea>
                </div>
                <button type="submit" class="btn">Send Ticket</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
