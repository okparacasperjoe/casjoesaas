<?php
$title = 'Campaign Analytics';
include __DIR__ . '/../../../../Core/Views/admin/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Campaign: <?= htmlspecialchars($campaign['name']) ?></h5>
                <a href="/mail/campaigns" class="btn btn-secondary btn-sm">Back to Campaigns</a>
            </div>
            <div class="card-body">
                <!-- Campaign Stats -->
                <div class="row text-center mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0 text-primary"><?= number_format($stats['sent'] ?? 0) ?></h3>
                            <small class="text-muted">Total Sent</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0 text-success"><?= number_format($stats['opened'] ?? 0) ?></h3>
                            <small class="text-muted">Opened (<?= $stats['open_rate'] ?? 0 ?>%)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0 text-info"><?= number_format($stats['clicked'] ?? 0) ?></h3>
                            <small class="text-muted">Clicked (<?= $stats['click_rate'] ?? 0 ?>%)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <h3 class="mb-0 text-warning"><?= number_format($stats['bounced'] ?? 0) ?></h3>
                            <small class="text-muted">Bounced</small>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <h6 class="mb-3">Recent Activity</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subscriber</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Opened At</th>
                                <th>Clicked URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($analytics)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No activity yet</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($analytics as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['first_name'] ?: 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td>
                                            <?php if ($row['status'] === 'opened'): ?>
                                                <span class="badge bg-success">Opened</span>
                                            <?php elseif ($row['status'] === 'clicked'): ?>
                                                <span class="badge bg-info">Clicked</span>
                                            <?php elseif ($row['status'] === 'bounced'): ?>
                                                <span class="badge bg-danger">Bounced</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sent</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['opened_at']): ?>
                                                <small><?= date('M j, Y g:i A', strtotime($row['opened_at'])) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">—</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['clicked_url']): ?>
                                                <small class="text-truncate" style="max-width: 200px; display: inline-block;" title="<?= htmlspecialchars($row['clicked_url']) ?>">
                                                    <?= htmlspecialchars($row['clicked_url']) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">—</small>
                                            <?php endif; ?>
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

<?php include __DIR__ . '/../../../../Core/Views/admin/footer.php'; ?>
