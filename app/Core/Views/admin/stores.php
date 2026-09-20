<?php
$pageTitle = 'Pending Store Approvals';
require __DIR__ . '/header.php';
?>
<style>
        .stores-container { 
            max-width: 100%; 
            background: var(--glass-bg); 
            padding: 30px; 
            border-radius: 15px; 
            border: 1px solid var(--glass-border); 
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; color: #eee; }
        .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .table th { color: var(--secondary); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-muted);
        }
        .empty-state ion-icon {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }
        .badge {
            background: var(--primary);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .btn-approve {
            background: linear-gradient(135deg, #00b894, #00cec9);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .btn-approve:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 184, 148, 0.4); }
        .btn-reject {
            background: rgba(255, 118, 117, 0.2);
            border: 1px solid rgba(255, 118, 117, 0.5);
            color: #ff7675;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .btn-reject:hover { background: #ff7675; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 118, 117, 0.4); }
        .slug-tag {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            color: #a29bfe;
        }
        
        @media (max-width: 992px) {
            .table-responsive { overflow-x: auto; }
        }
    </style>
        <div class="top-bar">
            <h2><ion-icon name="storefront-outline" style="vertical-align: middle;"></ion-icon> Pending Stores</h2>
            <div class="user-profile">
                <a href="/<?= ADMIN_PATH ?>" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                    <ion-icon name="arrow-back"></ion-icon> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;"><ion-icon name="checkmark-circle"></ion-icon> <?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="stores-container">
            <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                Applications Awaiting Review
                <span class="badge"><?= count($stores) ?></span>
            </h3>
            
            <?php if (empty($stores)): ?>
                <div class="empty-state">
                    <ion-icon name="storefront-outline"></ion-icon>
                    <h3>No pending applications</h3>
                    <p>All store applications have been processed or none have been submitted yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Store Name & Slug</th>
                                <th>Owner Details</th>
                                <th>Applied On</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stores as $store): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: bold; font-size: 1.1rem; color: #fff; margin-bottom: 5px;"><?= htmlspecialchars($store['store_name']) ?></div>
                                        <span class="slug-tag"><?= htmlspecialchars($store['slug']) ?></span>
                                    </td>
                                    <td>
                                        <div style="color: #fff;"><?= htmlspecialchars($store['owner_name']) ?></div>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($store['owner_email']) ?></div>
                                    </td>
                                    <td style="color: var(--text-muted);">
                                        <?= date('M j, Y', strtotime($store['created_at'] ?? 'now')) ?><br>
                                        <small><?= date('g:i A', strtotime($store['created_at'] ?? 'now')) ?></small>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                            <form method="POST" action="/<?= ADMIN_PATH ?>/stores/approve">
                                                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                                <input type="hidden" name="id" value="<?= $store['id'] ?>">
                                                <button type="submit" class="btn-approve" onclick="return confirm('Approve `<?= htmlspecialchars($store['store_name']) ?>`?')">Approve</button>
                                            </form>
                                            <form method="POST" action="/<?= ADMIN_PATH ?>/stores/reject">
                                                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                                <input type="hidden" name="id" value="<?= $store['id'] ?>">
                                                <button type="submit" class="btn-reject" onclick="return confirm('Reject `<?= htmlspecialchars($store['store_name']) ?>`?')">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
