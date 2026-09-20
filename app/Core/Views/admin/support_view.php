<?php
$pageTitle = "Ticket #" . $ticket['id'];
require_once __DIR__ . '/header.php';
?>

<style>
    /* Two-column chat timeline design */
    .support-grid { display: grid; grid-template-columns: 1fr 340px; gap: 32px; align-items: start; }
    @media (max-width: 900px) { .support-grid { grid-template-columns: 1fr; } }

    .meta-box { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; transition: all 0.3s; }
    .meta-box:hover { box-shadow: 0 15px 40px rgba(0,0,0,0.04); border-color: #cbd5e1; }
    .meta-header { padding: 18px 24px; border-bottom: 1px solid #f0f2f5; font-weight: 800; color: #000066; font-size: 0.9rem; text-transform: uppercase; display: flex; align-items: center; gap: 10px; background: #fafbfc; }
    .meta-body { padding: 24px; }

    /* Conversation bubble elements */
    .timeline { display: flex; flex-direction: column; gap: 24px; padding: 30px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    .timeline-item { display: flex; gap: 16px; align-items: flex-start; }
    .timeline-avatar { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; font-size: 1.1rem; }
    .avatar-user { background: #000066; color: #FFA600; box-shadow: 0 4px 10px rgba(0,0,102,0.15); }
    .avatar-admin { background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a; }
    
    .timeline-bubble { flex: 1; background: #f8fafc; border-radius: 0 16px 16px 16px; padding: 20px 24px; border: 1px solid #f1f5f9; position: relative; transition: all 0.2s; }
    .timeline-bubble:hover { background: #f1f5f9; }
    .timeline-bubble.admin { background: #fff; border: 1px solid #000066; border-radius: 16px 0 16px 16px; box-shadow: 0 4px 15px rgba(0,0,102,0.05); }
    
    .bubble-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .bubble-name { font-weight: 800; font-size: 0.95rem; color: #0f172a; }
    .bubble-name.admin { color: #000066; }
    .bubble-time { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .bubble-body { font-size: 0.95rem; color: #334155; line-height: 1.7; margin: 0; white-space: pre-wrap; }

    /* Reply area */
    .reply-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-top: 32px; padding: 24px; transition: all 0.3s; }
    .reply-card:focus-within { box-shadow: 0 15px 40px rgba(0,0,102,0.08); border-color: #000066; }
    .reply-textarea { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 16px; font-size: 1rem; color: #1e293b; background: #fafbfc; font-family: inherit; resize: vertical; min-height: 120px; box-sizing: border-box; transition: all 0.2s; }
    .reply-textarea:focus { outline: none; border-color: #000066; background: #fff; box-shadow: 0 0 0 3px rgba(0,0,102,0.1); }
    .reply-submit { background: #000066; color: #FFA600; font-weight: 800; font-size: 1rem; border: none; padding: 14px 28px; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,102,0.2); }
    .reply-submit:hover { background: #000044; box-shadow: 0 6px 20px rgba(0,0,102,0.3); transform: translateY(-2px); }

    /* Select input */
    .status-select { width: 100%; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; color: #1e293b; background: #fafbfc; cursor: pointer; transition: all 0.2s; font-weight: 600; }
    .status-select:focus { outline: none; border-color: #000066; box-shadow: 0 0 0 3px rgba(0,0,102,0.1); }
</style>

<div class="top-bar" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <h2 style="color: #000066; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; font-size: 1.8rem;">
        <div style="background: #000066; color: #FFA600; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <ion-icon name="ticket-outline"></ion-icon>
        </div>
        Ticket #<?= $ticket['id'] ?>
    </h2>
    <a href="/casper-joe/support" class="ep-back" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f0f2f5; color: #334155; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: all 0.3s; box-shadow: inset 0 0 0 1px #e2e8f0;">
        <ion-icon name="arrow-back-outline"></ion-icon> Back to Tickets
    </a>
</div>

<div class="settings-container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="support-grid">
        <!-- LEFT: Conversation Timeline & Reply -->
        <div>
            <!-- Topic Title Card -->
            <div class="meta-box" style="margin-bottom: 20px; padding: 20px 24px;">
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Subject Topic</span>
                <h3 style="margin: 4px 0 0; color: #000066; font-weight: 700; font-size: 1.25rem;"><?= htmlspecialchars($ticket['subject']) ?></h3>
            </div>

            <!-- Chat Bubbles Timeline -->
            <div class="timeline">
                <?php foreach ($messages as $msg): ?>
                    <div class="timeline-item">
                        <div class="timeline-avatar <?= $msg['is_staff'] ? 'avatar-admin' : 'avatar-user' ?>">
                            <?= $msg['is_staff'] ? 'AD' : strtoupper(substr($msg['author_name'] ?? 'U', 0, 2)) ?>
                        </div>
                        <div class="timeline-bubble <?= $msg['is_staff'] ? 'admin' : '' ?>">
                            <div class="bubble-header">
                                <span class="bubble-name <?= $msg['is_staff'] ? 'admin' : '' ?>">
                                    <?= $msg['is_staff'] ? '🛡️ Customer Support' : htmlspecialchars($msg['author_name']) ?>
                                </span>
                                <span class="bubble-time"><?= date('M j, Y • g:i A', strtotime($msg['created_at'])) ?></span>
                            </div>
                            <p class="bubble-body"><?= htmlspecialchars($msg['message']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Attachments -->
            <?php if (!empty($attachments)): ?>
                <div class="meta-box" style="margin-top: 20px; padding: 20px;">
                    <h4 style="margin: 0 0 14px 0; color: #000066; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="attach-outline"></ion-icon> Shared Attachments
                    </h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <?php foreach ($attachments as $att): ?>
                            <a href="<?= htmlspecialchars($att['file_url']) ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #f1f5f9; color: #475569; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; border: 1px solid #e2e8f0; transition: background 0.2s;">
                                <ion-icon name="document-text-outline" style="font-size: 1.1rem; color: #000066;"></ion-icon>
                                <span><?= htmlspecialchars($att['file_name']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reply Form -->
            <div class="reply-card">
                <h4 style="margin: 0 0 16px 0; color: #000066; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="create-outline"></ion-icon> Write a Response
                </h4>
                <form method="POST" action="/casper-joe/support/reply">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                    <div style="margin-bottom: 16px;">
                        <textarea name="reply" class="reply-textarea" required placeholder="Type your response to the customer..."></textarea>
                    </div>
                    <div style="text-align: right;">
                        <button type="submit" class="reply-submit">
                            <ion-icon name="send-outline"></ion-icon> Send Response
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: Ticket Details & Sidebar -->
        <div>
            <!-- Info Status Details Card -->
            <div class="meta-box" style="margin-bottom: 24px;">
                <div class="meta-header"><ion-icon name="information-circle-outline"></ion-icon> Ticket Status</div>
                <div class="meta-body">
                    <!-- Status Dropdown Form -->
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #475569; margin-bottom: 6px;">Manage Status</label>
                        <form method="POST" action="/casper-joe/support/updateStatus">
                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open / Active</option>
                                <option value="answered" <?= $ticket['status'] === 'answered' ? 'selected' : '' ?>>Answered</option>
                                <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </form>
                    </div>

                    <!-- Priority Badge -->
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #475569; margin-bottom: 6px;">Priority Level</label>
                        <?php if ($ticket['priority'] === 'high'): ?>
                            <span style="background: #fee2e2; color: #ef4444; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span> High Priority
                            </span>
                        <?php elseif ($ticket['priority'] === 'medium'): ?>
                            <span style="background: #fef3c7; color: #d97706; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span> Medium Priority
                            </span>
                        <?php else: ?>
                            <span style="background: #f1f5f9; color: #64748B; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748B;"></span> Low Priority
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Date Created -->
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #475569; margin-bottom: 4px;">Date Opened</label>
                        <span style="font-size: 0.9rem; color: #1e293b; font-weight: 500;">
                            <?= date('M j, Y • g:i A', strtotime($ticket['created_at'])) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Meta Info Card -->
            <div class="meta-box" style="margin-bottom: 24px;">
                <div class="meta-header"><ion-icon name="person-outline"></ion-icon> Customer Details</div>
                <div class="meta-body">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; color: #000066; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                            <?= strtoupper(substr($ticket['user_name'], 0, 2)) ?>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($ticket['user_name']) ?></div>
                            <div style="font-size: 0.78rem; color: #64748B; word-break: break-all;"><?= htmlspecialchars($ticket['user_email']) ?></div>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.82rem; color: #475569; margin-bottom: 4px;">User Domain / Account</label>
                        <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-family: monospace; border: 1px solid #e2e8f0; display: inline-block;">
                            <?= htmlspecialchars($ticket['tenant_domain']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
