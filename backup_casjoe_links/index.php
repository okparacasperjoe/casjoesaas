<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Static Websites | Casjoe Links</title>
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
                <h1>Static Websites</h1>
                <a href="/links/static/create" class="btn">Create New Website</a>
            </div>

            <div class="card">
                <?php if (empty($sites)): ?>
                    <p style="text-align: center; color: #888; padding: 20px;">No static websites created yet.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #eee;">
                                <th style="padding: 10px;">Website Name</th>
                                <th style="padding: 10px;">URL</th>
                                <th style="padding: 10px;">Status</th>
                                <th style="padding: 10px;">Created</th>
                                <th style="padding: 10px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sites as $site): ?>
                                <tr style="border-bottom: 1px solid #f5f5f5;">
                                    <td style="padding: 10px; font-weight: 500;"><?= htmlspecialchars($site['subdomain']) ?></td>
                                    <td style="padding: 10px;"><a href="/sites/<?= htmlspecialchars($site['subdomain']) ?>/" target="_blank" style="color: blue;">/sites/<?= htmlspecialchars($site['subdomain']) ?></a></td>
                                    <td style="padding: 10px;"><?= htmlspecialchars(ucfirst($site['status'])) ?></td>
                                    <td style="padding: 10px;"><?= date('M d, Y', strtotime($site['created_at'])) ?></td>
                                    <td style="padding: 10px;">
                                        <div style="display: flex; gap: 8px;">
                                            <!-- Share/Copy URL Button -->
                                            <button class="btn btn-sm" 
                                                    style="background: #e3f2fd; color: #1976d2; border: 1px solid #1976d2;"
                                                    onclick="copyToClipboard('/sites/<?= htmlspecialchars($site['subdomain']) ?>/', this)"
                                                    title="Copy URL">
                                                <ion-icon name="share-outline"></ion-icon> Share
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <a href="/links/static/edit/<?= $site['id'] ?>" 
                                               class="btn btn-sm" 
                                               style="background: #fff3e0; color: #f57c00; border: 1px solid #f57c00;">
                                                <ion-icon name="create-outline"></ion-icon> Edit
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <form action="/links/static/delete/<?= $site['id'] ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this website? This cannot be undone.');">
                                                <button type="submit" class="btn btn-sm" style="background: #fee; color: red; border: 1px solid #ffcdd2;">
                                                    <ion-icon name="trash-outline"></ion-icon> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function copyToClipboard(path, button) {
        const fullUrl = window.location.origin + path;
        
        // Create temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = fullUrl;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Visual feedback
        const originalHTML = button.innerHTML;
        button.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon> Copied!';
        button.style.background = '#c8e6c9';
        button.style.color = '#2e7d32';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.background = '#e3f2fd';
            button.style.color = '#1976d2';
        }, 2000);
    }
    </script>
</body>
</html>

