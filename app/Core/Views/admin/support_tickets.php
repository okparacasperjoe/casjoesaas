<?php
$pageTitle = "Support Tickets";
require_once __DIR__ . '/header.php';
?>

<style>
    .support-stat-card {
        background: #fff; padding: 24px; border-radius: 16px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; 
        display: flex; align-items: center; gap: 20px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative; overflow: hidden;
    }
    .support-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .support-stat-card::after {
        content: ''; position: absolute; top: 0; right: 0;
        width: 100px; height: 100px;
        background: radial-gradient(circle, rgba(0,0,102,0.05) 0%, transparent 70%);
        pointer-events: none; opacity: 0; transition: opacity 0.3s;
    }
    .support-stat-card:hover::after { opacity: 1; }
    
    .ticket-table tr { transition: all 0.2s; }
    .ticket-table tr:hover { background: #f8fafc; box-shadow: inset 4px 0 0 #000066; }
</style>

<div class="top-bar" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="color: #000066; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; font-size: 1.8rem;">
        <div style="background: #000066; color: #FFA600; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <ion-icon name="help-buoy-outline"></ion-icon>
        </div>
        Support Desk
    </h2>
</div>

<div class="settings-container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <!-- Filters and Statistics Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px;">
        <div class="support-stat-card">
            <div style="background: #f1f5f9; color: #3b82f6; width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                <ion-icon name="mail-open-outline"></ion-icon>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Total Tickets</div>
                <div style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 4px;"><?= count($tickets) ?></div>
            </div>
        </div>
        
        <div class="support-stat-card">
            <div style="background: #fef2f2; color: #ef4444; width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                <ion-icon name="alert-circle-outline"></ion-icon>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">High Priority</div>
                <div style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 4px;">
                    <?= count(array_filter($tickets, function($t) { return $t['priority'] === 'high'; })) ?>
                </div>
            </div>
        </div>

        <div class="support-stat-card">
            <div style="background: #ecfdf5; color: #10b981; width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                <ion-icon name="chatbubbles-outline"></ion-icon>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Open / Active</div>
                <div style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-top: 4px;">
                    <?= count(array_filter($tickets, function($t) { return $t['status'] === 'open'; })) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets List Box -->
    <div class="integration-box" style="background: #fff; padding: 0; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="padding: 24px; border-bottom: 1px solid #f0f2f5; display: flex; justify-content: space-between; align-items: center; background: #fff;">
            <h4 style="margin: 0; color: #0f172a; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <ion-icon name="list-outline" style="color: #FFA600; font-size: 1.4rem;"></ion-icon> Active Conversations
            </h4>
        </div>

        <?php if (empty($tickets)): ?>
            <div style="text-align: center; padding: 100px 20px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <ion-icon name="mail-unread-outline" style="font-size: 2.5rem; color: #cbd5e1;"></ion-icon>
                </div>
                <h3 style="color: #0f172a; font-size: 1.3rem; margin-bottom: 8px; font-weight: 700;">No tickets found</h3>
                <p style="color: #64748b; max-width: 320px; margin: 0 auto; font-size: 0.95rem; line-height: 1.5;">Any support requests submitted by users will appear here.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="ticket-table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f0f2f5; background: #fafbfc;">
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Ticket Details</th>
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Customer</th>
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Domain</th>
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Priority</th>
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                            <th style="padding: 18px 24px; color: #64748b; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 20px 24px; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #000066; margin-bottom: 4px;">#<?= $ticket['id'] ?></div>
                                    <div style="color: #1e293b; font-weight: 600;"><?= htmlspecialchars($ticket['subject'] ?? '') ?></div>
                                </td>
                                <td style="padding: 18px 24px; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #e2e8f0; color: #000066; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                                            <?= strtoupper(substr($ticket['user_name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($ticket['user_name'] ?? 'Unknown User') ?></div>
                                            <div style="font-size: 0.78rem; color: #64748B;"><?= htmlspecialchars($ticket['user_email'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 18px 24px; vertical-align: middle;">
                                    <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-family: monospace; border: 1px solid #e2e8f0;">
                                        <?= htmlspecialchars($ticket['tenant_domain'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td style="padding: 18px 24px; vertical-align: middle;">
                                    <?php if ($ticket['priority'] === 'high'): ?>
                                        <span style="background: #fee2e2; color: #ef4444; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span> High
                                        </span>
                                    <?php elseif ($ticket['priority'] === 'medium'): ?>
                                        <span style="background: #fef3c7; color: #d97706; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span> Medium
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #64748B; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748B;"></span> Low
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px 24px; vertical-align: middle;">
                                    <?php if ($ticket['status'] === 'open'): ?>
                                        <span style="background: #dbeafe; color: #2563eb; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">
                                            Open
                                        </span>
                                    <?php elseif ($ticket['status'] === 'answered'): ?>
                                        <span style="background: #d1fae5; color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">
                                            Answered
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #64748B; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">
                                            Closed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 18px 24px; vertical-align: middle; color: #64748B;">
                                    <?= date('M d, Y', strtotime($ticket['created_at'])) ?>
                                </td>
                                <td style="padding: 20px 24px; vertical-align: middle; text-align: right;">
                                    <a href="/casper-joe/support/<?= $ticket['id'] ?>" class="btn-primary" style="padding: 8px 20px; background: #000066; color: #FFA600; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s; border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,102,0.15);">
                                        Manage <ion-icon name="arrow-forward-outline"></ion-icon>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
