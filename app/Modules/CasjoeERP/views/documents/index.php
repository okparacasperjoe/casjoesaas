<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>E-Sign & Documents | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .doc-metric-card {
            background: var(--surface, #ffffff);
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        html.dark-theme .doc-metric-card {
            background: #111827;
            border-color: rgba(255,255,255,0.08);
        }
        .filter-pill {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #64748b;
            background: #f1f5f9;
            transition: all 0.2s;
        }
        html.dark-theme .filter-pill {
            background: #1e293b;
            color: #94a3b8;
        }
        .filter-pill.active {
            background: #2563eb;
            color: #ffffff !important;
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>E-Sign & Document Signing</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Draft, send, and collect legally-binding digital signatures from clients, vendors, and staff</p>
            </div>
            <a href="/erp/documents/create" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="add-circle-outline"></ion-icon> New Document / Contract
            </a>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="alert-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($errorMsg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMsg)): ?>
            <div style="background: #ecfdf5; border: 1px solid #34d399; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <ion-icon name="checkmark-circle-outline" style="font-size: 22px; flex-shrink: 0;"></ion-icon>
                <div><?= htmlspecialchars($successMsg) ?></div>
            </div>
        <?php endif; ?>

        <!-- Quick Summary Metrics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div class="doc-metric-card" style="border-left: 4px solid #2563eb;">
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Documents</div>
                    <div style="font-size: 24px; font-weight: 700; margin-top: 4px;"><?= (int)($metrics['total'] ?? 0) ?></div>
                </div>
                <ion-icon name="documents-outline" style="font-size: 32px; color: #2563eb; opacity: 0.8;"></ion-icon>
            </div>
            <div class="doc-metric-card" style="border-left: 4px solid #d97706;">
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Awaiting Signature</div>
                    <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: #d97706;"><?= (int)($metrics['pending'] ?? 0) ?></div>
                </div>
                <ion-icon name="time-outline" style="font-size: 32px; color: #d97706; opacity: 0.8;"></ion-icon>
            </div>
            <div class="doc-metric-card" style="border-left: 4px solid #16a34a;">
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Signed & Completed</div>
                    <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: #16a34a;"><?= (int)($metrics['signed'] ?? 0) ?></div>
                </div>
                <ion-icon name="checkmark-done-circle-outline" style="font-size: 32px; color: #16a34a; opacity: 0.8;"></ion-icon>
            </div>
            <div class="doc-metric-card" style="border-left: 4px solid #64748b;">
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Drafts</div>
                    <div style="font-size: 24px; font-weight: 700; margin-top: 4px; color: #64748b;"><?= (int)($metrics['draft'] ?? 0) ?></div>
                </div>
                <ion-icon name="create-outline" style="font-size: 32px; color: #64748b; opacity: 0.8;"></ion-icon>
            </div>
        </div>

        <!-- Filter Bar & Search -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 18px; flex-wrap: wrap;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <?php $currFilter = $_GET['filter'] ?? 'all'; ?>
                <a href="/erp/documents?filter=all" class="filter-pill <?= $currFilter === 'all' ? 'active' : '' ?>">All (<?= (int)$metrics['total'] ?>)</a>
                <a href="/erp/documents?filter=sent" class="filter-pill <?= $currFilter === 'sent' ? 'active' : '' ?>">Awaiting Signature (<?= (int)$metrics['pending'] ?>)</a>
                <a href="/erp/documents?filter=signed" class="filter-pill <?= $currFilter === 'signed' ? 'active' : '' ?>">Signed (<?= (int)$metrics['signed'] ?>)</a>
                <a href="/erp/documents?filter=draft" class="filter-pill <?= $currFilter === 'draft' ? 'active' : '' ?>">Drafts (<?= (int)$metrics['draft'] ?>)</a>
            </div>
            <form method="GET" action="/erp/documents" style="display: flex; gap: 8px;">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($currFilter) ?>">
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search document or recipient..." class="form-control" style="width: 240px; padding: 6px 12px; font-size: 13px;">
                <button type="submit" class="btn btn-outline" style="padding: 6px 12px;">Search</button>
            </form>
        </div>

        <!-- Documents Table -->
        <div class="card">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 12px 10px;">Document Title</th>
                        <th style="padding: 12px 10px;">Recipient</th>
                        <th style="padding: 12px 10px;">Type / Category</th>
                        <th style="padding: 12px 10px;">Created Date</th>
                        <th style="padding: 12px 10px;">Status</th>
                        <th style="padding: 12px 10px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): 
                            $st = strtolower($doc['status'] ?? 'draft');
                            $badgeBg = '#f1f5f9'; $badgeColor = '#475569';
                            if ($st === 'signed') { $badgeBg = '#dcfce7'; $badgeColor = '#15803d'; }
                            elseif ($st === 'sent' || $st === 'viewed') { $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; }
                            elseif ($st === 'declined') { $badgeBg = '#fee2e2'; $badgeColor = '#991b1b'; }
                        ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px 10px;">
                                    <a href="/erp/documents/show?uuid=<?= $doc['uuid'] ?>" style="font-weight: 600; text-decoration: none; color: inherit;">
                                        <?= htmlspecialchars($doc['title']) ?>
                                    </a>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <div style="font-weight: 500;"><?= htmlspecialchars($doc['recipient_name']) ?></div>
                                    <div style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($doc['recipient_email']) ?></div>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <span style="background: rgba(2, 132, 199, 0.08); color: #0284c7; padding: 2px 8px; border-radius: 4px; font-size: 12px; text-transform: capitalize; font-weight: 600;">
                                        <?= htmlspecialchars(str_replace('_', ' ', $doc['category'] ?? 'contract')) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px 10px; color: #64748b; font-size: 13px; white-space: nowrap;">
                                    <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: capitalize;">
                                        <?= ($st === 'sent') ? 'Awaiting Signature' : (($st === 'viewed') ? 'Viewed by Recipient' : $st) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px 10px; text-align: right; white-space: nowrap;">
                                    <a href="/erp/documents/show?uuid=<?= $doc['uuid'] ?>" class="btn btn-sm btn-outline" style="display: inline-flex; align-items: center; gap: 4px; font-size: 12px; padding: 5px 9px;">
                                        <ion-icon name="eye-outline"></ion-icon> View
                                    </a>
                                    <?php if ($st === 'draft'): ?>
                                        <a href="/erp/documents/edit?id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline" style="display: inline-flex; align-items: center; gap: 4px; font-size: 12px; padding: 5px 9px;">
                                            <ion-icon name="create-outline"></ion-icon> Edit
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="/erp/documents/delete" onsubmit="return confirm('Are you sure you want to delete this document?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $doc['id'] ?>">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); padding: 5px 9px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <ion-icon name="document-text-outline" style="font-size: 40px; color: #cbd5e1; margin-bottom: 8px;"></ion-icon>
                                <div>No documents found matching this filter.</div>
                                <a href="/erp/documents/create" style="color: #0284c7; text-decoration: underline; margin-top: 8px; display: inline-block;">Create your first document for signing</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
