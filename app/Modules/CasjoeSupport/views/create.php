<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Ticket | Casjoe Apps</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="brand">
             <img src="/assets/casjoe_logo.png" alt="Casjoe Apps" style="height: 40px;">
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/support" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to List</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h2>Open New Ticket</h2>
        </div>

        <div class="card" style="max-width: 800px; margin: auto;">
            <form method="POST">
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" required placeholder="Briefly describe the issue">
                </div>
                
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="8" required placeholder="Detailed explanation..."></textarea>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Submit Ticket</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>