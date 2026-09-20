<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Select File | Casjoe Cloud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body { background: #f8fafc; padding: 20px; font-family: 'Segoe UI', sans-serif; }
        .picker-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; }
        
        .file-item {
            background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;
            text-align: center; cursor: pointer; transition: 0.2s;
        }
        .file-item:hover { border-color: #000066; background: #f1f5f9; }
        .file-preview { height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 5px; overflow: hidden; }
        .file-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .file-preview ion-icon { font-size: 40px; color: #94a3b8; }
        .file-name { font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .folder-item { background: #e0e7ff; border-color: #c7d2fe; }
        .folder-item ion-icon { color: #000066; }
    </style>
</head>
<body>
    <div class="picker-header">
        <h3 style="margin: 0; color: #000066;">Select File</h3>
        <button onclick="window.close()" class="btn btn-sm" style="background: #ccc; border:none; padding: 5px 10px; cursor: pointer;">Cancel</button>
    </div>

    <!-- Breadcrumbs -->
    <div id="breadcrumbs" style="margin-bottom: 15px; font-size: 13px; color: #64748b;">
        <span onclick="loadFiles(null)" style="cursor: pointer; color: #000066; font-weight: bold;">Home</span>
    </div>

    <div id="fileGrid" class="picker-grid">
        <!-- Dynamically Loaded -->
        <p style="grid-column: 1/-1; text-align: center;">Loading...</p>
    </div>

    <script>
        let currentFolder = null;

        async function loadFiles(folderId) {
            currentFolder = folderId;
            const grid = document.getElementById('fileGrid');
            grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">Loading...</p>';

            try {
                const res = await fetch(`/cloud/files?folder_id=${folderId || ''}&type=image`); // Filter for images mostly
                const data = await res.json();
                
                grid.innerHTML = '';
                
                // Render Breadcrumbs
                const crumbs = document.getElementById('breadcrumbs');
                let crumbHtml = `<span onclick="loadFiles(null)" style="cursor: pointer; color: #000066; font-weight: bold;">Home</span>`;
                if (data.breadcrumb) {
                    data.breadcrumb.forEach(b => {
                        crumbHtml += ` / <span onclick="loadFiles(${b.id})" style="cursor: pointer;">${b.name}</span>`;
                    });
                }
                crumbs.innerHTML = crumbHtml;

                // Render Folders
                data.folders.forEach(f => {
                    const div = document.createElement('div');
                    div.className = 'file-item folder-item';
                    div.onclick = () => loadFiles(f.id);
                    div.innerHTML = `
                        <div class="file-preview"><ion-icon name="folder"></ion-icon></div>
                        <div class="file-name">${f.name}</div>
                    `;
                    grid.appendChild(div);
                });

                // Render Files
                if (data.files.length === 0 && data.folders.length === 0) {
                     grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">No files found.</p>';
                } else {
                    data.files.forEach(f => {
                        const div = document.createElement('div');
                        div.className = 'file-item';
                        div.onclick = () => selectFile(f);
                        
                        let icon = '<ion-icon name="document-text"></ion-icon>';
                        if (f.type.startsWith('image/')) {
                            // Ensure URL is absolute for preview if needed, or relative
                            icon = `<img src="${f.url}" alt="${f.name}">`;
                        }
                        
                        div.innerHTML = `
                            <div class="file-preview">${icon}</div>
                            <div class="file-name">${f.name}</div>
                        `;
                        grid.appendChild(div);
                    });
                }

            } catch (e) {
                grid.innerHTML = '<p style="color: red; grid-column: 1/-1; text-align: center;">Failed to load files.</p>';
                console.error(e);
            }
        }

        function selectFile(file) {
            // Post message to parent
            if (window.opener) {
                window.opener.postMessage({ type: 'fileSelected', url: file.url }, '*');
                window.close();
            } else {
                alert('Selected: ' + file.url + '\n(No parent window found)');
            }
        }

        // Init
        loadFiles(null);
    </script>
</body>
</html>
