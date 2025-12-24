<?php require __DIR__ . '/../../../../views/partials/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Casjoe Mail</h1>
    <div>
        <a href="/mail/compose" class="btn">Compose New</a>
        <a href="/mail/settings" class="btn" style="background:#666;">Configuration</a>
    </div>
</div>

<div class="card">
    <h3>Recent Activity</h3>
    <table>
        <thead>
            <tr>
                <th>To</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="4">No emails sent yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['to_email']) ?></td>
                        <td><?= htmlspecialchars($log['subject']) ?></td>
                        <td>
                            <span
                                style="padding: 4px 8px; border-radius: 4px; color: #fff; background: <?= $log['status'] == 'sent' ? 'green' : 'red' ?>">
                                <?= $log['status'] ?>
                            </span>
                        </td>
                        <td><?= $log['sent_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../../../views/partials/footer.php'; ?>