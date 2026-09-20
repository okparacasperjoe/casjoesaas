<?php
$title = 'Virtual Cards';
include __DIR__ . '/header.php';

$csrfToken = \App\Core\Services\CsrfService::getToken();
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Virtual Cards (<?= count($cards) ?>)</h5>
                <div>
                    <?php if (!empty($cards)): ?>
                        <form method="POST" action="/<?= ADMIN_PATH ?>/cards/delete-all" onsubmit="return confirm('WARNING: This will TERMINATE ALL CARDS in the system. Are you absolutely sure?');" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <button type="submit" class="btn btn-sm btn-danger mr-2">Terminate All</button>
                        </form>
                    <?php endif; ?>
                    <a href="/<?= ADMIN_PATH ?>/settings" class="btn btn-sm btn-secondary">Settings</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>User</th>
                                <th>Card Details</th>
                                <th>Provider</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cards)): ?>
                                <tr>
                                    <td colspan="7" class="text-center p-4">
                                        <div class="text-muted">No virtual cards found.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cards as $card):
                                    $dbStatus  = trim($card['status'] ?? '');

                                    // Use live API data for approved cards
                                    $live       = $liveCardData[$card['card_id']] ?? null;
                                    $liveStatus = $live ? ($live['card_status'] ?? $live['status'] ?? $dbStatus) : $dbStatus;
                                    $liveBal    = $live ? ($live['balance'] ?? '—') : '—';
                                    $livePan    = $live ? ($live['card_number'] ?? $live['pan'] ?? null) : null;
                                    $liveMasked = $livePan ? ('**** ' . substr($livePan, -4)) : ($live['masked_pan'] ?? '****');
                                    $expiryStr  = $live ? ($live['expiration'] ?? $live['expiry_date'] ?? '') : '';
                                    $expParts   = $expiryStr ? explode('/', $expiryStr) : [];
                                    $liveExpM   = isset($expParts[0]) ? sprintf('%02d', (int)$expParts[0]) : '--';
                                    $liveExpY   = isset($expParts[1]) ? $expParts[1] : '--';

                                    // For admin_pending — show DB status, no live data
                                    $displayStatus = ($dbStatus === 'admin_pending') ? 'admin_pending' : strtolower($liveStatus);
                                ?>
                                    <tr>
                                        <td>
                                            <strong class="text-dark"><?= htmlspecialchars($card['user_name'] ?? 'Unknown User') ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($card['user_email'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <code class="text-dark font-weight-bold"><?= htmlspecialchars($liveMasked) ?></code>
                                            <br>
                                            <small><?= $liveExpM ?>/<?= $liveExpY ?></small>
                                            <?php if ($live === null && $dbStatus !== 'admin_pending'): ?>
                                                <br><small class="text-warning">⚠ API unavailable</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= htmlspecialchars(ucfirst($card['provider'] ?? 'unknown')) ?></span>
                                            <?php if (($card['card_type'] ?? '') === 'nfc'): ?>
                                                <br><span class="badge badge-dark" style="font-size:0.65rem;">NFC</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-success font-weight-bold">
                                                <?= is_numeric($liveBal) ? number_format((float)$liveBal, 2) : $liveBal ?>
                                            </span> <small><?= htmlspecialchars($card['currency'] ?? 'USD') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'secondary';
                                            $statusText = ucfirst($displayStatus ?: 'unknown');
                                            if ($displayStatus === 'active')       { $badgeClass = 'success'; $statusText = 'Active'; }
                                            elseif ($displayStatus === 'inactive' || $displayStatus === 'frozen') { $badgeClass = 'warning'; }
                                            elseif ($displayStatus === 'admin_pending') { $badgeClass = 'primary'; $statusText = 'Pending Approval'; }
                                            elseif ($displayStatus === 'rejected') { $badgeClass = 'danger'; }
                                            elseif ($displayStatus === 'pending')  { $badgeClass = 'warning'; $statusText = 'Processing'; }
                                            ?>
                                            <span class="badge badge-<?= $badgeClass ?>">
                                                <?= htmlspecialchars($statusText) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($card['created_at'] ?? 'now')) ?></td>
                                        <td class="text-right">
                                            <?php if ($dbStatus === 'admin_pending'): ?>
                                                <form method="POST" action="/<?= ADMIN_PATH ?>/cards/approve" onsubmit="return confirm('Approve this request? The system will create the virtual card now.');" style="display: inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="id" value="<?= $card['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve Request">Approve</button>
                                                </form>
                                                <form method="POST" action="/<?= ADMIN_PATH ?>/cards/reject" onsubmit="return confirm('Reject this request? This will refund the user automatically.');" style="display: inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="id" value="<?= $card['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject Request">Reject</button>
                                                </form>
                                            <?php elseif ($displayStatus === 'active'): ?>
                                                <form method="POST" action="/<?= ADMIN_PATH ?>/cards/toggle-status" onsubmit="return confirm('Freeze this card?');" style="display: inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="id" value="<?= $card['id'] ?>">
                                                    <input type="hidden" name="action" value="freeze">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Freeze Card">Freeze</button>
                                                </form>
                                            <?php elseif ($displayStatus === 'inactive' || $displayStatus === 'frozen'): ?>
                                                <form method="POST" action="/<?= ADMIN_PATH ?>/cards/toggle-status" onsubmit="return confirm('Unfreeze this card?');" style="display: inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="id" value="<?= $card['id'] ?>">
                                                    <input type="hidden" name="action" value="unfreeze">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Unfreeze Card">Unfreeze</button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" action="/<?= ADMIN_PATH ?>/cards/delete" onsubmit="return confirm('PERMANENTLY DELETE this card? This acts as a Termination.');" style="display: inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                <input type="hidden" name="id" value="<?= $card['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Terminate Card">
                                                    Delete
                                                </button>
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
