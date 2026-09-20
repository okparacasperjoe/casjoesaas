<?php
$title = 'System Configuration';
include __DIR__ . '/../header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">System Configuration</h1>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Settings updated successfully!</div>
        <?php endif; ?>

        <form action="/<?= ADMIN_PATH ?>/settings/update" method="POST">
            <?= \App\Core\Services\CsrfService::generateInput() ?>

            <!-- Global Settings and Features -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                     <h5 class="mb-0"><i class="fas fa-cogs"></i> Global Settings</h5>
                </div>
                <div class="card-body">
                     <div class="row">
                         <!-- Feature Toggles -->
                         <div class="col-md-6 mb-3">
                             <div class="form-check form-switch ps-0">
                                 <label class="form-check-label fw-bold d-block mb-2" for="recaptcha">Security Features</label>
                                 <div class="form-check form-switch">
                                     <input class="form-check-input" type="checkbox" id="recaptcha" name="settings[recaptcha_enabled]" value="1" <?= ($settings['recaptcha_enabled'] ?? '1') == '1' ? 'checked' : '' ?>>
                                     <label class="form-check-label" for="recaptcha">Enable Google reCAPTCHA on Login/Register</label>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <hr>
                     <div class="row">
                         <!-- Bank Details -->
                         <div class="col-md-6">
                             <h6 class="text-secondary fw-bold">Manual Bank Transfer Details</h6>
                             <div class="mb-3">
                                 <label class="form-label">Bank Name</label>
                                 <input type="text" class="form-control" name="settings[bank_details_bank]" value="<?= htmlspecialchars($settings['bank_details_bank'] ?? '') ?>" placeholder="e.g. Chase Bank">
                             </div>
                             <div class="mb-3">
                                 <label class="form-label">Account Name</label>
                                 <input type="text" class="form-control" name="settings[bank_details_account_name]" value="<?= htmlspecialchars($settings['bank_details_account_name'] ?? '') ?>" placeholder="e.g. Casjoe Ltd">
                             </div>
                             <div class="mb-3">
                                 <label class="form-label">Account Number</label>
                                 <input type="text" class="form-control" name="settings[bank_details_number]" value="<?= htmlspecialchars($settings['bank_details_number'] ?? '') ?>" placeholder="Account No.">
                             </div>
                             <div class="mb-3">
                                 <label class="form-label">SWIFT / IBAN (Optional)</label>
                                 <input type="text" class="form-control" name="settings[bank_details_swift]" value="<?= htmlspecialchars($settings['bank_details_swift'] ?? '') ?>">
                             </div>
                         </div>
                         
                         <!-- SMTP Details -->
                         <div class="col-md-6">
                             <h6 class="text-secondary fw-bold">System Email (SMTP)</h6>
                             <div class="mb-3">
                                 <label class="form-label">SMTP Host</label>
                                 <input type="text" class="form-control" name="settings[smtp_host]" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="smtp.example.com">
                             </div>
                             <div class="mb-3">
                                 <label class="form-label">SMTP User</label>
                                 <input type="text" class="form-control" name="settings[smtp_user]" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>" placeholder="user@example.com">
                             </div>
                             <div class="mb-3">
                                 <label class="form-label">SMTP Password</label>
                                 <input type="password" class="form-control" name="settings[smtp_pass]" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '') ?>">
                             </div>
                             <div class="mb-3">
                                 <label class="form-label">SMTP Port</label>
                                 <input type="number" class="form-control" name="settings[smtp_port]" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>">
                             </div>
                         </div>
                     </div>
                </div>
            </div>

            <!-- Module Pricing & Limits -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-tags"></i> Module Pricing & Limits</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Module</th>
                                <th width="150">Price (USD)</th>
                                <th>Billing Cycle</th>
                                <th>Free Limit Rule</th>
                                <th>Paid Limit Rule</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $mod): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($mod['name']) ?></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control" name="modules[<?= $mod['id'] ?>][price]" value="<?= $mod['price'] ?>">
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $mod['billing_cycle'] ?></span>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm" name="modules[<?= $mod['id'] ?>][free_limit]" value="<?= $mod['free_limit'] ?? '' ?>" placeholder="None">
                                    <small class="text-muted d-block mt-1">
                                        <?php 
                                            if($mod['slug'] == 'casjoe-erp') echo "Max Users";
                                            elseif($mod['slug'] == 'casjoe-links') echo "Max QR Codes";
                                            else echo "Limit Count";
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="modules[<?= $mod['id'] ?>][paid_limit]" value="<?= $mod['paid_limit'] ?? '' ?>" placeholder="Unlimited or Value">
                                    <small class="text-muted d-block mt-1">
                                        <?php 
                                            if($mod['slug'] == 'casjoe-mail') echo "Emails/Month";
                                            elseif($mod['slug'] == 'casjoe-cloud') echo "Storage (GB)";
                                            else echo "Paid Cap";
                                        ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
include __DIR__ . '/../footer.php'; 
?>
