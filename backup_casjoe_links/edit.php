<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Static Website | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        /* Fix Sidebar Contrast */
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: rgba(255, 255, 255, 0.1); }
        .sidebar .nav-link ion-icon { color: inherit !important; }
        .sidebar { border-right: 4px solid #FFA600 !important; }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand"><a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;"><img src="/assets/casjoe_logo.webp" alt="Casjoe Links" style="height: 30px;"></a></div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="/links" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a></li>
                <li class="nav-item"><a href="/links/bio" class="nav-link"><ion-icon name="person-outline"></ion-icon> Bio Pages</a></li>
                <li class="nav-item"><a href="/links/short" class="nav-link"><ion-icon name="link-outline"></ion-icon> Short URLs</a></li>
                <li class="nav-item"><a href="/links/qr" class="nav-link"><ion-icon name="qr-code-outline"></ion-icon> QR Codes</a></li>
                <li class="nav-item"><a href="/links/static" class="nav-link active"><ion-icon name="globe-outline"></ion-icon> Static Websites</a></li>
                <li class="nav-item"><a href="/dashboard" class="nav-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Apps</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Edit Static Website</h1>
                <a href="/links/static" class="btn" style="background: #666; color: white;">Cancel</a>
            </div>

            <div class="card">
                <?php if (isset($_GET['error'])): ?>
                    <div style="background: #fee; color: red; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                        <?php if ($_GET['error'] == 'missing_fields'): ?>
                            Please provide a website name.
                        <?php elseif ($_GET['error'] == 'invalid_name'): ?>
                            Website name can only contain lowercase letters, numbers, and hyphens.
                        <?php elseif ($_GET['error'] == 'name_taken'): ?>
                            This website name is already taken. Please choose another.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form action="/links/static/update/<?= $site['id'] ?>" method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Website Name (URL Path)</label>
                        <input type="text" name="subdomain" class="form-control" value="<?= htmlspecialchars($site['subdomain']) ?>" required 
                               placeholder="e.g., my-landing-page" 
                               pattern="[a-z0-9-]+" 
                               title="Only lowercase letters, numbers, and hyphens allowed">
                        <small style="color: #888; display: block; margin-top: 4px;">Current URL: /sites/<?= htmlspecialchars($site['subdomain']) ?>/</small>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Replace Site Files (Optional)</label>
                        <input type="file" name="site_file" class="form-control" accept=".zip">
                        <small style="color: #888; display: block; margin-top: 4px;">Upload a new ZIP file to replace files. For React/Node apps, upload the compiled 'dist' folder.</small>
                    </div>

                    <div id="build-indicator" style="display: none; background: #eef; color: #338; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95em;">
                        <ion-icon name="sync-outline" class="spin" style="vertical-align: middle;"></ion-icon> 
                        Updating and processing project... This may take a moment. Please do not close this page.
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" id="submit-btn" class="btn" style="background: #FFA600; color: #000066;">Update Website</button>
                        <a href="/links/static" class="btn" id="cancel-btn" style="background: #eee; color: #333;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.querySelector('input[name="site_file"]');
            if (fileInput.files.length > 0) {
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').style.opacity = '0.5';
                document.getElementById('submit-btn').innerText = 'Processing...';
                document.getElementById('cancel-btn').style.pointerEvents = 'none';
                document.getElementById('build-indicator').style.display = 'block';
            }
        });
    </script>
    
    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spin { animation: spin 1s linear infinite; }
    </style>
</body>
</html>
