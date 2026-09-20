<?php
function timeAgo($datetime) {
    if (!$datetime) return '';
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . 'y ago';
    if ($diff->m > 0) return $diff->m . 'mo ago';
    if ($diff->d > 0) return $diff->d . 'd ago';
    if ($diff->h > 0) return $diff->h . 'h ago';
    if ($diff->i > 0) return $diff->i . 'm ago';
    return 'now';
}

function getChannelIcon($channel) {
    switch ($channel) {
        case 'support': return 'chatbubbles-outline';
        case 'forms': return 'document-text-outline';
        case 'chat': return 'chatbox-outline';
        case 'email': return 'mail-outline';
        case 'whatsapp': return 'logo-whatsapp';
        case 'webhook': return 'globe-outline';
        default: return 'chatbubble-outline';
    }
}

function getScoreBadgeClass($score) {
    if ($score >= 90) return 'badge-on-fire';
    if ($score >= 70) return 'badge-very-hot';
    if ($score >= 50) return 'badge-hot';
    if ($score >= 30) return 'badge-warm';
    return 'badge-cold';
}

function getScoreLabel($score) {
    if ($score >= 90) return 'On Fire';
    if ($score >= 70) return 'Very Hot';
    if ($score >= 50) return 'Hot';
    if ($score >= 30) return 'Warm';
    return 'Cold';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - CasjoeSaaS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/casjoe_theme.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .inbox-tabs { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; }
        .inbox-tab { padding: 8px 16px; border-radius: 20px; background: #f1f5f9; color: #1e293b; text-decoration: none; white-space: nowrap; }
        .inbox-tab.active { background: #000066; color: white; }
        .inbox-filters { display: flex; gap: 15px; margin-bottom: 20px; }
        .search-input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; width: 300px; }
        
        .conversation-list { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .conversation-item { display: flex; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit; align-items: center; transition: background 0.2s; }
        .conversation-item:hover { background: #f8fafc; }
        .conversation-item.unread { border-left: 4px solid #000066; background: #f0f4f8; }
        .conversation-item:last-child { border-bottom: none; }
        
        .conv-icon { font-size: 24px; color: #64748b; margin-right: 15px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border-radius: 50%; }
        .conv-content { flex-grow: 1; min-width: 0; }
        .conv-header { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .conv-name { font-weight: bold; color: #1e293b; }
        .conv-time { color: #94a3b8; font-size: 0.85em; }
        .conv-preview { color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.95em; }
        
        .conv-badges { display: flex; gap: 8px; align-items: center; margin-top: 6px; }
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 0.75em; font-weight: 500; }
        .badge-channel { background: #e2e8f0; color: #475569; text-transform: uppercase; }
        .badge-unread { background: #3b82f6; color: white; }
        
        .badge-cold { background: #e0f2fe; color: #0369a1; }
        .badge-warm { background: #fef08a; color: #854d0e; }
        .badge-hot { background: #fed7aa; color: #c2410c; }
        .badge-very-hot { background: #fecaca; color: #b91c1c; }
        .badge-on-fire { background: #ef4444; color: white; }

        html.dark-theme .conversation-list { background: #1e293b; }
        html.dark-theme .conversation-item { color: #e2e8f0; border-bottom-color: #334155; }
        html.dark-theme .conversation-item:hover { background: #0f172a; }
        html.dark-theme .conv-name { color: #f8fafc; }
        html.dark-theme .conv-preview { color: #94a3b8; }
        html.dark-theme .inbox-tab { background: #334155; color: #e2e8f0; }
        html.dark-theme .inbox-tab.active { background: #3b82f6; }
    </style>
</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/../../../Modules/CasjoeERP/Views/layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="inbox-header">
                <h1>Inbox <span class="badge badge-unread"><?php echo count(array_filter($conversations ?? [], fn($c) => ($c['unread_count'] ?? 0) > 0)); ?></span></h1>
            </div>

            <div class="inbox-tabs">
                <?php
                $channels = ['all' => 'All', 'support' => 'Support', 'forms' => 'Forms', 'chat' => 'Chat', 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'webhook' => 'Webhook'];
                $currentChannel = $currentChannel ?? 'all';
                foreach ($channels as $key => $label) {
                    $activeClass = $currentChannel === $key ? 'active' : '';
                    echo "<a href=\"?channel={$key}\" class=\"inbox-tab {$activeClass}\">{$label}</a>";
                }
                ?>
            </div>

            <div class="inbox-filters">
                <form method="GET" style="display: flex; gap: 10px; align-items: center; width: 100%;">
                    <input type="hidden" name="channel" value="<?php echo htmlspecialchars($currentChannel); ?>">
                    <input type="text" name="q" class="search-input" placeholder="Search conversations..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                    
                    <select name="status" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="open" <?php echo ($currentStatus ?? 'open') === 'open' ? 'selected' : ''; ?>>Open</option>
                        <option value="closed" <?php echo ($currentStatus ?? 'open') === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </form>
            </div>

            <div class="conversation-list">
                <?php if (empty($conversations)): ?>
                    <div style="padding: 40px; text-align: center; color: #64748b;">
                        <ion-icon name="mail-unread-outline" style="font-size: 48px; margin-bottom: 10px;"></ion-icon>
                        <p>No conversations found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="/inbox/conversation?id=<?php echo htmlspecialchars($conv['id']); ?>" class="conversation-item <?php echo ($conv['unread_count'] ?? 0) > 0 ? 'unread' : ''; ?>">
                            <div class="conv-icon">
                                <ion-icon name="<?php echo getChannelIcon($conv['channel']); ?>"></ion-icon>
                            </div>
                            <div class="conv-content">
                                <div class="conv-header">
                                    <span class="conv-name"><?php echo htmlspecialchars($conv['contact_name'] ?: 'Unknown Contact'); ?></span>
                                    <span class="conv-time"><?php echo timeAgo($conv['last_message_at']); ?></span>
                                </div>
                                <div class="conv-preview">
                                    <?php echo htmlspecialchars($conv['subject'] ?? $conv['last_message_preview'] ?? 'No message preview'); ?>
                                </div>
                                <div class="conv-badges">
                                    <span class="badge badge-channel"><?php echo htmlspecialchars($conv['channel']); ?></span>
                                    
                                    <?php if (isset($conv['ai_score'])): ?>
                                        <span class="badge <?php echo getScoreBadgeClass($conv['ai_score']); ?>">
                                            <?php echo getScoreLabel($conv['ai_score']); ?> (<?php echo $conv['ai_score']; ?>)
                                        </span>
                                    <?php endif; ?>

                                    <?php if (($conv['unread_count'] ?? 0) > 0): ?>
                                        <span class="badge badge-unread"><?php echo $conv['unread_count']; ?> new</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
