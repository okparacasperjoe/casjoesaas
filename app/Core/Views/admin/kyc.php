<?php
$title = "KYC Requests - Admin";
$active_nav = 'kyc';
require_once __DIR__ . '/../admin/header.php';
?>

<div class="content-header">
    <h1>Identity Verification Requests</h1>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User & Contact</th>
                    <th>Identity Details</th>
                    <th>Document</th>
                    <th>Selfie</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($verifications)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 30px; color: #64748b;">
                            <div style="font-size: 2rem; margin-bottom: 8px;">📋</div>
                            <strong>No KYC requests found.</strong>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($verifications as $req): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars(!empty(trim(($req['first_name'] ?? '') . ' ' . ($req['last_name'] ?? ''))) ? trim($req['first_name'] . ' ' . $req['last_name']) : ($req['user_name'] ?? 'N/A')) ?></strong><br>
                                <small style="color: #64748b;"><?= htmlspecialchars($req['user_email'] ?? 'N/A') ?></small><br>
                                <?php if (!empty($req['phone']) || !empty($req['user_phone'])): ?>
                                    <small style="color: #64748b;">📞 <?= htmlspecialchars($req['phone'] ?? $req['user_phone']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background:#e0e7ff; color:#3730a3;"><?= htmlspecialchars($req['doc_type'] ?? 'ID Card') ?></span><br>
                                <?php if (!empty($req['id_number'])): ?>
                                    <small><strong>ID No:</strong> <?= htmlspecialchars($req['id_number']) ?></small><br>
                                <?php endif; ?>
                                <?php if (!empty($req['bvn'])): ?>
                                    <small><strong>BVN:</strong> <?= htmlspecialchars($req['bvn']) ?></small><br>
                                <?php endif; ?>
                                <?php if (!empty($req['dob'])): ?>
                                    <small><strong>DOB:</strong> <?= htmlspecialchars($req['dob']) ?></small><br>
                                <?php endif; ?>
                                <?php if (!empty($req['city']) || !empty($req['state'])): ?>
                                    <small style="color:#64748b;">📍 <?= htmlspecialchars(($req['city'] ?? '') . ', ' . ($req['state'] ?? '') . ' ' . ($req['country'] ?? '')) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($req['doc_path'])): ?>
                                    <a href="<?= htmlspecialchars($req['doc_path']) ?>" target="_blank" class="btn-sm" style="display:inline-flex; align-items:center; gap:4px; text-decoration:none;">
                                        📄 View ID
                                    </a>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:0.85rem;">No Document</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($req['selfie_path'])): ?>
                                    <a href="<?= htmlspecialchars($req['selfie_path']) ?>" target="_blank">
                                        <img src="<?= htmlspecialchars($req['selfie_path']) ?>" alt="Selfie" style="height: 50px; width: 50px; object-fit: cover; border-radius: 50%; border: 2px solid #000066; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    </a>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-size:0.85rem;">No Selfie</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $st = strtolower($req['status'] ?? 'pending');
                                    if ($st === 'approved'): 
                                ?>
                                    <span class="badge" style="background:#dcfce7; color:#166534; font-weight:700;">✓ Approved</span>
                                <?php elseif ($st === 'rejected'): ?>
                                    <span class="badge" style="background:#fee2e2; color:#991b1b; font-weight:700;">✗ Rejected</span>
                                    <?php if (!empty($req['admin_notes'])): ?>
                                        <br><small style="color:#dc2626; max-width:140px; display:inline-block;"><?= htmlspecialchars($req['admin_notes']) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge" style="background:#fef3c7; color:#92400e; font-weight:700;">⏳ Pending Review</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:0.85rem; color:#475569;">
                                    <?= date('M j, Y', strtotime($req['created_at'])) ?><br>
                                    <small style="color:#94a3b8;"><?= date('H:i A', strtotime($req['created_at'])) ?></small>
                                </span>
                            </td>
                            <td>
                                <?php if (($req['status'] ?? 'pending') === 'pending'): ?>
                                    <form action="/<?= ADMIN_PATH ?>/kyc/approve" method="POST" style="display:inline-block;" onsubmit="return confirm('Approve this verification request?');">
                                        <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                        <input type="hidden" name="id" value="<?= $req['id'] ?>">
                                        <button type="submit" class="btn-sm btn-success" style="background:#10b981; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:600;">Approve</button>
                                    </form>
                                    <button type="button" class="btn-sm btn-danger" style="background:#ef4444; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-weight:600;" onclick="rejectKyc(<?= $req['id'] ?>)">Reject</button>
                                <?php else: ?>
                                    <form action="/<?= ADMIN_PATH ?>/kyc/approve" method="POST" style="display:inline-block;" onsubmit="return confirm('Re-approve this verification request?');">
                                        <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                        <input type="hidden" name="id" value="<?= $req['id'] ?>">
                                        <button type="submit" class="btn-sm" style="background:#e2e8f0; color:#334155; border:1px solid #cbd5e1; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:0.75rem;">Re-evaluate</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:20px; border-radius:8px; width:400px;">
        <h3>Reject Verification</h3>
        <form action="/<?= ADMIN_PATH ?>/kyc/reject" method="POST">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            <input type="hidden" name="id" id="rejectId">
            <div class="form-group">
                <label>Reason for Rejection</label>
                <textarea name="notes" class="form-control" rows="3" required placeholder="e.g. Document blurry, Selfie doesn't match ID..."></textarea>
            </div>
            <div style="margin-top:15px; text-align:right;">
                <button type="button" class="btn-sm" onclick="document.getElementById('rejectModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-sm btn-danger">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
    function rejectKyc(id) {
        document.getElementById('rejectId').value = id;
        document.getElementById('rejectModal').style.display = 'flex';
    }
</script>

<?php require_once __DIR__ . '/../admin/footer.php'; ?>
