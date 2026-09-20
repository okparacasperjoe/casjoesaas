<?php
$pageTitle = 'Module Management';
require __DIR__ . '/header.php';
?>
<style>
    .modules-container { max-width: 1000px; margin: 0 auto; background: var(--glass-bg); padding: 30px; border-radius: 15px; border: 1px solid var(--glass-border); }
    .table { width: 100%; border-collapse: collapse; margin-top: 20px; color: #eee; }
    .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .table th { color: var(--secondary); font-weight: 600; }
    .form-control { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 10px; border-radius: 8px; width: 100px; }
</style>
        <div class="top-bar">
            <h2><ion-icon name="cube-outline" style="vertical-align: middle;"></ion-icon> Module Management</h2>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;">Modules updated successfully.</div>
        <?php endif; ?>

        <div class="modules-container">
            <form action="/<?= ADMIN_PATH ?>/modules/update" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo \App\Core\Services\CsrfService::generateToken(); ?>">
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Module Name</th>
                                <th>Slug</th>
                                <th>Current Price (USD)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $module): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: bold;"><?php echo htmlspecialchars($module['name']); ?></div>
                                        <div style="font-size: 0.8em; opacity: 0.7;"><?php echo htmlspecialchars($module['description'] ?? ''); ?></div>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($module['slug']); ?></code></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <span>$</span>
                                            <input type="number" step="0.01" name="prices[<?php echo $module['id']; ?>]" class="form-control" value="<?php echo number_format($module['price'], 2, '.', ''); ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <span style="padding: 5px 10px; border-radius: 15px; font-size: 0.8em; background: rgba(108, 92, 231, 0.2); color: #a29bfe;">
                                            <?php echo ($module['price'] > 0) ? 'Paid' : 'Free'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <h3 style="margin-top: 40px; color: var(--brand-white); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">AI Add-ons Pricing</h3>
                <div style="display: flex; gap: 20px; margin-top: 20px;">
                    <div>
                        <label style="display: block; font-size: 0.85em; color: var(--text-muted); margin-bottom: 5px;">AI Employee Price (NGN)</label>
                        <input type="number" name="ai_employee_price_ngn" class="form-control" style="width: 150px;" value="<?= htmlspecialchars(AI_EMPLOYEE_PRICE_NGN) ?>">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85em; color: var(--text-muted); margin-bottom: 5px;">AI Employee Price (USD)</label>
                        <input type="number" step="0.01" name="ai_employee_price_usd" class="form-control" style="width: 150px;" value="<?= htmlspecialchars(AI_EMPLOYEE_PRICE_USD) ?>">
                    </div>
                </div>

                <div class="mt-3" style="margin-top: 30px; text-align: right;">
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </form>
<?php require __DIR__ . '/footer.php'; ?>
