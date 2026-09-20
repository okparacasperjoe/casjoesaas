<?php
$title = 'Withdrawal Requests';
include __DIR__ . '/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Pending Withdrawals</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($withdrawals)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No pending withdrawals.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($withdrawals as $tx): 
                                    $meta = json_decode($tx['meta'] ?? '{}', true);
                                    $details = $meta['details'] ?? [];
                                ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($tx['user_name']) ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($tx['user_email']) ?></small>
                                        </td>
                                        <td>
                                            <?= number_format($tx['amount'], 2) ?> <?= htmlspecialchars($tx['currency']) ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($meta['type'] ?? 'Unknown') ?></strong><br>
                                            <small><?= htmlspecialchars($tx['description']) ?></small>
                                        </td>
                                        <td><?= date('M d, Y H:i', strtotime($tx['created_at'])) ?></td>
                                        <td>
                                            <form method="POST" action="/<?= ADMIN_PATH ?>/withdrawals/approve" style="display:inline;">
                                                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                                <input type="hidden" name="id" value="<?= $tx['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="POST" action="/<?= ADMIN_PATH ?>/withdrawals/reject" style="display:inline;">
                                                <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                                <input type="hidden" name="id" value="<?= $tx['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
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
</div>

<?php include __DIR__ . '/footer.php'; ?>
