<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Broadcast | Admin</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<div class="app-container">
    <?php 
    $sidebarPath = __DIR__ . '/../../../Modules/CasjoeERP/views/layout/sidebar.php';
    if (file_exists($sidebarPath)) {
        require $sidebarPath; 
    }
    ?>
    <main class="main-content">
        <div class="top-bar">
            <h2><a href="/admin" style="text-decoration:none; color:inherit;">Admin</a> / Broadcast</h2>
        </div>

        <div style="background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 20px auto;">
            <?php if(isset($_GET['success'])): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="checkmark-circle"></ion-icon> Broadcast sent successfully to all users!
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/broadcast/send">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Subject Line</label>
                    <input type="text" name="subject" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;" placeholder="e.g. Important Update: Maintenance Scheduled" required>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Message Body (HTML Supported)</label>
                    <textarea name="message" class="form-control" rows="10" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: sans-serif;" placeholder="Write your message here..." required></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Recipients</label>
                    <select name="group" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="all">All Users</option>
                        <option value="admins">Admins Only</option>
                        <option value="active">Active Subscribers</option>
                    </select>
                </div>

                <button type="submit" class="btn" style="padding: 12px 30px; display: flex; align-items: center; gap: 10px;">
                    <ion-icon name="paper-plane-outline"></ion-icon> Send Broadcast
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
