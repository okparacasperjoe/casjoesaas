<?php $active = $active ?? 'dashboard'; ?>
<aside class="sidebar">
    <?php include __DIR__ . '/sidebar_cloud_css.php'; ?>
    <div class="cloud-brand">
        <a href="/dashboard" style="text-decoration:none; display:flex; align-items:center;">
            <img src="/assets/casjoe_logo.webp" alt="Casjoe Cloud" style="height: 40px;">
        </a>
    </div>
    <ul class="cloud-menu">
        <li class="cloud-item">
            <a href="/dashboard" class="cloud-link">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Apps
            </a>
        </li>
        <li class="cloud-item">
            <a href="/ai-office" class="cloud-link" style="border-left: 3px solid #FFA600; background: rgba(255,166,0,0.05);">
                <ion-icon name="briefcase"></ion-icon> AI Office
            </a>
        </li>
        <li class="cloud-item">
            <a href="/cloud" class="cloud-link <?= $active == 'dashboard' ? 'active' : '' ?>">
                <ion-icon name="cloud-outline"></ion-icon> My Cloud
            </a>
        </li>
        <li class="cloud-item">
            <a href="javascript:void(0)" onclick="loadSharedItemsInUI()" class="cloud-link">
                <ion-icon name="people-outline"></ion-icon> Shared with me
            </a>
        </li>
    </ul>

    <div class="folder-section-title">
        Folders
    </div>
    <div id="sidebarFolderTree" style="padding: 10px 20px; font-size: 13px;">
        <p style="color: rgba(255,255,255,0.5);">Loading...</p>
    </div>
</aside>

<style>
    /* Inline overrides for folder tree specific to sidebar context */
    .tree-item {
        margin: 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255,255,255,0.7);
        cursor: pointer;
        transition: color 0.2s;
        font-weight: 500;
    }
    .tree-item:hover { color: #FFA600 !important; }
    .tree-item ion-icon { color: #fbbf24; font-size: 18px; }
    .tree-sub { margin-left: 12px; border-left: 1px solid rgba(255,255,255,0.1); padding-left: 12px; }
</style>

<script>
async function loadSidebarTree() {
    try {
        const res = await fetch('/cloud/folder-tree');
        const data = await res.json();
        const container = document.getElementById('sidebarFolderTree');
        
        if (!data.tree || data.tree.length === 0) {
            container.innerHTML = '<p style="color: #94a3b8;">No folders</p>';
            return;
        }

        container.innerHTML = renderTreeBranch(data.tree);
    } catch (e) {
        console.error(e);
    }
}

function renderTreeBranch(nodes) {
    return nodes.map(node => `
        <div class="tree-node">
            <div class="tree-item" onclick="openFolder(${node.id})">
                <ion-icon name="folder"></ion-icon>
                <span>${node.name}</span>
            </div>
            ${node.children ? `<div class="tree-sub">${renderTreeBranch(node.children)}</div>` : ''}
        </div>
    `).join('');
}

loadSidebarTree();
</script>
