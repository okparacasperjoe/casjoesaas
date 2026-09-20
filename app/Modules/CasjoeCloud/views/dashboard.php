<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Casjoe Cloud | Enterprise Vault & Secure Drive</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --cloud-bg: #070d1f;
            --cloud-glass: rgba(15, 23, 42, 0.65);
            --cloud-border: rgba(255, 255, 255, 0.08);
            --cloud-gold: #FFA600;
            --cloud-blue: #0044ff;
        }
        body {
            background: radial-gradient(circle at 15% 15%, #0d1b3e 0%, #070d1f 70%);
            color: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Top Bar & Breadcrumbs */
        .cloud-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 36px;
            background: rgba(10, 17, 40, 0.7);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--cloud-border);
            flex-wrap: wrap;
            gap: 16px;
        }
        .cloud-title-box h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .cloud-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn-cloud-primary {
            background: linear-gradient(135deg, var(--cloud-gold) 0%, #ff7b00 100%);
            color: #000;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.3);
            transition: transform 0.2s;
        }
        .btn-cloud-primary:hover {
            transform: translateY(-2px);
        }
        .btn-cloud-secondary {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-cloud-secondary:hover {
            background: rgba(255, 255, 255, 0.13);
        }

        /* Stats Row */
        .cloud-stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 18px 24px 14px 24px;
        }
        @media (max-width: 900px) {
            .cloud-stats-row {
                grid-template-columns: repeat(2, 1fr);
                margin: 14px 16px;
            }
        }
        .stat-card {
            background: var(--cloud-glass);
            border: 1px solid var(--cloud-border);
            border-radius: 12px;
            padding: 11px 13px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(15px);
        }
        .stat-card-label {
            font-size: 0.68rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .stat-card-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 2px;
        }
        .usage-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }
        .usage-fill {
            height: 100%;
            background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%);
        }

        /* Breadcrumb pill */
        .breadcrumbs-bar {
            margin: 0 36px 20px 36px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 0.9rem;
        }
        .breadcrumb-pill {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 999px;
            color: #cbd5e1;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .breadcrumb-pill:hover {
            background: rgba(255, 166, 0, 0.15);
            border-color: var(--cloud-gold);
            color: #fff;
        }

        /* Search & Filter Bar */
        .search-box {
            position: relative;
            max-width: 300px;
            width: 100%;
        }
        .search-box input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 0.88rem;
            outline: none;
        }
        .search-box ion-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
        }

        /* Drag & Drop Upload Zone */
        .dropzone-banner {
            margin: 0 36px 24px 36px;
            background: rgba(10, 20, 48, 0.45);
            border: 2px dashed rgba(255, 166, 0, 0.35);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
        }
        .dropzone-banner:hover {
            background: rgba(255, 166, 0, 0.07);
            border-color: var(--cloud-gold);
        }

        /* Grid */
        .content-area {
            padding: 0 36px 50px 36px;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(195px, 1fr));
            gap: 20px;
        }
        .file-card {
            background: var(--cloud-glass);
            border: 1px solid var(--cloud-border);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            transition: all 0.25s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .file-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 166, 0, 0.4);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .file-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .folder-box {
            background: rgba(255, 166, 0, 0.15);
            color: #FFA600;
        }
        .file-thumb-box {
            width: 100%;
            height: 130px;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .file-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .file-card:hover .file-thumb-box img {
            transform: scale(1.06);
        }
        .delete-floating-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(15, 23, 42, 0.88);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.2s;
        }
        .delete-floating-btn:hover {
            background: #ef4444;
            color: #fff;
        }
        .file-box {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }
        .file-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #f8fafc;
            word-break: break-all;
            margin-bottom: 6px;
        }
        .file-meta {
            font-size: 0.78rem;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .delete-btn {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .delete-btn:hover {
            background: #ef4444;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <?php include __DIR__ . '/partials/sidebar_cloud_css.php'; ?>
        <div class="cloud-brand">
            <ion-icon name="cloud-done" style="color: #FFA600; font-size: 2rem;"></ion-icon>
            <span>Casjoe Cloud</span>
        </div>
        
        <div style="padding: 20px;">
            <button class="btn-cloud-primary" style="width: 100%; justify-content: center;" onclick="document.getElementById('uploadInput').click()">
                <ion-icon name="cloud-upload-outline" style="font-size: 1.2rem;"></ion-icon> Upload Files
            </button>
            <form id="uploadForm" action="/cloud/upload" method="POST" enctype="multipart/form-data" style="display:none;">
                <input type="hidden" name="folder_id" value="<?= htmlspecialchars($folderId ?? '') ?>">
                <input type="file" id="uploadInput" name="file" onchange="document.getElementById('uploadForm').submit()">
            </form>
            <form id="uploadFolderForm" action="/cloud/upload" method="POST" enctype="multipart/form-data" style="display:none;">
                <input type="hidden" name="folder_id" value="<?= htmlspecialchars($folderId ?? '') ?>">
                <input type="file" id="uploadFolderInput" name="files[]" webkitdirectory multiple onchange="document.getElementById('uploadFolderForm').submit()">
            </form>
            
            <button class="btn-cloud-secondary" style="width: 100%; justify-content: center; margin-top: 12px;" onclick="createFolder()">
                <ion-icon name="folder-outline" style="font-size: 1.2rem;"></ion-icon> New Folder
            </button>
        </div>

        <?php
        $usedBytes = (int)($usedBytes ?? 0);
        $totalBytes = (int)($totalBytes ?? (20 * 1024 * 1024 * 1024));
        $usedMB = round($usedBytes / 1024 / 1024, 2);
        $pct = $totalBytes > 0 ? min(100, ($usedBytes / $totalBytes) * 100) : 0;
        ?>
        <div style="padding: 20px; margin: 0 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 14px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 6px;">
                <span style="color: rgba(255,255,255,0.7);">Storage</span>
                <span style="color: #fff; font-weight: bold;"><?= $usedMB ?> MB</span>
            </div>
            <div style="background: rgba(255,255,255,0.1); border-radius: 4px; height: 6px; overflow: hidden; margin-bottom: 8px;">
                <div style="width: <?= $pct ?>%; height: 100%; background: #FFA600; border-radius: 4px;"></div>
            </div>
            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.5);">20 GB Total</div>
        </div>

        <ul class="cloud-menu" style="margin-top: 20px;">
            <li class="cloud-item"><a href="/cloud" class="cloud-link active"><ion-icon name="folder-open"></ion-icon> Enterprise Vault</a></li>
            <li class="cloud-item"><a href="#" class="cloud-link" onclick="openAuditLogsModal(); return false;"><ion-icon name="list-circle-outline"></ion-icon> Access Logs</a></li>
            <li class="cloud-item"><a href="#" class="cloud-link" onclick="openSharingPoliciesModal(); return false;"><ion-icon name="options-outline"></ion-icon> Sharing Policies</a></li>
            <li class="cloud-item"><a href="#" class="cloud-link"><ion-icon name="shield-checkmark"></ion-icon> Encrypted Backups</a></li>
            <li class="cloud-item"><a href="/dashboard" class="cloud-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Apps</a></li>
        </ul>
    </aside>

    <main class="main-content" style="background: transparent;">
        <!-- Top Navigation Bar -->
        <div class="cloud-topbar">
            <div class="cloud-title-box">
                <h1>
                    <ion-icon name="cloud-circle" style="color: var(--cloud-gold);"></ion-icon>
                    Casjoe Enterprise Vault
                </h1>
            </div>
            <div class="cloud-actions">
                <div class="search-box">
                    <ion-icon name="search-outline"></ion-icon>
                    <input type="text" id="fileSearch" placeholder="Filter files or folders..." onkeyup="filterItems()">
                </div>
            </div>
        </div>

        <!-- Telemetry Stats Cards -->
        <!-- Telemetry & Access Log Stats Cards -->
        <div class="cloud-stats-row">
            <div class="stat-card" onclick="openAuditLogsModal()" style="cursor: pointer;" title="Click to view full access logs">
                <div class="stat-card-label">
                    <ion-icon name="eye-outline" style="color: #38bdf8; font-size: 1.15rem;"></ion-icon> Total Asset Views
                </div>
                <div class="stat-card-value"><?= number_format($logStats['view'] ?? 0) ?></div>
                <div style="font-size: 0.68rem; color: #94a3b8;">Page &amp; preview visits</div>
            </div>

            <div class="stat-card" onclick="openAuditLogsModal()" style="cursor: pointer;" title="Click to view full access logs">
                <div class="stat-card-label">
                    <ion-icon name="download-outline" style="color: #10b981; font-size: 1.15rem;"></ion-icon> File Downloads
                </div>
                <div class="stat-card-value"><?= number_format($logStats['download'] ?? 0) ?></div>
                <div style="font-size: 0.68rem; color: #10b981;">● Force login protected</div>
            </div>

            <div class="stat-card" onclick="openSharingPoliciesModal()" style="cursor: pointer;" title="Click to configure sharing policies">
                <div class="stat-card-label">
                    <ion-icon name="share-social-outline" style="color: var(--cloud-gold); font-size: 1.15rem;"></ion-icon> Shared Links
                </div>
                <div class="stat-card-value"><?= number_format($logStats['share'] ?? 0) ?></div>
                <div style="font-size: 0.68rem; color: #94a3b8;">Active enterprise tokens</div>
            </div>

            <div class="stat-card" onclick="openAuditLogsModal()" style="cursor: pointer;" title="Click to view full access logs">
                <div class="stat-card-label">
                    <ion-icon name="cloud-upload-outline" style="color: #a855f7; font-size: 1.15rem;"></ion-icon> Collaborator Uploads
                </div>
                <div class="stat-card-value"><?= number_format($logStats['upload'] ?? 0) ?></div>
                <div style="font-size: 0.68rem; color: #a855f7;">External asset uploads</div>
            </div>
        </div>

        <!-- Drag & Drop Upload Zone -->
        <div class="dropzone-banner" id="dropzoneBanner" onclick="document.getElementById('uploadInput').click()">
            <ion-icon name="cloud-upload" style="font-size: 2.4rem; color: var(--cloud-gold);"></ion-icon>
            <div style="font-weight: 700; font-size: 1rem; margin-top: 6px; color: #fff;">Click or Drag &amp; Drop Files Here to Upload</div>
            <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 4px;">Supports PDFs, Spreadsheets, Images, Archives &amp; Enterprise Assets</div>
        </div>

        <!-- Breadcrumbs -->
        <div class="breadcrumbs-bar">
            <span style="color: #64748b; font-weight: 600;">Location:</span>
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <a href="/cloud?folder=<?= $crumb['id'] ?>" class="breadcrumb-pill">
                    <ion-icon name="<?= $i === 0 ? 'home' : 'folder' ?>"></ion-icon>
                    <?= htmlspecialchars($crumb['name']) ?>
                </a>
                <?php if ($i < count($breadcrumbs) - 1): ?>
                    <span style="color: #475569;">/</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Main Items Grid -->
        <div class="content-area">
            <?php if (!empty($folders)): ?>
            <div class="section-title">
                <ion-icon name="folder-open" style="color: var(--cloud-gold);"></ion-icon> Folders (<?= count($folders) ?>)
            </div>
            <div class="file-grid" style="margin-bottom: 32px;">
                <?php foreach ($folders as $folder): ?>
                    <div class="file-card vault-item" onclick="if(!event.target.closest('button')){ window.location.href='/cloud?folder=<?= $folder['id'] ?>'; }" style="cursor: pointer;">
                        <div class="card-top">
                            <div class="file-icon-box folder-box">
                                <ion-icon name="folder"></ion-icon>
                            </div>
                            <button type="button" class="delete-floating-btn" style="border-color: rgba(255,166,0,0.5); color: #FFA600;" title="Share Folder" onclick="event.stopPropagation(); shareFolderModal('<?= $folder['id'] ?>', '<?= htmlspecialchars(addslashes($folder['name'])) ?>')">
                                <ion-icon name="share-social"></ion-icon>
                            </button>
                        </div>
                        <div>
                            <div class="file-name"><?= htmlspecialchars($folder['name']) ?></div>
                            <div class="file-meta">
                                <span>Folder Directory</span>
                                <span style="color: #FFA600; font-weight: 700;">Share</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="section-title">
                <ion-icon name="documents" style="color: #60a5fa;"></ion-icon> Files (<?= count($files) ?>)
            </div>
            <div class="file-grid">
                <?php foreach ($files as $file): ?>
                    <?php
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $icon = 'document-text';
                    $badgeColor = '#60a5fa';
                    if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) {
                        $icon = 'image';
                        $badgeColor = '#a855f7';
                    } elseif (in_array($ext, ['pdf'])) {
                        $icon = 'document';
                        $badgeColor = '#ef4444';
                    } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                        $icon = 'stats-chart';
                        $badgeColor = '#10b981';
                    } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar'])) {
                        $icon = 'archive';
                        $badgeColor = '#f59e0b';
                    }
                    $isMedia = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'mp4', 'webm', 'mov']);
                    ?>
                    <div class="file-card vault-item" style="cursor: pointer;" onclick="handleFileClick(event, '<?= $file['id'] ?>', '<?= htmlspecialchars(addslashes($file['name'])) ?>', '<?= $ext ?>', <?= $isMedia ? 'true' : 'false' ?>)">
                        <div style="position: absolute; top: 10px; right: 10px; display: flex; gap: 6px; z-index: 10;">
                            <button type="button" class="delete-floating-btn" style="position: static; border-color: rgba(255,166,0,0.5); color: #FFA600;" title="Share File" onclick="event.stopPropagation(); shareFileModal('<?= $file['id'] ?>', '<?= htmlspecialchars(addslashes($file['name'])) ?>')">
                                <ion-icon name="share-social"></ion-icon>
                            </button>
                            <form action="/cloud/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($file['name'])) ?> from vault?')">
                                <input type="hidden" name="id" value="<?= $file['id'] ?>">
                                <button type="submit" class="delete-floating-btn" style="position: static;" title="Delete File">
                                    <ion-icon name="trash"></ion-icon>
                                </button>
                            </form>
                        </div>
                        <?php if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'])): ?>
                            <div class="file-thumb-box">
                                <img src="/cloud/preview?id=<?= $file['id'] ?>" alt="<?= htmlspecialchars($file['name']) ?>" loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="file-thumb-box" style="background: rgba(255,255,255,0.03); flex-direction: column; gap: 8px;">
                                <ion-icon name="<?= $icon ?>" style="font-size: 3.2rem; color: <?= $badgeColor ?>;"></ion-icon>
                                <span style="font-size: 0.72rem; font-weight: 800; color: <?= $badgeColor ?>; letter-spacing: 1px;"><?= htmlspecialchars(strtoupper($ext ?: 'FILE')) ?></span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="file-name"><?= htmlspecialchars($file['name']) ?></div>
                            <div class="file-meta">
                                <span><?= round($file['size_bytes'] / 1024, 2) ?> KB</span>
                                <span style="text-transform: uppercase; font-size: 0.68rem; padding: 2px 6px; border-radius: 4px; background: rgba(255,255,255,0.08);"><?= htmlspecialchars($ext ?: 'FILE') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($folders) && empty($files)): ?>
                    <div style="grid-column: 1/-1; text-align: center; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.12); border-radius: 18px; padding: 60px 20px;">
                        <ion-icon name="cloud-outline" style="font-size: 4rem; color: var(--cloud-gold); opacity: 0.6;"></ion-icon>
                        <h3 style="margin: 12px 0 6px 0; color: #fff;">Your Vault Directory is Empty</h3>
                        <p style="color: #94a3b8; font-size: 0.9rem; margin: 0 0 18px 0;">Upload your enterprise files or create folders to keep documents organized and encrypted.</p>
                        <button class="btn-cloud-primary" onclick="document.getElementById('uploadInput').click()">
                            <ion-icon name="cloud-upload"></ion-icon> Upload First Document
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sharedFolders) || !empty($sharedFiles)): ?>
                    <div class="section-title" style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 28px;">
                        <ion-icon name="people" style="color: #38bdf8;"></ion-icon> Shared With Team / Staff (<?= count($sharedFolders) + count($sharedFiles) ?>)
                    </div>
                    <div class="file-grid" style="margin-bottom: 32px;">
                        <?php foreach ($sharedFolders as $folder): ?>
                            <div class="file-card vault-item" onclick="window.location.href='/cloud/share?token=<?= urlencode($folder['share_token']) ?>'" style="cursor: pointer; border-color: rgba(56, 189, 248, 0.3);">
                                <div class="card-top">
                                    <div class="file-icon-box folder-box" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                        <ion-icon name="folder"></ion-icon>
                                    </div>
                                    <span style="font-size: 0.72rem; color: #38bdf8; font-weight: 700; background: rgba(56, 189, 248, 0.15); padding: 3px 8px; border-radius: 6px;">SHARED</span>
                                </div>
                                <div class="file-name" style="margin-top: 12px;"><?= htmlspecialchars($folder['name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($sharedFiles as $file): ?>
                            <div class="file-card vault-item" onclick="window.location.href='/cloud/file/share?token=<?= urlencode($file['share_token']) ?>'" style="cursor: pointer; border-color: rgba(56, 189, 248, 0.3);">
                                <div class="card-top">
                                    <div class="file-icon-box" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                                        <ion-icon name="document"></ion-icon>
                                    </div>
                                    <span style="font-size: 0.72rem; color: #38bdf8; font-weight: 700; background: rgba(56, 189, 248, 0.15); padding: 3px 8px; border-radius: 6px;">SHARED</span>
                                </div>
                                <div class="file-name" style="margin-top: 12px;"><?= htmlspecialchars($file['name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
    function createFolder() {
        const name = prompt("Enter Vault Folder Name:");
        if (name && name.trim() !== "") {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/cloud/folder/create';
            
            const input = document.createElement('input');
            input.name = 'name';
            input.value = name.trim();
            form.appendChild(input);

            const parent = document.createElement('input');
            parent.type = 'hidden';
            parent.name = 'parent_id';
            parent.value = '<?= htmlspecialchars($folderId ?? '') ?>';
            form.appendChild(parent);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function filterItems() {
        const term = document.getElementById('fileSearch').value.toLowerCase();
        const items = document.querySelectorAll('.vault-item');
        items.forEach(item => {
            const text = item.querySelector('.file-name').textContent.toLowerCase();
            if (text.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // HTML5 Drag & Drop File Upload Handler
    const dropzone = document.getElementById('dropzoneBanner');
    const uploadInput = document.getElementById('uploadInput');
    const uploadForm = document.getElementById('uploadForm');

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
            dropzone.style.background = 'rgba(255, 166, 0, 0.18)';
            dropzone.style.transform = 'scale(1.01)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.style.borderColor = 'rgba(255, 166, 0, 0.35)';
            dropzone.style.background = 'rgba(10, 20, 48, 0.45)';
            dropzone.style.transform = 'scale(1)';
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            uploadInput.files = files;
            dropzone.innerHTML = '<ion-icon name="sync" style="font-size: 2.4rem; color: #FFA600;"></ion-icon><div style="font-weight: 700; font-size: 1rem; margin-top: 6px; color: #fff;">Uploading your file to Vault...</div>';
            uploadForm.submit();
        }
    }, false);

    let currentShareFolderId = null;

    async function shareFolderModal(id, name) {
        currentShareFolderId = id;
        document.getElementById('permissionSelectorWrap').style.display = 'block';
        const res = await fetch('/cloud/folder/share', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id)
        });
        const data = await res.json();
        if (data.url) {
            document.getElementById('shareModalName').textContent = name;
            document.getElementById('shareModalUrl').value = data.url;
            document.getElementById('sharePermissionSelect').value = data.permission || 'editor';
            document.getElementById('shareModal').style.display = 'flex';
        }
    }

    async function shareFileModal(id, name) {
        currentShareFolderId = null;
        document.getElementById('permissionSelectorWrap').style.display = 'none';
        const res = await fetch('/cloud/file/share', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id)
        });
        const data = await res.json();
        if (data.url) {
            document.getElementById('shareModalName').textContent = name;
            document.getElementById('shareModalUrl').value = data.url;
            document.getElementById('shareModal').style.display = 'flex';
        }
    }

    async function updateFolderPermission(perm) {
        if (!currentShareFolderId) return;
        const res = await fetch('/cloud/folder/share', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(currentShareFolderId) + '&permission=' + encodeURIComponent(perm)
        });
        const data = await res.json();
        if (data.url) {
            document.getElementById('shareModalUrl').value = data.url;
        }
    }

    async function updateFolderPassword(pwd) {
        if (!currentShareFolderId) return;
        const perm = document.getElementById('sharePermissionSelect').value;
        const res = await fetch('/cloud/folder/share', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(currentShareFolderId) + '&permission=' + encodeURIComponent(perm) + '&password=' + encodeURIComponent(pwd)
        });
        const data = await res.json();
        if (data.url) {
            document.getElementById('shareModalUrl').value = data.url;
        }
    }

    function copyShareUrl() {
        const input = document.getElementById('shareModalUrl');
        input.select();
        document.execCommand('copy');
        const btn = document.getElementById('copyBtn');
        btn.textContent = 'Copied!';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.textContent = 'Copy Link';
            btn.style.background = '#FFA600';
        }, 2000);
    }

    function handleFileClick(e, id, name, ext, isMedia) {
        if (e.target.closest('form') || e.target.closest('button')) return;
        if (isMedia) {
            openLightbox(id, name, ext);
        } else {
            window.open('/cloud/preview?id=' + id, '_blank');
        }
    }

    function openLightbox(id, name, ext) {
        const title = document.getElementById('lightboxTitle');
        const content = document.getElementById('lightboxContent');
        const dl = document.getElementById('lightboxDownload');
        title.textContent = name;
        dl.href = '/cloud/preview?id=' + id;

        if (['mp4', 'webm', 'mov'].includes(ext)) {
            content.innerHTML = '<video src="/cloud/preview?id=' + id + '" controls autoplay style="max-width:100%; max-height:75vh; border-radius:10px;"></video>';
        } else {
            content.innerHTML = '<img src="/cloud/preview?id=' + id + '" style="max-width:100%; max-height:75vh; object-fit:contain; border-radius:10px;">';
        }
        document.getElementById('mediaLightboxModal').style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightboxContent').innerHTML = '';
        document.getElementById('mediaLightboxModal').style.display = 'none';
    }
