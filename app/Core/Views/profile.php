<?php
$title = "My Profile";
require __DIR__ . '/global_header.php';
?>

<style>
    .profile-tabs {
        display: flex;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        white-space: nowrap;
        border-bottom: 1px solid #dee2e6;
        gap: 0;
    }
    .profile-tabs::-webkit-scrollbar { display: none; }
    .profile-tabs .nav-link {
        color: #64748b;
        border: none;
        border-radius: 0;
        padding: 0.7rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        transition: 0.2s;
    }
    .profile-tabs .nav-link:hover {
        color: #000066;
        background-color: #f8fafc;
    }
    .profile-tabs .nav-link.active {
        color: #000066;
        background-color: transparent;
        border-bottom: 3px solid #FFA600;
        font-weight: 600;
    }
    .profile-card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 10px;
        overflow: hidden;
    }
    .tab-content { padding: 1.25rem; }
    @media (max-width: 768px) {
        .tab-content { padding: 1rem; }
        .profile-tabs .nav-link { padding: 0.6rem 0.75rem; font-size: 0.8rem; }
    }
</style>

<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">My Profile</h4>
                <a href="/logout" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 10px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                    <ion-icon name="log-out-outline"></ion-icon> Logout
                </a>
            </div>

            <?php if (!empty($tenant)): ?>
            <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #020314 0%, #0a1128 100%); color: #fff; border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($tenant['logo'])): ?>
                            <div style="background: rgba(255,255,255,0.08); border: 2px solid rgba(255,166,0,0.5); padding: 8px 12px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <img src="<?= htmlspecialchars($tenant['logo']) ?>" alt="Company Logo" style="max-height: 52px; max-width: 140px; object-fit: contain;">
                            </div>
                        <?php else: ?>
                            <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(255,166,0,0.15); border: 1px solid rgba(255,166,0,0.4); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #FFA600; font-weight: bold;">
                                <?= strtoupper(substr($tenant['name'] ?? 'B', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #FFA600; font-weight: 700;">Active Organization</span>
                            <h5 class="fw-bold mb-1" style="color: #fff; font-size: 1.25rem;"><?= htmlspecialchars($tenant['name'] ?? 'My Business') ?></h5>
                            <div style="font-size: 0.85rem; color: #94A3B8;">
                                <?= !empty($tenant['country']) ? htmlspecialchars($tenant['country']) : 'Global' ?> 
                                <?= !empty($tenant['currency']) ? ' • ' . htmlspecialchars($tenant['currency']) : '' ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="/dashboard" class="btn btn-sm" style="background: rgba(255,166,0,0.15); color: #FFA600; border: 1px solid rgba(255,166,0,0.4); font-weight: 600; border-radius: 10px; padding: 8px 16px; text-decoration: none;">
                            <ion-icon name="grid-outline" style="vertical-align: middle;"></ion-icon> Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Feedback Messages -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-3 border-0 shadow-sm py-2 px-3" role="alert" style="font-size: 0.9rem;">
                    <ion-icon name="checkmark-circle" class="me-1"></ion-icon>
                    <?php 
                        switch($success) {
                            case 'profile_updated': echo 'Profile updated.'; break;
                            case 'currency_updated': echo 'Currency updated.'; break;
                            case 'password_updated': echo 'Password updated.'; break;
                            case 'kyc_submitted': echo 'KYC documents submitted!'; break;
                            case 'kyc_details_updated': echo 'KYC details updated!'; break;
                            default: echo 'Done!';
                        }
                    ?>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3 border-0 shadow-sm py-2 px-3" role="alert" style="font-size: 0.9rem;">
                    <ion-icon name="alert-circle" class="me-1"></ion-icon>
                    <?php 
                         switch($_GET['error']) {
                            case 'name_required': echo 'Name is required.'; break;
                            case 'invalid_current_password': echo 'Wrong current password.'; break;
                            case 'password_mismatch': echo 'Passwords don\'t match.'; break;
                            case 'no_document': echo 'Upload a document.'; break;
                            case 'upload_failed': echo 'Upload failed.'; break;
                            case 'already_exists': echo 'KYC already submitted.'; break;
                            case 'kyc_fields_required': echo 'Please fill in all identity fields (Name, Date of Birth, ID Number) before uploading documents.'; break;
                            case 'not_verified_for_update': echo 'You must be verified to update these details.'; break;
                            case 'db_error': echo 'Database error updating KYC details.'; break;
                            case 'avatar_too_large': echo 'Profile photo is too large. Maximum size is 2MB.'; break;
                            case 'invalid_avatar_type': echo 'Invalid photo type. Allowed formats: JPG, JPEG, PNG, WEBP, GIF.'; break;
                            default: echo 'Something went wrong.';
                         }
                    ?>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabbed Card -->
            <div class="card profile-card">
                <ul class="nav profile-tabs px-2 pt-1" id="profileTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#account" role="tab">Account</a></li>
                    <li class="nav-item"><a class="nav-link" id="referrals-tab" data-bs-toggle="tab" href="#referrals" role="tab">Referrals</a></li>
                    <li class="nav-item"><a class="nav-link" id="preferences-tab" data-bs-toggle="tab" href="#preferences" role="tab">Preferences</a></li>
                    <li class="nav-item"><a class="nav-link" id="kyc-tab" data-bs-toggle="tab" href="#kyc" role="tab">Identity</a></li>
                    <li class="nav-item"><a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab">Password</a></li>
                    <li class="nav-item"><a class="nav-link" id="security-tab" data-bs-toggle="tab" href="#security" role="tab">Security</a></li>
                </ul>

                <div class="tab-content" id="profileTabsContent">

                    <!-- Account Tab -->
                    <div class="tab-pane fade show active" id="account" role="tabpanel">
                        <h6 class="fw-bold mb-3" style="color: #000066;">Account Details</h6>
                        <form action="/profile/update" method="POST" enctype="multipart/form-data">
                            <?= \App\Core\Services\CsrfService::getTokenField() ?>
                            
                            <!-- Avatar Section -->
                            <div class="mb-3 d-flex align-items-center gap-3">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="rounded-circle shadow-sm border border-2 border-primary" style="width: 70px; height: 70px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle shadow-sm border border-2 border-primary d-flex align-items-center justify-content-center bg-light text-primary" style="width: 70px; height: 70px; font-size: 1.8rem; font-weight: bold;">
                                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <label class="form-label small text-muted mb-1 d-block">Profile Photo (Max 2MB)</label>
                                    <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Email <span class="text-muted">(read-only)</span></label>
                                <input type="email" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-sm" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="e.g. +234...">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold px-3" style="background:#000066;border:none;">Update</button>
                        </form>
                    </div>

                    <!-- Referrals Tab -->
                    <div class="tab-pane fade" id="referrals" role="tabpanel">
                        <h6 class="fw-bold mb-2" style="color: #000066;">Referral Program</h6>
                        <p class="text-muted small mb-3">Invite friends and earn rewards!</p>
                        <div class="bg-light p-2 rounded mb-3">
                            <label class="form-label small fw-bold mb-1" style="color:#000066;">Your Referral Link</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control border-0 bg-white" value="https://<?= $_SERVER['HTTP_HOST'] ?>/register?ref=<?= htmlspecialchars($user['referral_code'] ?? '') ?>" id="referralLink" readonly style="font-size:0.8rem;">
                                <button class="btn btn-dark px-3" type="button" onclick="copyReferralLink()">Copy</button>
                            </div>
                        </div>
                        <h6 class="fw-bold small mb-2">My Referrals (<?= count($referrals ?? []) ?>)</h6>
                        <?php if (empty($referrals)): ?>
                            <div class="text-center py-4 bg-light rounded text-muted small">
                                <ion-icon name="people-outline" style="font-size:1.5rem;opacity:0.4;display:block;margin:0 auto 5px;"></ion-icon>
                                No referrals yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-hover mb-0" style="font-size:0.85rem;">
                                    <thead class="bg-light"><tr><th class="border-0 px-2">User</th><th class="border-0">Joined</th><th class="border-0 text-end px-2">Status</th></tr></thead>
                                    <tbody>
                                        <?php foreach($referrals as $ref): ?>
                                        <tr>
                                            <td class="px-2">
                                                <strong><?= htmlspecialchars($ref['name'] ?: 'User') ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars(substr($ref['email'], 0, 3) . '***' . substr($ref['email'], strpos($ref['email'], '@'))) ?></small>
                                            </td>
                                            <td class="align-middle"><?= date('M j, Y', strtotime($ref['created_at'])) ?></td>
                                            <td class="align-middle text-end px-2">
                                                <?php if($ref['is_verified']): ?>
                                                    <span class="badge bg-success-subtle text-success rounded-pill px-2">Verified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Preferences Tab -->
                    <div class="tab-pane fade" id="preferences" role="tabpanel">
                        <h6 class="fw-bold mb-3" style="color: #000066;">System Preferences</h6>
                        <form action="/profile/currency" method="POST">
                            <?= \App\Core\Services\CsrfService::getTokenField() ?>

                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1" style="color:#000066;">Preferred Currency</label>
                                <p class="text-muted small mb-2">Used for transactions and pricing.</p>
                                <select name="currency" class="form-select form-select-sm">
                                    <?php 
                                    $currencies = ['USD', 'NGN', 'GHS', 'KES', 'ZAR', 'UGX', 'TZS', 'RWF', 'XOF', 'XAF', 'GBP', 'EUR'];
                                    $current = $user['currency'] ?? 'USD';
                                    foreach($currencies as $c): 
                                    ?>
                                        <option value="<?= $c ?>" <?= $current === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold px-3" style="background:#000066;border:none;">Save</button>
                        </form>
                    </div>

                    <!-- Identity Tab -->
                    <div class="tab-pane fade" id="kyc" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0" style="color: #000066;">Identity Verification</h6>
                            <?php 
                                $kyc_status = $user['kyc_status'] ?? 'unverified';
                                $badge_class = 'bg-secondary'; $status_text = 'Unverified';
                                if($kyc_status == 'pending') { $badge_class = 'bg-warning text-dark'; $status_text = 'Under Review'; }
                                if($kyc_status == 'verified') { $badge_class = 'bg-success'; $status_text = 'Verified'; }
                                if($kyc_status == 'rejected') { $badge_class = 'bg-danger'; $status_text = 'Rejected'; }
                            ?>
                            <span class="badge <?= $badge_class ?> rounded-pill px-2 py-1" style="font-size:0.75rem;"><?= $status_text ?></span>
                        </div>
                        <?php if($kyc_status == 'unverified' || $kyc_status == 'rejected'): ?>
                            <p class="text-muted small mb-3">Complete your identity verification to unlock virtual card creation and higher limits.</p>
                            <div class="alert alert-info border-0 py-2 px-3 mb-3" style="font-size:0.82rem; background:#e8f4fd;">
                                <ion-icon name="card-outline" class="me-1" style="vertical-align:middle;"></ion-icon>
                                <strong>Required for Virtual Cards:</strong> Your name, date of birth and ID details are securely passed to our card provider for instant cardholder approval.
                            </div>
                            <form action="/profile/kyc" method="POST" enctype="multipart/form-data">
                                <?= \App\Core\Services\CsrfService::getTokenField() ?>

                                <!-- Section 1: Personal Details -->
                                <p class="fw-bold small mb-2" style="color:#000066;">Personal Information</p>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control form-control-sm" placeholder="e.g. John" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control form-control-sm" placeholder="e.g. Doe" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="dob" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control form-control-sm" placeholder="e.g. +2348012345678">
                                    </div>
                                </div>

                                <!-- Section 2: Address Details (Required for NFC Cards) -->
                                <p class="fw-bold small mb-2 mt-3" style="color:#000066;">Residential Address</p>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Street Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address_line1" class="form-control form-control-sm" placeholder="e.g. 123 Main St" required>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">City <span class="text-danger">*</span></label>
                                        <input type="text" name="city" class="form-control form-control-sm" placeholder="e.g. Lagos" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">State/Province <span class="text-danger">*</span></label>
                                        <input type="text" name="state" class="form-control form-control-sm" placeholder="e.g. Lagos" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">Postal/Zip Code <span class="text-danger">*</span></label>
                                        <input type="text" name="postal_code" class="form-control form-control-sm" placeholder="e.g. 100001" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">Country (3-letter Code) <span class="text-danger">*</span></label>
                                        <input type="text" name="country" class="form-control form-control-sm" placeholder="e.g. NGA" minlength="3" maxlength="3" required style="text-transform: uppercase;">
                                    </div>
                                </div>

                                <!-- Section 3: ID Details -->
                                <p class="fw-bold small mb-2" style="color:#000066;">Identity Document</p>
                                <div class="mb-2">
                                    <label class="form-label small mb-1">Document Type</label>
                                    <select name="doc_type" id="kycDocType" class="form-select form-select-sm" onchange="document.getElementById('kycIdType').value=this.value">
                                        <option value="National ID">National ID Card</option>
                                        <option value="International Passport">Int'l Passport</option>
                                        <option value="Drivers License">Driver's License</option>
                                    </select>
                                    <input type="hidden" name="id_type" id="kycIdType" value="National ID">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small mb-1">ID Number <span class="text-danger">*</span></label>
                                    <input type="text" name="id_number" class="form-control form-control-sm" placeholder="Enter your document ID number" minlength="5" required>
                                    <div class="form-text" style="font-size:0.78rem;">Passport number, NIN, Driver's License number, etc.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small mb-1">BVN (Bank Verification Number) <span class="text-danger">*</span></label>
                                    <input type="text" name="bvn" class="form-control form-control-sm" placeholder="Enter your 11-digit BVN" minlength="11" maxlength="11" required>
                                    <div class="form-text" style="font-size:0.78rem;">Required for creating accounts and virtual cards.</div>
                                </div>

                                <!-- Section 3: Document Uploads -->
                                <p class="fw-bold small mb-2" style="color:#000066;">Document Photos</p>
                                <div class="row g-2 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">ID Document Photo <span class="text-danger">*</span></label>
                                        <input type="file" name="document" class="form-control form-control-sm" accept="image/*,.pdf" required>
                                        <div class="form-text" style="font-size:0.78rem;">Clear photo of your ID</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small mb-1">Selfie Photo <span class="text-danger">*</span></label>
                                        <input type="file" name="selfie" class="form-control form-control-sm" accept="image/*" capture="user" required>
                                        <div class="form-text" style="font-size:0.78rem;">Clear selfie holding your ID</div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-dark fw-bold px-3">
                                    <ion-icon name="shield-checkmark-outline" class="me-1" style="vertical-align:middle;"></ion-icon>
                                    Submit Verification
                                </button>
                            </form>
                        <?php elseif($kyc_status == 'pending'): ?>
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm text-warning mb-2" role="status"></div>
                                <p class="fw-bold small mb-1">Under Review</p>
                                <p class="text-muted small mb-0">Usually 24-48 hours.</p>
                            </div>
                        <?php else: ?>
                            <?php if(empty($kyc['id_number']) || empty($kyc['first_name'])): ?>
                                <div class="alert alert-warning border-0 py-2 px-3 mb-3" style="font-size:0.82rem; background:#fff3cd;">
                                    <ion-icon name="warning-outline" class="me-1" style="vertical-align:middle;"></ion-icon>
                                    <strong>Missing Identity Details:</strong> Your verification is approved, but missing new required cardholder fields. Please update them below to create virtual cards.
                                </div>
                                <form action="/profile/kyc/update-details" method="POST">
                                    <?= \App\Core\Services\CsrfService::getTokenField() ?>

                                    <!-- Section 1: Personal Details -->
                                    <p class="fw-bold small mb-2" style="color:#000066;">Personal Information</p>
                                    <div class="row g-2 mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control form-control-sm" placeholder="e.g. John" value="<?= htmlspecialchars($kyc['first_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control form-control-sm" placeholder="e.g. Doe" value="<?= htmlspecialchars($kyc['last_name'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" name="dob" class="form-control form-control-sm" value="<?= htmlspecialchars($kyc['dob'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">Phone Number</label>
                                            <input type="tel" name="phone" class="form-control form-control-sm" placeholder="e.g. +2348012345678" value="<?= htmlspecialchars($kyc['phone'] ?? $user['phone'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small mb-1">ID Number <span class="text-danger">*</span></label>
                                        <input type="text" name="id_number" class="form-control form-control-sm" placeholder="Enter your document ID number" value="<?= htmlspecialchars($kyc['id_number'] ?? '') ?>" minlength="5" required>
                                        <div class="form-text" style="font-size:0.78rem;">Passport number, NIN, Driver's License number, etc.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small mb-1">BVN (Bank Verification Number) <span class="text-danger">*</span></label>
                                        <input type="text" name="bvn" class="form-control form-control-sm" placeholder="Enter your 11-digit BVN" value="<?= htmlspecialchars($kyc['bvn'] ?? '') ?>" minlength="11" maxlength="11" required>
                                        <div class="form-text" style="font-size:0.78rem;">Required for creating accounts and virtual cards.</div>
                                    </div>

                                    <!-- Address Details -->
                                    <p class="fw-bold small mb-2 mt-3" style="color:#000066;">Residential Address</p>
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">Street Address <span class="text-danger">*</span></label>
                                        <input type="text" name="address_line1" class="form-control form-control-sm" placeholder="e.g. 123 Main St" value="<?= htmlspecialchars($kyc['address_line1'] ?? '') ?>" required>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">City <span class="text-danger">*</span></label>
                                            <input type="text" name="city" class="form-control form-control-sm" placeholder="e.g. Lagos" value="<?= htmlspecialchars($kyc['city'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">State/Province <span class="text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control form-control-sm" placeholder="e.g. Lagos" value="<?= htmlspecialchars($kyc['state'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">Postal/Zip Code <span class="text-danger">*</span></label>
                                            <input type="text" name="postal_code" class="form-control form-control-sm" placeholder="e.g. 100001" value="<?= htmlspecialchars($kyc['postal_code'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small mb-1">Country (3-letter Code) <span class="text-danger">*</span></label>
                                            <input type="text" name="country" class="form-control form-control-sm" placeholder="e.g. NGA" value="<?= htmlspecialchars($kyc['country'] ?? '') ?>" minlength="3" maxlength="3" required style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-dark fw-bold px-3">
                                        <ion-icon name="save-outline" class="me-1" style="vertical-align:middle;"></ion-icon>
                                        Update Details
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="text-center py-4 bg-success-subtle rounded">
                                    <ion-icon name="checkmark-done-circle" style="font-size:2.5rem;color:#198754;"></ion-icon>
                                    <p class="fw-bold text-success mt-2 mb-0 small">Verified ✓</p>
                                    <p class="text-muted small mt-1 mb-0">You can now create virtual cards.</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Password Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <h6 class="fw-bold mb-3" style="color: #000066;">Change Password</h6>
                        <form action="/profile/password" method="POST">
                            <?= \App\Core\Services\CsrfService::getTokenField() ?>

                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Current Password</label>
                                <input type="password" name="current_password" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">New Password</label>
                                <input type="password" name="new_password" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-warning fw-bold px-3">Update Password</button>
                        </form>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <h6 class="fw-bold mb-2" style="color: #000066;">Security Settings</h6>
                        <p class="text-muted small mb-3">Manage 2FA and login sessions.</p>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <ion-icon name="shield-checkmark-outline" style="font-size:1.5rem;color:#000066;" class="me-3"></ion-icon>
                                <div>
                                    <p class="fw-bold small mb-0">Security & Login</p>
                                    <p class="text-muted small mb-0">Sessions, 2FA, login history.</p>
                                </div>
                            </div>
                            <a href="/security" class="btn btn-sm btn-outline-dark fw-bold px-3">Manage</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function copyReferralLink() {
        var el = document.getElementById("referralLink");
        el.select(); el.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(el.value).then(function() {
            const btn = document.querySelector('button[onclick="copyReferralLink()"]');
            btn.innerText = "Copied!";
            btn.classList.replace('btn-dark','btn-success');
            setTimeout(() => { btn.innerText = "Copy"; btn.classList.replace('btn-success','btn-dark'); }, 2000);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const hash = window.location.hash;
        if (hash) {
            const tabEl = document.querySelector(`a[href="${hash}"]`);
            if (tabEl) new bootstrap.Tab(tabEl).show();
        }

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(link => {
            link.addEventListener('shown.bs.tab', e => { window.location.hash = e.target.getAttribute('href'); });
        });

        const p = new URLSearchParams(window.location.search);
        const s = p.get('success'), e = p.get('error');
        if (s === 'password_updated' || e === 'invalid_current_password' || e === 'password_mismatch')
            new bootstrap.Tab(document.querySelector('#password-tab')).show();
        else if (s === 'currency_updated')
            new bootstrap.Tab(document.querySelector('#preferences-tab')).show();
        else if (s === 'kyc_submitted' || e === 'no_document' || e === 'upload_failed' || e === 'already_exists')
            new bootstrap.Tab(document.querySelector('#kyc-tab')).show();
    });
</script>

<?php require __DIR__ . '/global_footer.php'; ?>
