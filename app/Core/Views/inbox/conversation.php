<?php
function formatMessageTime($datetime) {
    if (!$datetime) return '';
    $dt = new DateTime($datetime);
    return $dt->format('M j, Y g:i A');
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

$convChannel = $conversation['channel'] ?? 'unknown';
$contactEmail = $conversation['contact_email'] ?? '';
$contactPhone = $conversation['contact_phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversation - CasjoeSaaS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/casjoe_theme.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .conv-container { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; height: calc(100vh - 40px); margin: 20px 0; }
        
        .conv-header { padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .back-btn { display: flex; align-items: center; gap: 5px; text-decoration: none; color: #64748b; font-weight: 500; }
        .back-btn:hover { color: #000066; }
        .contact-info { display: flex; flex-direction: column; }
        .contact-name { font-weight: bold; color: #1e293b; font-size: 1.1em; display: flex; align-items: center; gap: 8px; }
        .contact-details { font-size: 0.85em; color: #64748b; display: flex; gap: 10px; margin-top: 4px; }
        .contact-details a { color: #3b82f6; text-decoration: none; }
        .contact-details a:hover { text-decoration: underline; }
        
        .header-right { display: flex; gap: 10px; align-items: center; }
        .btn { padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; font-weight: 500; text-decoration: none; font-size: 0.9em; }
        .btn-outline { background: transparent; border: 1px solid #cbd5e1; color: #475569; }
        .btn-outline:hover { background: #f1f5f9; }
        .btn-primary { background: #000066; color: white; }
        .btn-primary:hover { background: #000044; }

        .badge { padding: 2px 8px; border-radius: 12px; font-size: 0.75em; font-weight: 500; }
        .badge-channel { background: #e2e8f0; color: #475569; text-transform: uppercase; }
        .badge-status-open { background: #dcfce7; color: #166534; }
        .badge-status-closed { background: #f1f5f9; color: #475569; }

        .badge-cold { background: #e0f2fe; color: #0369a1; }
        .badge-warm { background: #fef08a; color: #854d0e; }
        .badge-hot { background: #fed7aa; color: #c2410c; }
        .badge-very-hot { background: #fecaca; color: #b91c1c; }
        .badge-on-fire { background: #ef4444; color: white; }

        .message-thread { flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; background: #f8fafc; }
        
        .msg-wrapper { display: flex; flex-direction: column; max-width: 75%; }
        .msg-wrapper.inbound { align-self: flex-start; }
        .msg-wrapper.outbound { align-self: flex-end; align-items: flex-end; }
        .msg-wrapper.system { align-self: center; align-items: center; max-width: 90%; }
        
        .msg-meta { font-size: 0.75em; color: #94a3b8; margin-bottom: 4px; display: flex; gap: 8px; }
        
        .msg-bubble { padding: 10px 14px; border-radius: 8px; font-size: 0.95em; line-height: 1.4; word-break: break-word; }
        .inbound .msg-bubble { background: #f1f5f9; color: #1e293b; border-bottom-left-radius: 2px; border: 1px solid #e2e8f0; }
        .outbound .msg-bubble { background: #000066; color: white; border-bottom-right-radius: 2px; }
        .system .msg-bubble { background: transparent; color: #64748b; font-style: italic; border: none; text-align: center; }

        .form-data-table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 0.9em; background: white; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0; }
        .form-data-table th, .form-data-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; color: #1e293b; }
        .form-data-table th { background: #f8fafc; font-weight: 600; width: 30%; }
        .outbound .form-data-table th, .outbound .form-data-table td { color: #1e293b; }
        
        .reply-box { padding: 15px 20px; border-top: 1px solid #e2e8f0; background: white; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; }
        .reply-textarea { width: 100%; min-height: 80px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; resize: vertical; margin-bottom: 10px; font-family: inherit; }
        .reply-footer { display: flex; justify-content: space-between; align-items: center; }
        .reply-note { font-size: 0.85em; color: #64748b; }

        html.dark-theme .conv-container, html.dark-theme .reply-box { background: #1e293b; border-color: #334155; }
        html.dark-theme .conv-header { border-bottom-color: #334155; }
        html.dark-theme .contact-name { color: #f8fafc; }
        html.dark-theme .message-thread { background: #0f172a; }
        html.dark-theme .inbound .msg-bubble { background: #334155; color: #e2e8f0; border-color: #475569; }
        html.dark-theme .reply-textarea { background: #0f172a; border-color: #475569; color: #f8fafc; }
        html.dark-theme .form-data-table { background: #1e293b; border-color: #475569; }
        html.dark-theme .form-data-table th { background: #334155; color: #f8fafc; }
        html.dark-theme .form-data-table td { color: #e2e8f0; border-color: #475569; }
    </style>
</head>
<body>
    <div class="app-container">
        <?php require __DIR__ . '/../../../Modules/CasjoeERP/Views/layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="conv-container">
                
                <div class="conv-header">
                    <div class="header-left">
                        <a href="/inbox" class="back-btn"><ion-icon name="arrow-back-outline"></ion-icon> Back</a>
                        <div class="contact-info">
                            <div class="contact-name">
                                <?php echo htmlspecialchars($conversation['contact_name'] ?? 'Unknown Contact'); ?>
                                <span class="badge badge-channel"><?php echo htmlspecialchars($convChannel); ?></span>
                                <?php if (isset($lead) && isset($lead['ai_score'])): ?>
                                    <span class="badge <?php echo getScoreBadgeClass($lead['ai_score']); ?>">
                                        <?php echo getScoreLabel($lead['ai_score']); ?> (<?php echo $lead['ai_score']; ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="contact-details">
                                <?php if ($contactEmail): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><ion-icon name="mail-outline"></ion-icon> <?php echo htmlspecialchars($contactEmail); ?></a>
                                <?php endif; ?>
                                <?php if ($contactPhone): ?>
                                    <a href="tel:<?php echo htmlspecialchars($contactPhone); ?>"><ion-icon name="call-outline"></ion-icon> <?php echo htmlspecialchars($contactPhone); ?></a>
                                    <?php if ($convChannel === 'whatsapp'): ?>
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $contactPhone); ?>" target="_blank"><ion-icon name="logo-whatsapp"></ion-icon> WhatsApp</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="header-right">
                        <span class="badge <?php echo ($conversation['status'] ?? 'open') === 'open' ? 'badge-status-open' : 'badge-status-closed'; ?>">
                            <?php echo ucfirst($conversation['status'] ?? 'open'); ?>
                        </span>
                        <?php if (($conversation['status'] ?? 'open') !== 'closed'): ?>
                            <form method="POST" action="/inbox/close" style="margin: 0;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($conversation['id']); ?>">
                                <button type="submit" class="btn btn-outline">Close</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="message-thread" id="message-thread">
                    <?php if (empty($messages)): ?>
                        <div style="text-align: center; color: #64748b; margin-top: 20px;">No messages in this conversation.</div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): 
                            $direction = $msg['direction'] ?? 'inbound';
                            $alignClass = $direction;
                        ?>
                            <div class="msg-wrapper <?php echo $alignClass; ?>">
                                <?php if ($direction !== 'system'): ?>
                                    <div class="msg-meta">
                                        <span class="sender"><?php echo htmlspecialchars($msg['sender_name'] ?? ($direction === 'inbound' ? ($conversation['contact_name'] ?? 'Contact') : 'You')); ?></span>
                                        <span class="time"><?php echo formatMessageTime($msg['created_at']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="msg-bubble">
                                    <?php if (($msg['content_type'] ?? 'text') === 'form_data'): ?>
                                        <?php 
                                            $formData = json_decode($msg['content'], true); 
                                            if (is_array($formData)):
                                        ?>
                                            <table class="form-data-table">
                                                <tbody>
                                                    <?php foreach ($formData as $k => $v): ?>
                                                        <tr>
                                                            <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $k))); ?></th>
                                                            <td><?php echo htmlspecialchars(is_array($v) ? json_encode($v) : $v); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($msg['content']); ?>
                                        <?php endif; ?>
                                    <?php elseif (($msg['content_type'] ?? 'text') === 'html'): ?>
                                        <?php echo $msg['content']; ?>
                                    <?php else: ?>
                                        <?php echo nl2br(htmlspecialchars($msg['content'])); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($direction === 'system'): ?>
                                    <div class="msg-meta" style="justify-content: center; margin-top: 4px;">
                                        <span class="time"><?php echo formatMessageTime($msg['created_at']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="reply-box">
                    <form method="POST" action="/inbox/reply">
                        <input type="hidden" name="conversation_id" value="<?php echo htmlspecialchars($conversation['id'] ?? ''); ?>">
                        <textarea name="content" class="reply-textarea" placeholder="Type your reply here..." required></textarea>
                        <div class="reply-footer">
                            <div class="reply-note">
                                <?php
                                    if ($convChannel === 'support') echo "Reply will be sent to support ticket.";
                                    elseif ($convChannel === 'email') echo "Reply will be emailed to " . htmlspecialchars($contactEmail) . ".";
                                    elseif ($convChannel === 'whatsapp') echo "Reply will be sent via WhatsApp to " . htmlspecialchars($contactPhone) . ".";
                                    else echo "Reply via " . htmlspecialchars($convChannel) . ".";
                                ?>
                            </div>
                            <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 5px;">
                                <ion-icon name="send"></ion-icon> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
        var thread = document.getElementById('message-thread');
        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    </script>
</body>
</html>
