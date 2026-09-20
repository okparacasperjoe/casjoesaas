<?php
// Shared Folder Portal View
$permission = $folder['share_permission'] ?? 'editor';
$canEdit = ($permission === 'editor');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Vault Folder: <?= htmlspecialchars($folder['name'] ?? 'Shared Folder') ?> - Casjoe Enterprise Cloud</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        :root {
            --bg-deep: #050b18;
            --cloud-glass: rgba(15, 23, 42, 0.78);
            --cloud-border: rgba(255, 255, 255, 0.09);
            --cloud-gold: #FFA600;
        }
        * { box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body {
            margin: 0;
            background: radial-gradient(circle at top right, #0a1931 0%, #050b18 60%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        .shared-container {
            width: 100%;
            max-width: 1050px;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--cloud-glass);
            border: 1px solid var(--cloud-border);
            border-radius: 18px;
            padding: 22px 28px;
            margin-bottom: 24px;
            backdrop-filter: blur(20px);
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .folder-badge-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255, 166, 0, 0.15);
            color: #FFA600;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
        }
        .header-title {
            margin: 0;
            font-size: 1.55rem;
            font-weight: 800;
            color: #fff;
        }
        .header-sub {
            margin: 4px 0 0 0;
            font-size: 0.85rem;
            color: #94a3b8;
        }
        .perm-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 99px;
            text-transform: uppercase;
        }
        .perm-editor {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .perm-viewer {
            background: rgba(96, 165, 250, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(96, 165, 250, 0.3);
        }
        .dropzone-banner {
            background: rgba(10, 20, 48, 0.55);
            border: 2px dashed rgba(255, 166, 0, 0.4);
            border-radius: 18px;
            padding: 40px 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            margin-bottom: 28px;
        }
        .dropzone-banner:hover {
            border-color: #FFA600;
            background: rgba(255, 166, 0, 0.14);
            transform: translateY(-2px);
        }
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 16px;
        }
        .file-card {
            background: var(--cloud-glass);
            border: 1px solid var(--cloud-border);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.2s;
        }
        .file-card:hover {
            border-color: rgba(255,166,0,0.35);
        }
        .file-thumb-box {
            width: 100%;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            background: rgba(0,0,0,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .file-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .file-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .file-meta {
            font-size: 0.74rem;
            color: #94a3b8;
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
        }
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #ef4444;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
        }
    </style>
</head>
<body>

<div class="shared-container">
    <div class="header-bar">
        <div class="header-left">
            <div class="folder-badge-icon">
                <ion-icon name="folder-open"></ion-icon>
            </div>
            <div>
                <h1 class="header-title"><?= htmlspecialchars($folder['name']) ?></h1>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">
                    <span class="perm-badge <?= $canEdit ? 'perm-editor' : 'perm-viewer' ?>">
                        <ion-icon name="<?= $canEdit ? 'create' : 'eye' ?>"></ion-icon>
                        <?= $canEdit ? 'Full Collaboration (Upload & Delete)' : 'View & Download Only' ?>
                    </span>
                </div>
            </div>
        </div>
        <div>
            <a href="/cloud" style="color: #60a5fa; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 6px;">
                <ion-icon name="cloud-outline"></ion-icon> Open My Cloud
            </a>
        </div>
    </div>

    <?php if ($canEdit): ?>
        <!-- Hidden Upload Form -->
        <form action="/cloud/share/upload" method="POST" enctype="multipart/form-data" id="uploadForm" style="display:none;">
            <input type="hidden" name="share_token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            <input type="file" name="file" id="uploadInput" onchange="document.getElementById('uploadForm').submit()">
        </form>

        <!-- Upload Zone -->
        <div class="dropzone-banner" id="dropzoneBanner" onclick="document.getElementById('uploadInput').click()">
            <ion-icon name="cloud-upload" style="font-size: 2.6rem; color: var(--cloud-gold);"></ion-icon>
            <div style="font-weight: 700; font-size: 1.05rem; margin-top: 8px; color: #fff;">Click or Drag &amp; Drop Files to Upload to This Folder</div>
            <div style="font-size: 0.82rem; color: #94a3b8; margin-top: 4px;">You have Editor permission to upload directly to this folder</div>
        </div>
    <?php else: ?>
        <div style="background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.25); border-radius: 14px; padding: 16px 20px; margin-bottom: 24px; font-size: 0.88rem; color: #93c5fd; display: flex; align-items: center; gap: 10px;">
            <ion-icon name="information-circle" style="font-size: 1.4rem; color: #60a5fa;"></ion-icon>
            <span>You have <strong>View &amp; Download Only</strong> access to this folder. Uploading and deleting are disabled by the folder owner.</span>
        </div>
    <?php endif; ?>

    <div style="font-weight: 700; font-size: 1.1rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <ion-icon name="documents" style="color: #60a5fa;"></ion-icon> Shared Folder Files (<?= count($files) ?>)
    </div>

    <div class="file-grid">
        <?php foreach ($files as $file): ?>
            <?php
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $isMedia = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'mp4', 'webm', 'mov']);
            $icon = 'document-text';
            $badgeColor = '#60a5fa';
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) {
                $icon = 'image';
                $badgeColor = '#a855f7';
            } elseif (in_array($ext, ['mp4', 'webm', 'mov'])) {
                $icon = 'videocam';
                $badgeColor = '#ec4899';
            } elseif (in_array($ext, ['pdf'])) {
                $icon = 'document';
                $badgeColor = '#ef4444';
            } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                $icon = 'stats-chart';
                $badgeColor = '#10b981';
            }
            ?>
            <div class="file-card" style="cursor: pointer;" onclick="handleFileClick(event, '<?= $file['id'] ?>', '<?= htmlspecialchars(addslashes($file['name'])) ?>', '<?= $ext ?>', <?= $isMedia ? 'true' : 'false' ?>)">
                <?php if ($canEdit): ?>
                    <form action="/cloud/share/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Delete this file?')">
                        <input type="hidden" name="share_token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                        <input type="hidden" name="id" value="<?= $file['id'] ?>">
                        <button type="submit" class="delete-btn" title="Delete File" onclick="event.stopPropagation()">
                            <ion-icon name="trash"></ion-icon>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'])): ?>
                    <div class="file-thumb-box">
                        <img src="/cloud/preview?id=<?= $file['id'] ?>&share_token=<?= urlencode($_GET['token'] ?? '') ?>" alt="<?= htmlspecialchars($file['name']) ?>" loading="lazy">
                    </div>
                <?php else: ?>
                    <div class="file-thumb-box" style="background: rgba(255,255,255,0.03); flex-direction: column; gap: 6px;">
                        <ion-icon name="<?= $icon ?>" style="font-size: 2.8rem; color: <?= $badgeColor ?>;"></ion-icon>
                        <span style="font-size: 0.72rem; font-weight: 800; color: <?= $badgeColor ?>;"><?= htmlspecialchars(strtoupper($ext ?: 'FILE')) ?></span>
                    </div>
                <?php endif; ?>
                <div class="file-name"><?= htmlspecialchars($file['name']) ?></div>
                <div class="file-meta" style="align-items: center;">
                    <span><?= round($file['size_bytes'] / 1024, 2) ?> KB</span>
                    <a href="/cloud/share/download?id=<?= $file['id'] ?>&token=<?= urlencode($_GET['token'] ?? '') ?>" onclick="event.stopPropagation()" style="background: rgba(255,166,0,0.18); color: #FFA600; border: 1px solid rgba(255,166,0,0.35); text-decoration: none; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; gap: 4px;">
                        <ion-icon name="download-outline"></ion-icon> Download
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($files)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 14px;">
                <ion-icon name="folder-open-outline" style="font-size: 3.5rem; color: #94a3b8; opacity: 0.5;"></ion-icon>
                <h3 style="color: #fff; margin: 10px 0 6px 0;">No Files Yet</h3>
                <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">No documents inside this folder yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- In-App Media Lightbox Modal -->