</script>

<!-- Share Folder Modal -->
<div id="shareModal" style="display:none; position:fixed; inset:0; background:rgba(5,11,24,0.85); backdrop-filter:blur(10px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:rgba(15,23,42,0.95); border:1px solid rgba(255,166,0,0.35); border-radius:20px; width:100%; max-width:520px; padding:28px; position:relative; box-shadow:0 25px 50px rgba(0,0,0,0.6);">
        <button onclick="document.getElementById('shareModal').style.display='none'" style="position:absolute; top:16px; right:16px; background:none; border:none; color:#94a3b8; font-size:1.6rem; cursor:pointer;"><ion-icon name="close"></ion-icon></button>
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <div style="width:48px; height:48px; border-radius:14px; background:rgba(255,166,0,0.18); color:#FFA600; display:flex; align-items:center; justify-content:center; font-size:1.8rem;">
                <ion-icon name="share-social"></ion-icon>
            </div>
            <div>
                <h3 style="margin:0; font-size:1.25rem; color:#fff;" id="shareModalName">Item Name</h3>
                <p style="margin:2px 0 0 0; font-size:0.8rem; color:#94a3b8;">Secure Enterprise Share Portal</p>
            </div>
        </div>
        <p style="font-size:0.85rem; color:#cbd5e1; line-height:1.4; margin-bottom:14px;">
            Anyone with this invite link will be prompted to <strong>create a Casjoe account or sign in</strong> before accessing this item.
        </p>

        <div id="permissionSelectorWrap" style="margin-bottom:18px; background:rgba(0,0,0,0.35); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 14px;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#FFA600; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:6px;">Folder Access Permission</label>
            <select id="sharePermissionSelect" onchange="updateFolderPermission(this.value)" style="width:100%; background:#0b1329; color:#fff; border:1px solid rgba(255,255,255,0.15); border-radius:8px; padding:10px; font-size:0.88rem;">
                <option value="editor">Editor (Collaborators can View, Upload &amp; Delete files)</option>
                <option value="viewer">Viewer (View &amp; Download Only - Uploading disabled)</option>
            </select>
        </div>

        <div id="passwordSelectorWrap" style="margin-bottom:16px; background:rgba(0,0,0,0.35); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 14px;">
            <label style="display:block; font-size:0.75rem; font-weight:700; color:#38bdf8; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:6px;">Optional Password Protection</label>
            <input type="password" id="sharePasswordInput" placeholder="Set folder password (optional)..." onchange="updateFolderPassword(this.value)" style="width:100%; box-sizing:border-box; background:#0b1329; color:#fff; border:1px solid rgba(255,255,255,0.15); border-radius:8px; padding:10px; font-size:0.88rem;">
        </div>

        <div style="display:flex; gap:8px;">
            <input type="text" id="shareModalUrl" readonly style="flex:1; background:rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.15); border-radius:10px; padding:10px 14px; color:#fff; font-size:0.85rem;">
            <button type="button" id="copyBtn" onclick="copyShareUrl()" style="background:#FFA600; color:#050b18; font-weight:700; border:none; border-radius:10px; padding:10px 18px; cursor:pointer; transition:all 0.2s;">Copy Link</button>
        </div>
    </div>
