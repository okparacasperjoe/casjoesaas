<?php
$title = "Notifications";
require __DIR__ . '/global_header.php';
?>

<style>
    .notification-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-radius: 12px;
        background: #fff;
        margin-bottom: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid transparent;
        display: flex;
        flex-direction: column;
        padding: 16px 20px;
    }
    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .notification-card.unread {
        border-left-color: #FFA600;
        background: #fffdf5;
    }
    .notification-card.read {
        border-left-color: #e2e8f0;
    }
    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .notification-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.05rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .notification-time {
        font-size: 0.75rem;
        color: #94a3b8;
    }
    .notification-body {
        color: #4b5563;
        font-size: 0.9rem;
        margin-bottom: 10px;
        line-height: 1.5;
    }
    .notification-actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }
    .btn-action {
        padding: 6px 14px;
        font-size: 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: 0.2s;
        cursor: pointer;
        border: none;
    }
    .btn-view {
        background: rgba(0,0,102,0.08);
        color: #000066;
    }
    .btn-view:hover {
        background: rgba(0,0,102,0.15);
        color: #000066;
    }
    .btn-mark-read {
        background: transparent;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }
    .btn-mark-read:hover {
        background: #f8fafc;
        color: #1e293b;
    }
    
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #94a3b8;
    }
    .empty-state ion-icon {
        font-size: 4rem;
        color: #e2e8f0;
        margin-bottom: 15px;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            
            <div class="page-header">
                <h4 class="fw-bold m-0" style="color: #000066;">Notifications</h4>
                <?php
                $hasUnread = false;
                foreach($notifications as $n) {
                    if($n['is_read'] == 0) { $hasUnread = true; break; }
                }
                ?>
                <?php if ($hasUnread): ?>
                    <button class="btn btn-outline-secondary btn-sm" onclick="markAllRead()" style="border-radius: 20px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                        <ion-icon name="checkmark-done-outline"></ion-icon> Mark All as Read
                    </button>
                <?php endif; ?>
            </div>

            <div id="notification-list">
                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <ion-icon name="notifications-off-outline"></ion-icon>
                        <h5>No notifications yet</h5>
                        <p>You're all caught up! Check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-card <?= $notification['is_read'] ? 'read' : 'unread' ?>" id="notif-<?= $notification['id'] ?>">
                            <div class="notification-header">
                                <h5 class="notification-title">
                                    <?php if (!$notification['is_read']): ?>
                                        <span style="width: 8px; height: 8px; background: #FFA600; border-radius: 50%; display: inline-block;"></span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($notification['title']) ?>
                                </h5>
                                <span class="notification-time"><?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?></span>
                            </div>
                            <div class="notification-body">
                                <?= htmlspecialchars($notification['message']) ?>
                            </div>
                            <div class="notification-actions">
                                <?php if ($notification['link'] && $notification['link'] !== '#'): ?>
                                    <a href="<?= htmlspecialchars($notification['link']) ?>" class="btn-action btn-view" onclick="markAsRead(<?= $notification['id'] ?>, true)">
                                        <ion-icon name="eye-outline"></ion-icon> View Details
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!$notification['is_read']): ?>
                                    <button class="btn-action btn-mark-read" onclick="markAsRead(<?= $notification['id'] ?>)">
                                        <ion-icon name="checkmark-outline"></ion-icon> Mark as Read
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
function markAsRead(id, followLink = false) {
    fetch('/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
    }).then(response => response.json())
    .then(data => {
        if(data.success && !followLink) {
            const card = document.getElementById('notif-' + id);
            card.classList.remove('unread');
            card.classList.add('read');
            // Remove the orange dot
            const dot = card.querySelector('.notification-title span');
            if(dot) dot.remove();
            // Remove the mark as read button
            const btn = card.querySelector('.btn-mark-read');
            if(btn) btn.remove();
        }
    });
}

function markAllRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST'
    }).then(response => response.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        }
    });
}
</script>

<?php require __DIR__ . '/global_footer.php'; ?>