<div id="mediaLightboxModal" style="display:none; position:fixed; inset:0; background:rgba(5,11,24,0.92); backdrop-filter:blur(15px); z-index:999999; align-items:center; justify-content:center; padding:30px;">
    <div style="position:relative; max-width:900px; width:100%; max-height:90vh; display:flex; flex-direction:column; align-items:center;">
        <div style="width:100%; display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 id="lightboxTitle" style="margin:0; font-size:1.15rem; color:#fff; word-break:break-all;"></h3>
            <div style="display:flex; gap:10px;">
                <a id="lightboxDownload" href="#" download class="perm-editor" style="text-decoration:none; padding:8px 16px; border-radius:10px; font-size:0.85rem; display:inline-flex; align-items:center; gap:6px;">
                    <ion-icon name="download-outline"></ion-icon> Download
                </a>
                <button onclick="closeLightbox()" style="background:rgba(255,255,255,0.1); border:none; color:#fff; width:36px; height:36px; border-radius:10px; font-size:1.4rem; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <ion-icon name="close"></ion-icon>
                </button>
            </div>
        </div>
        <div id="lightboxContent" style="max-height:80vh; overflow:hidden; display:flex; align-items:center; justify-content:center; border-radius:14px; background:rgba(0,0,0,0.5); padding:10px; width:100%;">
            <!-- Media renders here -->
        </div>
    </div>
