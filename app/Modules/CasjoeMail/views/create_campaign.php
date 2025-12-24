<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Campaign | Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">
                <ion-icon name="mail"></ion-icon>
                Casjoe<span>Mail</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <ion-icon name="apps-outline"></ion-icon>
                        Back to Apps
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/mail" class="nav-link active">
                        <ion-icon name="grid-outline"></ion-icon>
                        Dashboard
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <h1>New Email Campaign</h1>
            </div>

            <div class="card" style="max-width: 800px; margin: 0 auto;">
                <form method="POST" action="/mail/campaigns/send">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Campaign Subject</label>
                        <input type="text" name="subject" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Recipients</label>
                        <select disabled
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; background: #eee;">
                            <option>All Users (Active)</option>
                        </select>
                        <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">Currently broadcasting to all
                            workspace users.</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">Email Body (HTML supported)</label>
                        <textarea name="body" rows="10" required
                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-family: monospace;"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn" style="flex: 1; padding: 12px;">Send Blast</button>
                        <a href="/mail" class="btn"
                            style="background: transparent; border: 1px solid #ccc; color: var(--text-color);">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>