</div>

<!-- In-App Media Lightbox Modal -->
<div id="mediaLightboxModal" style="display:none; position:fixed; inset:0; background:rgba(5,11,24,0.92); backdrop-filter:blur(15px); z-index:999999; align-items:center; justify-content:center; padding:30px;">
    <div style="position:relative; max-width:900px; width:100%; max-height:90vh; display:flex; flex-direction:column; align-items:center;">
        <div style="width:100%; display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h3 id="lightboxTitle" style="margin:0; font-size:1.15rem; color:#fff; word-break:break-all;"></h3>
            <div style="display:flex; gap:10px;">
                <a id="lightboxDownload" href="#" download style="background:#FFA600; color:#050b18; font-weight:700; text-decoration:none; padding:8px 16px; border-radius:10px; font-size:0.85rem; display:inline-flex; align-items:center; gap:6px;">
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

<!-- Enterprise Audit Logs Modal -->
<div id="auditLogsModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(5,11,24,0.85); backdrop-filter:blur(10px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#0b1329; border:1px solid rgba(255,255,255,0.12); border-radius:20px; max-width:760px; width:100%; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 25px 60px rgba(0,0,0,0.6); overflow:hidden;">
        <div style="padding:22px 26px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <ion-icon name="list-circle-outline" style="font-size:1.6rem; color:#38bdf8;"></ion-icon>
                <h3 style="margin:0; font-size:1.2rem; color:#fff;">Live Enterprise Telemetry &amp; Access Logs</h3>
            </div>
            <button onclick="closeAuditLogsModal()" style="background:transparent; border:none; color:#94a3b8; font-size:1.6rem; cursor:pointer;"><ion-icon name="close"></ion-icon></button>
        </div>
        <div style="padding:22px 26px; overflow-y:auto; flex:1;">
            <?php if (empty($recentLogs)): ?>
                <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
                    <ion-icon name="analytics-outline" style="font-size:3rem; color:#475569;"></ion-icon>
                    <p style="margin-top:12px; font-size:0.95rem;">No telemetry events recorded yet. Access events will appear here in real-time.</p>
                </div>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.85rem;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1); color:#94a3b8; text-transform:uppercase; font-size:0.72rem; letter-spacing:0.6px;">
                            <th style="padding:10px 8px;">Action</th>
                            <th style="padding:10px 8px;">Resource</th>
                            <th style="padding:10px 8px;">IP Address</th>
                            <th style="padding:10px 8px;">Device / Browser</th>
                            <th style="padding:10px 8px;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): 
                            $badgeColors = [
                                'view' => 'background:rgba(56,189,248,0.15); color:#38bdf8;',
                                'download' => 'background:rgba(16,185,129,0.15); color:#10b981;',
                                'share' => 'background:rgba(255,166,0,0.15); color:#FFA600;',
                                'upload' => 'background:rgba(168,85,247,0.15); color:#a855f7;',
                                'permission_change' => 'background:rgba(244,63,94,0.15); color:#f43f5e;'
                            ];
                            $style = $badgeColors[$log['action']] ?? 'background:rgba(255,255,255,0.1); color:#fff;';
                        ?>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05); color:#e2e8f0;">
                            <td style="padding:12px 8px;">
                                <span style="padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; text-transform:uppercase; <?= $style ?>">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td style="padding:12px 8px; font-weight:600;">
                                <?= htmlspecialchars(ucfirst($log['resource_type']) . ' #' . $log['resource_id']) ?>
                            </td>
                            <td style="padding:12px 8px; font-family:monospace; color:#94a3b8;">
                                <?= htmlspecialchars($log['ip_address'] ?: '127.0.0.1') ?>
                            </td>
                            <td style="padding:12px 8px; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#cbd5e1;" title="<?= htmlspecialchars($log['device_info'] ?? '') ?>">
                                <?= htmlspecialchars($log['device_info'] ?: 'Enterprise Client') ?>
                            </td>
                            <td style="padding:12px 8px; color:#94a3b8;">
                                <?= htmlspecialchars($log['created_at']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Admin Sharing Policies & Defaults Modal -->
<div id="sharingPoliciesModal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(5,11,24,0.85); backdrop-filter:blur(10px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#0b1329; border:1px solid rgba(255,255,255,0.12); border-radius:20px; max-width:560px; width:100%; box-shadow:0 25px 60px rgba(0,0,0,0.6); overflow:hidden;">
        <div style="padding:22px 26px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <ion-icon name="options-outline" style="font-size:1.6rem; color:var(--cloud-gold);"></ion-icon>
                <h3 style="margin:0; font-size:1.2rem; color:#fff;">Default Sharing &amp; Admin Policies</h3>
            </div>
            <button onclick="closeSharingPoliciesModal()" style="background:transparent; border:none; color:#94a3b8; font-size:1.6rem; cursor:pointer;"><ion-icon name="close"></ion-icon></button>
        </div>
        <form action="/cloud/settings/save" method="POST" style="padding:24px 26px;">
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:#FFA600; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:8px;">Default Sharing Mode</label>
                <select name="default_sharing_mode" style="width:100%; background:#050b18; color:#fff; border:1px solid rgba(255,255,255,0.15); border-radius:10px; padding:12px; font-size:0.9rem;">
                    <option value="preview_login" <?= ($sharingPolicy['default_sharing_mode'] ?? '') === 'preview_login' ? 'selected' : '' ?>>Preview then Login to Download (Recommended)</option>
                    <option value="public" <?= ($sharingPolicy['default_sharing_mode'] ?? '') === 'public' ? 'selected' : '' ?>>Public (Instant Preview &amp; Download without Login)</option>
                    <option value="login_required" <?= ($sharingPolicy['default_sharing_mode'] ?? '') === 'login_required' ? 'selected' : '' ?>>Login Required (Must login to view or download)</option>
                    <option value="private" <?= ($sharingPolicy['default_sharing_mode'] ?? '') === 'private' ? 'selected' : '' ?>>Private (Restricted to Tenant Owner)</option>
                </select>
            </div>

            <div style="margin-bottom:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:14px;">
                <label style="display:flex; align-items:center; gap:10px; color:#fff; font-weight:600; font-size:0.9rem; cursor:pointer;">
                    <input type="checkbox" name="always_require_login" value="1" <?= !empty($sharingPolicy['always_require_login']) ? 'checked' : '' ?> style="width:18px; height:18px; accent-color:#FFA600;">
                    Always Require Login (Global Enforcement)
                </label>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px; margin-left:28px;">Overrides individual share links to force sign-in across all tenant files.</div>
            </div>

            <div style="margin-bottom:20px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:14px;">
                <label style="display:flex; align-items:center; gap:10px; color:#fff; font-weight:600; font-size:0.9rem; cursor:pointer;">
                    <input type="checkbox" name="disable_public_links" value="1" <?= !empty($sharingPolicy['disable_public_links']) ? 'checked' : '' ?> style="width:18px; height:18px; accent-color:#f43f5e;">
                    Disable External Public Links
                </label>
                <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px; margin-left:28px;">Prevents anonymous external tokens from being created.</div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" onclick="closeSharingPoliciesModal()" style="background:rgba(255,255,255,0.08); color:#fff; border:none; border-radius:10px; padding:10px 18px; font-weight:600; cursor:pointer;">Cancel</button>
                <button type="submit" style="background:#FFA600; color:#050b18; border:none; border-radius:10px; padding:10px 22px; font-weight:700; cursor:pointer;">Save Policies</button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Right-Click Context Menu -->
<div id="cloudContextMenu" style="display:none; position:fixed; z-index:999999; background:rgba(11, 19, 41, 0.95); backdrop-filter:blur(15px); border:1px solid rgba(255,255,255,0.15); border-radius:14px; padding:8px 0; min-width:210px; box-shadow:0 15px 35px rgba(0,0,0,0.65);">
    <div onclick="createFolder(); hideCloudContextMenu();" style="padding:10px 18px; display:flex; align-items:center; gap:12px; color:#fff; font-size:0.88rem; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='rgba(255,166,0,0.15)'; this.style.color='#FFA600';" onmouseout="this.style.background='transparent'; this.style.color='#fff';">
        <ion-icon name="folder-outline" style="font-size:1.15rem; color:#FFA600;"></ion-icon>
        <span>New Folder</span>
    </div>
    <div onclick="document.getElementById('uploadInput').click(); hideCloudContextMenu();" style="padding:10px 18px; display:flex; align-items:center; gap:12px; color:#fff; font-size:0.88rem; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='rgba(56,189,248,0.15)'; this.style.color='#38bdf8';" onmouseout="this.style.background='transparent'; this.style.color='#fff';">
        <ion-icon name="cloud-upload-outline" style="font-size:1.15rem; color:#38bdf8;"></ion-icon>
        <span>Upload File(s)</span>
    </div>
    <div onclick="document.getElementById('uploadFolderInput').click(); hideCloudContextMenu();" style="padding:10px 18px; display:flex; align-items:center; gap:12px; color:#fff; font-size:0.88rem; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='rgba(168,85,247,0.15)'; this.style.color='#a855f7';" onmouseout="this.style.background='transparent'; this.style.color='#fff';">
        <ion-icon name="folder-open-outline" style="font-size:1.15rem; color:#a855f7;"></ion-icon>
        <span>Upload Folder</span>
    </div>
    <div style="height:1px; background:rgba(255,255,255,0.08); margin:6px 0;"></div>
    <div onclick="openAuditLogsModal(); hideCloudContextMenu();" style="padding:10px 18px; display:flex; align-items:center; gap:12px; color:#fff; font-size:0.88rem; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.06)';" onmouseout="this.style.background='transparent';">
        <ion-icon name="list-circle-outline" style="font-size:1.15rem; color:#10b981;"></ion-icon>
        <span>Access Audit Logs</span>
    </div>
    <div onclick="openSharingPoliciesModal(); hideCloudContextMenu();" style="padding:10px 18px; display:flex; align-items:center; gap:12px; color:#fff; font-size:0.88rem; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.06)';" onmouseout="this.style.background='transparent';">
        <ion-icon name="options-outline" style="font-size:1.15rem; color:var(--cloud-gold);"></ion-icon>
        <span>Sharing Policies</span>
    </div>
</div>

<script>
function openAuditLogsModal() {
    const m = document.getElementById('auditLogsModal');
    if (m) m.style.display = 'flex';
}
function closeAuditLogsModal() {
    const m = document.getElementById('auditLogsModal');
    if (m) m.style.display = 'none';
}
function openSharingPoliciesModal() {
    const m = document.getElementById('sharingPoliciesModal');
    if (m) m.style.display = 'flex';
}
function closeSharingPoliciesModal() {
    const m = document.getElementById('sharingPoliciesModal');
    if (m) m.style.display = 'none';
}

function hideCloudContextMenu() {
    const cm = document.getElementById('cloudContextMenu');
    if (cm) cm.style.display = 'none';
}

document.addEventListener('contextmenu', function(e) {
    // Only show context menu when right clicking inside the main cloud interface
    if (e.target.closest('.main-content') || e.target.closest('.content-area') || e.target.closest('.file-grid')) {
        e.preventDefault();
        const cm = document.getElementById('cloudContextMenu');
        if (!cm) return;
        let x = e.clientX;
        let y = e.clientY;
        if (x + 220 > window.innerWidth) x = window.innerWidth - 230;
        if (y + 220 > window.innerHeight) y = window.innerHeight - 230;
        cm.style.left = x + 'px';
        cm.style.top = y + 'px';
        cm.style.display = 'block';
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#cloudContextMenu')) {
        hideCloudContextMenu();
    }
});
</script>
</body>
</html>
