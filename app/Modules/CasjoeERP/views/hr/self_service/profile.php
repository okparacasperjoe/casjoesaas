<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>My Profile | Casjoe BOS</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
            .sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                height: 100%;
                z-index: 1000;
                transition: 0.3s;
                width: 260px;
                background-color: #000066; 
            }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; width: 100%; padding-top: 70px !important; }
        }
        
        .premium-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            border-top: 5px solid #ffa600;
            margin: auto;
            max-width: 800px;
        }
        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            width: 100%;
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <?php require dirname(__DIR__, 5) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <?php require __DIR__ . '/../../layout/sidebar.php'; ?>
    <main class="main-content">
        <div class="top-bar">
            <h2>My Profile</h2>
            <a href="/erp/my-portal" class="btn" style="background: #666;">Back</a>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 500;">
                ✅ Profile details updated successfully!
            </div>
        <?php endif; ?>

        <div class="premium-card">
             <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 100px; height: 100px; background: #eee; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 3rem; color: #888; margin-bottom: 10px;">
                    <ion-icon name="person"></ion-icon>
                </div>
                <h2><?= htmlspecialchars(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')) ?></h2>
                <p style="color: #666;"><?= htmlspecialchars($employee['position'] ?? 'Employee') ?></p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['email'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['phone'] ?? 'N/A') ?>" readonly>
                </div>
                 <div class="form-group">
                    <label>Department</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['department'] ?? 'General') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Date Joined</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['hire_date'] ?? $employee['date_joined'] ?? 'N/A') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" readonly><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
                </div>
                 <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['emergency_contact'] ?? 'Not set') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['bank_name'] ?? 'Not set') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Bank Account Number</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['bank_account_number'] ?? 'Not set') ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Bank Account Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($employee['bank_account_name'] ?? 'Not set') ?>" readonly>
                </div>
            </div>
            
            <div style="margin-top: 30px; text-align: right;">
                <button class="btn btn-primary" onclick="openUpdateProfileModal()"><ion-icon name="create-outline" style="vertical-align:middle;"></ion-icon> Request Profile Update</button>
            </div>
        </div>
    </main>
</div>

<!-- CSS overrides for Modals (if not already styled) -->
<style>
    .cancel-modal { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 10000; align-items: center; justify-content: center; }
    .cancel-modal.active { display: flex; }
    .modal-content { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 28px; width: 90%; max-width: 520px; color: #1e293b; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
</style>

<!-- Update Profile Modal -->
<div class="cancel-modal" id="updateProfileModal">
    <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
        <h3 style="color:#000066; margin-top:0; font-size:1.3rem; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
            <ion-icon name="create-outline" style="font-size:1.5rem; color:#FFA600; vertical-align:middle;"></ion-icon> Update Profile & Bank Details
        </h3>
        <form method="POST" action="/erp/my-portal/profile/update">
            <?= \App\Core\Services\CsrfService::getTokenField() ?>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($employee['phone'] ?? '') ?>" placeholder="+234..." style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Emergency Contact</label>
                <input type="text" name="emergency_contact" class="form-control" value="<?= htmlspecialchars($employee['emergency_contact'] ?? '') ?>" placeholder="e.g. Jane Doe (+234...)" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;">
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#334155;">Residential Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Enter your full home address..." style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:10px; width:100%; box-sizing:border-box;"><?= htmlspecialchars($employee['address'] ?? '') ?></textarea>
            </div>
            
            <div style="margin-top:16px; margin-bottom:16px; padding:16px; background:#f8fafc; border-radius:10px; border:1px solid #cbd5e1;">
                <h4 style="color:#000066; margin:0 0 12px 0; font-size:1rem; font-weight:600;">Bank Account Information</h4>
                <div class="form-group" style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:4px; font-size:0.85rem; font-weight:600; color:#334155;">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($employee['bank_name'] ?? '') ?>" placeholder="e.g. Access Bank" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:8px 10px; width:100%; box-sizing:border-box;">
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:4px; font-size:0.85rem; font-weight:600; color:#334155;">Account Number</label>
                    <input type="text" name="bank_account_number" class="form-control" value="<?= htmlspecialchars($employee['bank_account_number'] ?? '') ?>" placeholder="e.g. 0123456789" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:8px 10px; width:100%; box-sizing:border-box;">
                </div>
                <div class="form-group" style="margin-bottom:4px;">
                    <label style="display:block; margin-bottom:4px; font-size:0.85rem; font-weight:600; color:#334155;">Account Holder Name</label>
                    <input type="text" name="bank_account_name" class="form-control" value="<?= htmlspecialchars($employee['bank_account_name'] ?? '') ?>" placeholder="e.g. Casper Joe" style="background:#ffffff; border:1px solid #cbd5e1; color:#0f172a; border-radius:8px; padding:8px 10px; width:100%; box-sizing:border-box;">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn" style="background:#f1f5f9; color:#334155 !important; border:1px solid #cbd5e1; padding:10px 18px; border-radius:8px; cursor:pointer;" onclick="closeUpdateProfileModal()">Dismiss</button>
                <button type="submit" class="btn" style="background:#000066; color:#ffffff !important; font-weight:600; border:none; padding:10px 22px; border-radius:8px; cursor:pointer;"><ion-icon name="checkmark-circle-outline" style="margin-right:6px;"></ion-icon> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUpdateProfileModal() {
        document.getElementById('updateProfileModal').classList.add('active');
    }
    function closeUpdateProfileModal() {
        document.getElementById('updateProfileModal').classList.remove('active');
    }
</script>
</body>
</html>
