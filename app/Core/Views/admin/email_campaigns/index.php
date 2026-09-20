<?php
// Admin Context Email Campaigns List
$active = 'email-campaigns';
$pageTitle = "Email Campaigns";
require __DIR__ . '/../header.php';
?>
<style>
    .campaign-list { background: #13141f; border-radius: 12px; border: 1px solid rgba(255, 166, 0, 0.2); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4); overflow: hidden; }
    .campaign-item { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); transition: all 0.2s; }
    .campaign-item:last-child { border-bottom: none; }
    .campaign-item:hover { background: rgba(255, 166, 0, 0.06); }
    .c-meta h3 { margin: 0 0 6px; font-size: 1.05rem; color: #ffffff; font-weight: 600; }
    .c-meta p { margin: 0; font-size: 0.85rem; color: #94a3b8; }
    .c-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-draft { background: rgba(255, 255, 255, 0.1); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.2); }
    .status-sent { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
    .c-actions a { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 6px; background: rgba(255, 255, 255, 0.08); color: #f1f5f9; text-decoration: none; margin-left: 6px; transition: all 0.2s; border: 1px solid rgba(255, 255, 255, 0.1); }
    .c-actions a:hover { background: rgba(255, 166, 0, 0.2); color: #FFA600; border-color: #FFA600; }
    .c-actions a.delete:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; border-color: #ef4444; }
</style>

<div class="content-wrapper" style="padding: 20px;">
    <?php if (!empty($_GET['message'])): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            ✅ <?= htmlspecialchars($_GET['message']) ?>
        </div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h2 style="margin:0;"><?= $pageTitle ?></h2>
        <a href="/<?= ADMIN_PATH ?>/email-campaigns/create" class="btn btn-primary">+ Create Campaign</a>
    </div>

    <div class="campaign-list">
        <?php if (empty($campaigns)): ?>
            <div style="padding: 40px; text-align: center; color: #64748b;">
                <ion-icon name="paper-plane-outline" style="font-size: 48px; color: #cbd5e1; margin-bottom: 10px;"></ion-icon>
                <p>No admin campaigns found. Create one to broadcast to your users.</p>
            </div>
        <?php else: ?>
            <?php foreach ($campaigns as $c): ?>
                <div class="campaign-item">
                    <div class="c-meta">
                        <h3><?= htmlspecialchars($c['name']) ?></h3>
                        <p>Subject: <?= htmlspecialchars($c['subject']) ?> &bull; Created: <?= date('M j, Y', strtotime($c['created_at'])) ?></p>
                    </div>
                    <div style="display:flex; align-items:center; gap: 20px;">
                        <span class="c-status status-<?= strtolower($c['status']) ?>"><?= htmlspecialchars($c['status']) ?></span>
                        <div class="c-actions">
                            <a href="/<?= ADMIN_PATH ?>/email-campaigns/edit/<?= $c['id'] ?>" title="Edit"><ion-icon name="create-outline"></ion-icon></a>
                            <a href="/<?= ADMIN_PATH ?>/email-campaigns/delete/<?= $c['id'] ?>" class="delete" title="Delete" onclick="return confirm('Delete this campaign?');"><ion-icon name="trash-outline"></ion-icon></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../footer.php'; ?>
