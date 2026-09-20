<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <title>Shared Folder | Casjoe Cloud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --cloud-primary: #000066; 
            --cloud-accent: #FFA600;  
            --cloud-bg: #f8fafc;
        }

        body { background: var(--cloud-bg); margin: 0; font-family: "Inter", sans-serif; }
        .main-content { background: var(--cloud-bg); min-height: 100vh; padding: 2rem; max-width: 1200px; margin: 0 auto; }
        
        .header { display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem; }
        .header-logo { width: 40px; height: 40px; background: var(--cloud-primary); display: flex; align-items: center; justify-content: center; border-radius: 8px; color: white; }
        .header-title h2 { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0; }
        .header-subtitle { font-size: 13px; color: #64748b; }

        .cloud-tools { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
        .tool-group { display: flex; gap: 0.75rem; align-items: center; }

        .breadcrumb-nav { display: flex; list-style: none; padding: 0; margin: 0; font-size: 14px; color: #64748b; }
        .breadcrumb-item + .breadcrumb-item::before { content: "/"; padding: 0 10px; color: #cbd5e1; }
        .breadcrumb-item a { color: var(--cloud-primary); text-decoration: none; font-weight: 600; cursor: pointer; }

        .explorer-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1.25rem; }
        .item-card {
            background: white; border-radius: 12px; padding: 0.75rem; border: 1px solid #f1f5f9;
            transition: all 0.2s; position: relative; cursor: pointer; text-align: center;
        }
        .item-card:hover { border-color: var(--cloud-accent); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); transform: translateY(-2px); }
        .item-preview {
            width: 100%; height: 90px; background: #f8fafc; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 10px; overflow: hidden;
        }
        .item-preview img { width: 100%; height: 100%; object-fit: cover; }
        .item-preview ion-icon { font-size: 40px; color: #94a3b8; }
        .item-name { font-size: 12px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .empty-state {
            grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #94a3b8;
            background: rgba(255,255,255,0.5); border-radius: 16px; border: 2px dashed #e2e8f0;
        }
        .empty-state ion-icon { font-size: 64px; color: #cbd5e1; margin-bottom: 15px; }

        .item-menu-btn { position: absolute; top: 5px; right: 5px; color: #94a3b8; padding: 5px; border-radius: 4px; z-index: 10; cursor: pointer; }
        .item-dropdown {
            position: absolute; top: 30px; right: 5px; background: white; border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid #e2e8f0; z-index: 2000;
            display: none; width: 140px; text-align: left;
        }
        .dropdown-item { padding: 8px 12px; font-size: 13px; color: #475569; display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .dropdown-item:hover { background: #f8fafc; color: var(--cloud-primary); }

        .view-switcher { display: flex; background: #f1f5f9; border-radius: 8px; padding: 4px; }
        .view-btn { padding: 6px 10px; border-radius: 6px; border: none; color: #64748b; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .view-btn.active { background: white; color: var(--cloud-primary); box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

        @media (max-width: 768px) {
            .main-content { padding: 1rem; }
            .explorer-grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; }
            .breadcrumb-nav { font-size: 12px; overflow-x: auto; white-space: nowrap; padding-bottom: 5px; max-width: 100%; display: block; }
        }
    </style>
</head>
<body>

<main class="main-content">
    <div class="header">
        <div class="header-logo"><ion-icon name="cloud"></ion-icon></div>
        <div>
            <div class="header-title"><h2>Shared Folder</h2></div>
            <div class="header-subtitle">Casjoe Cloud</div>
        </div>
    </div>

    <div class="cloud-tools">
        <div class="tool-group" style="overflow: hidden;">
            <div class="breadcrumb-nav" id="breadcrumbNav"></div>
        </div>
        <div class="tool-group">
            <div class="view-switcher">
                <button class="view-btn active" id="btnGrid" onclick="setView('grid')"><ion-icon name="grid-outline"></ion-icon></button>
                <button class="view-btn" id="btnList" onclick="setView('list')"><ion-icon name="list-outline"></ion-icon></button>
            </div>
        </div>
    </div>

    <div id="explorerContainer" class="explorer-grid"></div>

</main>

<script>
    const shareToken = '<?= htmlspecialchars($shareToken ?? '') ?>';
    let currentFolderId = null;
    let sharePermission = 'view';
    let rootFolderId = null;

    async function loadSharedFolder() {
        const container = document.getElementById('explorerContainer');
        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 100px; color: #94a3b8;"><ion-icon name="sync-outline" style="font-size: 30px; animation: spin 1s linear infinite;"></ion-icon><div style="margin-top:10px;">Loading...</div></div>';
        
        try {
            const res = await fetch(`/cloud/share/api/${shareToken}?folder_id=${currentFolderId || ''}`);
            const data = await res.json();
            
            if (data.error) {
                container.innerHTML = `<div class="empty-state"><ion-icon name="alert-circle-outline"></ion-icon><h3>Error</h3><p>${data.error}</p></div>`;
                return;
            }

            sharePermission = data.permission;
            rootFolderId = data.root_id;
            updateBreadcrumbs(data.breadcrumb);
            
            let html = '';
            data.folders.forEach(f => html += renderItem(f, 'folder'));
            data.files.forEach(f => html += renderItem(f, 'file'));
            
            if (html === '') {
                html = `
                    <div class="empty-state">
                        <ion-icon name="folder-open-outline"></ion-icon>
                        <h3>This folder is empty</h3>
                    </div>
                `;
            }
            container.innerHTML = html;
        } catch (e) {
            console.error(e);
            container.innerHTML = `<div class="empty-state"><h3>Failed to load folder contents</h3></div>`;
        }
    }

    function renderItem(item, type) {
        return `
            <div class="item-card" onclick="${type==='folder' ? `openFolder(${item.id})` : ''}">
                <div class="item-menu-btn" onclick="toggleMenu(event, ${item.id}, '${type}')">
                    <ion-icon name="ellipsis-vertical"></ion-icon>
                </div>
                <div class="item-dropdown" id="dropdown-${type}-${item.id}">
                    ${type !== 'folder' ? `<div class="dropdown-item" onclick="viewFile(event, '${item.url}')"><ion-icon name="eye-outline"></ion-icon> View</div>` : ''}
                    ${type !== 'folder' && sharePermission === 'download' ? `<div class="dropdown-item" onclick="downloadFile(event, '${item.url}')"><ion-icon name="download-outline"></ion-icon> Download</div>` : ''}
                </div>
                <div class="item-preview">
                    ${type === 'folder' ? '<ion-icon name="folder" style="color: #fbbf24;"></ion-icon>' : 
                        (item.type && item.type.startsWith('image/') ? `<img src="${item.url}" alt="${item.name}">` : 
                        (item.type && item.type.startsWith('video/') ? `<video src="${item.url}#t=0.1" preload="metadata" style="width:100%; height:100%; object-fit:cover;" muted></video>` : `<ion-icon name="document-outline"></ion-icon>`))}
                </div>
                <div class="item-name" title="${item.name}">${item.name}</div>
            </div>
        `;
    }

    function openFolder(id) { 
        currentFolderId = id; 
        loadSharedFolder(); 
    }

    function viewFile(e, url) { 
        e.stopPropagation(); 
        window.open(url, '_blank'); 
    }

    function downloadFile(e, url) {
        e.stopPropagation();
        const link = document.createElement('a');
        link.href = url + (url.includes('?') ? '&' : '?') + 'download=1';
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function updateBreadcrumbs(crumbs) {
        const nav = document.getElementById('breadcrumbNav');
        let html = '';
        if (crumbs && crumbs.length > 0) {
            crumbs.forEach((c, index) => {
                if (index === crumbs.length - 1) {
                    html += `<li class="breadcrumb-item" style="color: var(--cloud-primary); font-weight: 700;">${c.name}</li>`;
                } else {
                    html += `<li class="breadcrumb-item"><a onclick="openFolder(${c.id})">${c.name}</a></li>`;
                }
            });
        }
        nav.innerHTML = html;
    }

    function toggleMenu(e, id, type) {
        e.stopPropagation();
        const el = document.getElementById(`dropdown-${type}-${id}`);
        const allDropdowns = document.querySelectorAll('.item-dropdown');
        
        allDropdowns.forEach(d => { 
            if(d !== el) d.style.display = 'none'; 
        });

        if (el.style.display === 'block') {
            el.style.display = 'none';
        } else {
            el.style.display = 'block';
        }
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.item-dropdown').forEach(d => d.style.display = 'none');
    });

    function setView(viewMode) {
        const container = document.getElementById('explorerContainer');
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        
        if (viewMode === 'list') {
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            document.getElementById('btnList').classList.add('active');
            // Adding a class to cards for list mode
            container.querySelectorAll('.item-card').forEach(c => c.style.display = 'flex');
        } else {
            container.style.display = 'grid';
            container.style.flexDirection = 'row';
            document.getElementById('btnGrid').classList.add('active');
            container.querySelectorAll('.item-card').forEach(c => c.style.display = 'block');
        }
    }

    // Initialize
    loadSharedFolder();
</script>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

</body>
</html>
