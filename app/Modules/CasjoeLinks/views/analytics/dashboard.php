<?php 
$title = "Analytics Dashboard";
require __DIR__ . '/../../../../Core/Views/global_header.php';
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Analytics Dashboard</h1>
            <p class="text-muted">Overview of your link performance (Last 30 Days)</p>
        </div>
        <div>
           <a href="/links" class="btn btn-outline-secondary">Back to Links</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Total Visits</h6>
                    <h2 class="display-5 fw-bold text-primary mb-0"><?= number_format($summary['total_visits']) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted small fw-bold mb-2">Unique Visitors</h6>
                    <h2 class="display-5 fw-bold text-success mb-0"><?= number_format($summary['unique_visitors']) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Devices -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Device Breakdown</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($devices)): ?>
                        <p class="text-muted text-center py-4">No data available</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach($devices as $d): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><?= htmlspecialchars($d['device_type'] ?: 'Unknown') ?></span>
                                    <span class="badge bg-light text-dark rounded-pill"><?= $d['count'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Referrers -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Top Referrers</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($referrers)): ?>
                        <p class="text-muted text-center py-4">No data available</p>
                    <?php else: ?>
                         <ul class="list-group list-group-flush">
                            <?php foreach($referrers as $r): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 text-truncate">
                                    <span class="text-truncate" style="max-width: 80%;" title="<?= htmlspecialchars($r['referrer']) ?>">
                                        <?= htmlspecialchars($r['referrer']) ?>
                                    </span>
                                    <span class="badge bg-light text-dark rounded-pill"><?= $r['count'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logistics -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
             <h5 class="fw-bold mb-0">Recent Activity Log</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background-color: #000066; color: #ffffff;">
                    <tr>
                        <th class="ps-4" style="color: #ffffff;">Time</th>
                        <th style="color: #ffffff;">Type</th>
                        <th style="color: #ffffff;">Link</th>
                        <th style="color: #ffffff;">Visitor Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($recent)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No activity recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($recent as $log): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; background-color: #ffffff;">
                            <td class="ps-4 text-nowrap" style="color: #1e293b !important; font-weight: 500; background-color: #ffffff;"><?= date('M j, H:i', strtotime($log['visited_at'])) ?></td>
                            <td style="background-color: #ffffff;">
                                <?php if($log['link_type'] == 'short'): ?>
                                    <span class="badge bg-primary">Short URL</span>
                                <?php elseif($log['link_type'] == 'bio'): ?>
                                    <span class="badge bg-purple text-white" style="background-color: #6f42c1;">Bio Page</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($log['link_type']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #1e293b !important; font-weight: 600; background-color: #ffffff;"><?= htmlspecialchars($log['link_name'] ?? 'ID: '.$log['link_id']) ?></td>
                            <td style="color: #475569 !important; background-color: #ffffff;">
                                <div class="small">
                                    <i class="bi bi-laptop"></i> <?= htmlspecialchars($log['os'] . ' / ' . $log['browser']) ?>
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

<?php require __DIR__ . '/../../../../Core/Views/global_footer.php'; ?>

