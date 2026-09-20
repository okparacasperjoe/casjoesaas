<?php
$pageTitle = 'Manage Offline Invoices';
include __DIR__ . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: rgba(46, 213, 115, 0.2); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3);">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #fff;">Pending Module Invoices</h3>
                <span class="badge warning" style="background: rgba(255, 166, 0, 0.2); color: #ffa502; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem;">Bank Transfer Subscriptions</span>
            </div>
            
            <div class="table-container" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #888;">Date</th>
                            <th style="text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #888;">Workspace / User</th>
                            <th style="text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #888;">Reference</th>
                            <th style="text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #888;">Amount</th>
                            <th style="text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #888;">Status</th>
                            <th style="text-align: left; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); color: #888;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #666;">No pending invoices found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($invoices as $tx): ?>
                                <tr>
                                    <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #eee;">
                                        <?= date('M j, Y H:i', strtotime($tx['date'])) ?>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #eee;">
                                        <strong><?= htmlspecialchars($tx['domain'] ?? 'Unknown Domain') ?></strong><br>
                                        <small style="color: #888;"><?= htmlspecialchars($tx['user_name'] ?? '') ?> (<?= htmlspecialchars($tx['user_email'] ?? '') ?>)</small>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #aaa; font-family: monospace;">
                                        <?= htmlspecialchars($tx['reference']) ?>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #eee; font-weight: bold;">
                                        <?= $tx['currency'] ?> <?= number_format($tx['amount'], 2) ?>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <span style="background: rgba(255, 166, 0, 0.2); color: #ffa502; padding: 5px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">Pending</span>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <div style="display: flex; gap: 10px;">
                                            <a href="/casper-joe/invoices/approve/<?= $tx['id'] ?>" onclick="return confirm('Approve this invoice? The module will be activated.');" style="background: #2ed573; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: 500; text-decoration: none;" title="Approve">
                                                <ion-icon name="checkmark-outline"></ion-icon> Approve
                                            </a>
                                            <a href="/casper-joe/invoices/reject/<?= $tx['id'] ?>" onclick="return confirm('Reject this invoice?');" style="background: #ff4757; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: 500; text-decoration: none;" title="Reject">
                                                <ion-icon name="close-outline"></ion-icon> Reject
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