</div>

<script>
    function handleFileClick(e, id, name, ext, isMedia) {
        if (e.target.closest('form') || e.target.closest('button')) return;
        if (isMedia) {
            openLightbox(id, name, ext);
        } else {
            window.open('/cloud/preview?id=' + id + '&share_token=<?= urlencode($_GET['token'] ?? '') ?>', '_blank');
        }
    }

    function openLightbox(id, name, ext) {
        const title = document.getElementById('lightboxTitle');
        const content = document.getElementById('lightboxContent');
        const dl = document.getElementById('lightboxDownload');
        title.textContent = name;
        dl.href = '/cloud/share/download?id=' + id + '&token=<?= urlencode($_GET['token'] ?? '') ?>';

        const previewUrl = '/cloud/preview?id=' + id + '&share_token=<?= urlencode($_GET['token'] ?? '') ?>';
        if (['mp4', 'webm', 'mov'].includes(ext)) {
            content.innerHTML = '<video src="' + previewUrl + '" controls autoplay style="max-width:100%; max-height:75vh; border-radius:10px;"></video>';
        } else {
            content.innerHTML = '<img src="' + previewUrl + '" style="max-width:100%; max-height:75vh; object-fit:contain; border-radius:10px;">';
        }
        document.getElementById('mediaLightboxModal').style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightboxContent').innerHTML = '';
        document.getElementById('mediaLightboxModal').style.display = 'none';
    }

    <?php if ($canEdit): ?>
    const dropzone = document.getElementById('dropzoneBanner');
    const uploadInput = document.getElementById('uploadInput');
    const uploadForm = document.getElementById('uploadForm');

    if (dropzone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
            window.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.style.borderColor = '#FFA600';
                dropzone.style.background = 'rgba(255, 166, 0, 0.2)';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.style.borderColor = 'rgba(255, 166, 0, 0.4)';
                dropzone.style.background = 'rgba(10, 20, 48, 0.55)';
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                uploadInput.files = files;
                dropzone.innerHTML = '<ion-icon name="sync" style="font-size: 2.6rem; color: #FFA600;"></ion-icon><div style="font-weight: 700; font-size: 1.05rem; margin-top: 8px; color: #fff;">Uploading your file...</div>';
                uploadForm.submit();
            }
        }, false);
    }
    <?php endif; ?>
</script>
</body>
</html>
