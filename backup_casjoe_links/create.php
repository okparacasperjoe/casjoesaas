<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Static Website | Casjoe Links</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --primary: #000066;
            --accent: #FFA600;
            --bg-gradient: linear-gradient(135deg, #f6f8fb 0%, #e5ebf4 100%);
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-dark);
            margin: 0;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar { border-right: 4px solid var(--accent) !important; background: var(--primary); }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: rgba(255, 255, 255, 0.1); }
        .sidebar .nav-link ion-icon { color: inherit !important; }

        .main-content {
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            box-sizing: border-box;
        }

        .premium-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.12);
        }

        .header-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .header-section h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 10px 0;
        }

        .header-section p {
            color: var(--text-muted);
            margin: 0;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-size: 15px;
        }

        .premium-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid rgba(0,0,0,0.05);
            border-radius: 12px;
            background: rgba(255,255,255,0.9);
            font-family: inherit;
            font-size: 15px;
            color: var(--text-dark);
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .premium-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(255, 166, 0, 0.15);
            background: #fff;
        }

        /* Drag and Drop Zone */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--accent);
            background: rgba(255, 166, 0, 0.05);
        }

        .upload-zone ion-icon {
            font-size: 48px;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .upload-zone h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
        }

        .upload-zone p {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: var(--text-muted);
        }

        .upload-zone input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-name-display {
            display: inline-block;
            background: rgba(0,0,102,0.1);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 35px;
        }

        .btn-premium {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,102,0.2);
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,102,0.3);
            background: #000088;
        }

        .btn-cancel {
            background: rgba(0,0,0,0.05);
            color: var(--text-dark);
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: rgba(0,0,0,0.08);
            color: var(--text-dark);
        }

        .alert-error {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #f87171;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .build-indicator {
            display: none;
            background: rgba(0,0,102,0.05);
            border: 1px solid rgba(0,0,102,0.1);
            color: var(--primary);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            animation: pulse 2s infinite;
        }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spin { animation: spin 1s linear infinite; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0,0,102,0.1); }
            70% { box-shadow: 0 0 0 10px rgba(0,0,102,0); }
            100% { box-shadow: 0 0 0 0 rgba(0,0,102,0); }
        }
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
            <div class="premium-card">
                <div class="header-section">
                    <h2>Deploy Static Website</h2>
                    <p>Upload your static files or Node.js project. We'll handle the rest.</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert-error">
                        <ion-icon name="alert-circle-outline" style="font-size: 20px;"></ion-icon>
                        <div>
                        <?php if ($_GET['error'] == 'missing_fields'): ?>
                            Please provide all required fields.
                        <?php elseif ($_GET['error'] == 'invalid_name'): ?>
                            Website name can only contain lowercase letters, numbers, and hyphens.
                        <?php elseif ($_GET['error'] == 'name_taken'): ?>
                            This website name is already taken. Please choose another.
                        <?php else: ?>
                            An error occurred during upload. Please try again.
                        <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="/links/static/store" method="POST" enctype="multipart/form-data" id="upload-form">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::generateToken() ?>">
                    
                    <div class="form-group">
                        <label>Website Name (URL Slug)</label>
                        <input type="text" name="subdomain" class="premium-input" required 
                               placeholder="e.g. my-awesome-project" 
                               pattern="[a-z0-9-]+" 
                               title="Lowercase letters, numbers and hyphens only">
                    </div>

                    <div class="form-group">
                        <label>Upload Project (.zip)</label>
                        <div class="upload-zone" id="drop-zone">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                            <h4>Drag & Drop your .zip file here</h4>
                            <p>or click to browse from your computer</p>
                            <input type="file" name="site_file" id="file-input" accept=".zip" required>
                            <span class="file-name-display" id="file-name" style="display: none;"></span>
                        </div>
                        <small style="color: var(--text-muted); display: block; margin-top: 10px; font-size: 13px; text-align: center;">
                            Supports HTML sites. For React/Next.js/Vite, please upload the compiled 'dist' or 'build' folder.
                        </small>
                    </div>

                    <div id="build-indicator" class="build-indicator">
                        <ion-icon name="sync-outline" class="spin" style="font-size: 18px; vertical-align: text-bottom; margin-right: 5px;"></ion-icon> 
                        Uploading & Processing Project...<br>
                        <span style="font-size: 12px; opacity: 0.8; font-weight: 400;">This may take a moment. Please do not close this window.</span>
                    </div>

                    <div class="action-buttons">
                        <a href="/links/static" class="btn-premium btn-cancel">Cancel</a>
                        <button type="submit" id="submit-btn" class="btn-premium btn-submit">
                            <ion-icon name="rocket-outline"></ion-icon> Upload & Deploy
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // File Upload UI Enhancements
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const fileName = document.getElementById('file-name');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            fileInput.files = files;
            updateFileName();
        }

        fileInput.addEventListener('change', updateFileName);

        function updateFileName() {
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
                fileName.style.display = 'inline-block';
            } else {
                fileName.style.display = 'none';
            }
        }

        // Form Submit Loading State
        document.getElementById('upload-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.innerHTML = '<ion-icon name="sync-outline" class="spin"></ion-icon> Deploying...';
            document.getElementById('build-indicator').style.display = 'block';
        });
    </script>
</body>
</html>

