<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Services\SecurityLogger;

class ProfileController
{
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();

        // Fetch full user record to get referral_code
        try {
            $stmtUser = $db->query("SELECT * FROM users WHERE id = ?", [$user['id']]);
            $fullUser = $stmtUser->fetch();
            if ($fullUser) {
                $user = $fullUser;
            }
        } catch (\Exception $e) {}

        if (empty($user['referral_code'])) {
            $refCode = strtoupper(substr(md5($user['id'] . '-' . ($user['email'] ?? '') . '-' . time()), 0, 8));
            try {
                $db->query("UPDATE users SET referral_code = ? WHERE id = ?", [$refCode, $user['id']]);
                $user['referral_code'] = $refCode;
            } catch (\Exception $e) {}
        }

        // Check for success message
        $success = $_GET['success'] ?? null;

        // Fetch Referrals
        $stmt = $db->query("SELECT name, email, created_at, is_verified FROM users WHERE referred_by = ? ORDER BY created_at DESC", [$user['id']]);
        $referrals = $stmt->fetchAll();

        // Fetch Approved KYC record
        $kyc = null;
        try {
            $stmt = $db->query("SELECT * FROM kyc_verifications WHERE user_id = ? AND status = 'approved' ORDER BY id DESC LIMIT 1", [$user['id']]);
            $kyc = $stmt->fetch();
        } catch (\Exception $e) {}

        // Fetch Tenant / Business details
        $tenant = null;
        try {
            $stmtT = $db->query("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
            $tenant = $stmtT->fetch();
        } catch (\Exception $e) {}

        require __DIR__ . '/../Views/profile.php';
    }

    public function updateProfile()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $userId = Auth::user()['id'];
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);

        if (empty($name)) {
            header('Location: /profile?error=name_required');
            exit;
        }

        $avatarName = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = $_FILES['avatar']['name'];
            $fileSize = $_FILES['avatar']['size'];
            
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                if ($fileSize <= 2 * 1024 * 1024) { // 2MB Limit
                    $uploadDir = __DIR__ . '/../../../public/uploads/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Generate unique file name
                    $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
                    $destPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $avatarName = $newFileName;
                    }
                } else {
                    header('Location: /profile?error=avatar_too_large');
                    exit;
                }
            } else {
                header('Location: /profile?error=invalid_avatar_type');
                exit;
            }
        }

        $db = Database::getInstance();
        if ($avatarName) {
            // Delete old avatar if exists
            try {
                $stmtOld = $db->query("SELECT avatar FROM users WHERE id = ?", [$userId]);
                $oldAvatar = $stmtOld->fetchColumn();
                if ($oldAvatar && file_exists(__DIR__ . '/../../../public/uploads/avatars/' . $oldAvatar)) {
                    @unlink(__DIR__ . '/../../../public/uploads/avatars/' . $oldAvatar);
                }
            } catch (\Exception $e) {}

            $db->query("UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?", [$name, $phone, $avatarName, $userId]);
            $_SESSION['avatar'] = $avatarName;
        } else {
            $db->query("UPDATE users SET name = ?, phone = ? WHERE id = ?", [$name, $phone, $userId]);
        }

        // Update Session if name or phone changed
        $_SESSION['name'] = $name;
        $_SESSION['user_phone'] = $phone;

        SecurityLogger::info('User Update', "User updated profile details (Name: {$name}, Phone: {$phone}).");

        header('Location: /profile?success=profile_updated');
        exit;
    }

    public function updateCurrency()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $userId = Auth::user()['id'];

        $currency = $_POST['currency'] ?? 'USD';
        
        // Validate Currency
        $allowed = ['USD', 'NGN', 'GHS', 'KES', 'ZAR', 'UGX', 'TZS', 'RWF', 'XOF', 'XAF', 'GBP', 'EUR'];
        if (!in_array($currency, $allowed)) {
            $currency = 'USD';
        }

        SecurityLogger::info('User Update', "User updated currency preference to {$currency}.");

        $db = Database::getInstance();
        $db->query("UPDATE users SET currency = ? WHERE id = ?", [$currency, $userId]);
        
        // Update Session Immediately
        $_SESSION['currency'] = $currency;

        header('Location: /profile?success=currency_updated');
        exit;
    }

    public function updatePassword()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $userId = Auth::user()['id'];

        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        $db = Database::getInstance();
        $stmt = $db->query("SELECT password FROM users WHERE id = ?", [$userId]);
        $storedHash = $stmt->fetchColumn();

        // 1. Verify Current
        if (!password_verify($current, $storedHash)) {
            SecurityLogger::warning('Password Change Failed', "Incorrect current password attempt for user.");
            header('Location: /profile?error=invalid_current_password');
            exit;
        }

        // 2. Verify Match
        if ($new !== $confirm) {
            SecurityLogger::info('Password Change Mismatch', "Password confirmation mismatch during update.");
            header('Location: /profile?error=password_mismatch');
            exit;
        }

        // 3. Update
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password = ? WHERE id = ?", [$newHash, $userId]);
        
        SecurityLogger::info('Password Changed', "User successfully changed their password.");

        header('Location: /profile?success=password_updated');
        exit;
    }

    public function submitKyc()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $user = Auth::user();

        $userId = $user['id'];
        $tenantId = TenantContext::getTenantId();
        $docType = $_POST['doc_type'] ?? 'ID Card';

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $dob       = trim($_POST['dob']        ?? '');
        $phone     = trim($_POST['phone']      ?? '');
        $idNumber  = trim($_POST['id_number']  ?? '');
        $bvn       = trim($_POST['bvn']        ?? '');
        $idType    = trim($_POST['id_type']    ?? $docType);

        // Address fields (NFC)
        $line1      = trim($_POST['address_line1'] ?? '');
        $city       = trim($_POST['city'] ?? '');
        $state      = trim($_POST['state'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        $country    = strtoupper(trim($_POST['country'] ?? ''));

        // Basic validation of required structured fields
        if (empty($firstName) || empty($lastName) || empty($dob) || empty($idNumber)) {
            header('Location: /profile?error=kyc_fields_required');
            exit;
        }

        if (empty($_FILES['document']['name'])) {
            header('Location: /profile?error=no_document');
            exit;
        }

        $db = Database::getInstance();
        
        // Check if already pending/approved
        $stmt = $db->query("SELECT status FROM kyc_verifications WHERE user_id = ? AND status IN ('pending', 'approved')", [$userId]);
        if ($stmt->fetch()) {
            header('Location: /profile?error=already_exists');
            exit;
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/kyc/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Upload ID Document
        $ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
        $filename = 'kyc_' . $userId . '_' . time() . '.' . $ext;
        $target = $uploadDir . $filename;
        $docPath = null;
        if (move_uploaded_file($_FILES['document']['tmp_name'], $target)) {
            $docPath = '/uploads/kyc/' . $filename;
        }

        // Upload Selfie
        $selfiePath = null;
        if (!empty($_FILES['selfie']['name'])) {
            $sExt = pathinfo($_FILES['selfie']['name'], PATHINFO_EXTENSION);
            $sFilename = 'selfie_' . $userId . '_' . time() . '.' . $sExt;
            $sTarget = $uploadDir . $sFilename;
            if (move_uploaded_file($_FILES['selfie']['tmp_name'], $sTarget)) {
                $selfiePath = '/uploads/kyc/' . $sFilename;
            }
        }

        if ($docPath) {
            SecurityLogger::info('KYC Submitted', "User submitted KYC documents (Type: {$docType}, Name: {$firstName} {$lastName}).");

            // Try to save with new structured columns; fall back gracefully if migration not yet run
            try {
                $db->query(
                    "INSERT INTO kyc_verifications (tenant_id, user_id, doc_type, doc_path, selfie_path, status, first_name, last_name, dob, phone, id_number, id_type, bvn, address_line1, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [$tenantId, $userId, $docType, $docPath, $selfiePath, $firstName, $lastName, $dob, $phone, $idNumber, $idType, $bvn, $line1, $city, $state, $postalCode, $country]
                );
            } catch (\Exception $e) {
                // Structured columns may not exist yet — fall back to basic insert
                error_log("KYC structured insert failed, falling back: " . $e->getMessage());
                $db->query(
                    "INSERT INTO kyc_verifications (tenant_id, user_id, doc_type, doc_path, selfie_path, status) VALUES (?, ?, ?, ?, ?, 'pending')",
                    [$tenantId, $userId, $docType, $docPath, $selfiePath]
                );
            }
            
            $db->query("UPDATE users SET kyc_status = 'pending' WHERE id = ?", [$userId]);

            // Send email notification
            $subject = "KYC Verification Received";
            $message = "
                <h2>Hello {$user['name']},</h2>
                <p>We have successfully received your KYC verification documents!</p>
                <p><strong>Submission Details:</strong></p>
                <ul>
                    <li>Document Type: {$docType}</li>
                    <li>Name: {$firstName} {$lastName}</li>
                    <li>Status: <span style='color: #f59e0b; font-weight: bold;'>Pending Review</span></li>
                    <li>Submitted: " . date('F j, Y g:i A') . "</li>
                </ul>
                <p>Our team will review your documents within 24-48 hours. You will receive another email once your verification has been processed.</p>
                <p style='color: #64748b; font-size: 0.9rem;'>Thank you for verifying your identity with Casjoe!</p>
            ";
            
            try {
                \App\Core\Mailer::send($user['email'], $subject, $message);
            } catch (\Exception $e) {
                error_log("Failed to send KYC submission email: " . $e->getMessage());
            }

            header('Location: /profile?success=kyc_submitted');
        } else {
            header('Location: /profile?error=upload_failed');
        }
        exit;
    }

    public function updateKycDetails()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             header("Location: /profile"); exit;
        }

        if (!\App\Core\Services\CsrfService::verifyToken($_POST['csrf_token'] ?? '')) {
            die("CSRF Token Verification Failed");
        }

        $user = Auth::user();

        $userId = $user['id'];

        // Only allow if user is already verified
        $userKycStatus = $user['kyc_status'] ?? 'unverified';
        if ($userKycStatus !== 'verified') {
            header('Location: /profile?error=not_verified_for_update');
            exit;
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $dob       = trim($_POST['dob']        ?? '');
        $phone     = trim($_POST['phone']      ?? '');
        $idNumber  = trim($_POST['id_number']  ?? '');
        $bvn       = trim($_POST['bvn']        ?? '');
        
        $line1      = trim($_POST['address_line1'] ?? '');
        $city       = trim($_POST['city'] ?? '');
        $state      = trim($_POST['state'] ?? '');
        $postalCode = trim($_POST['postal_code'] ?? '');
        $country    = strtoupper(trim($_POST['country'] ?? ''));

        if (empty($firstName) || empty($lastName) || empty($dob) || empty($idNumber)) {
            header('Location: /profile?error=kyc_fields_required');
            exit;
        }

        $db = Database::getInstance();

        // Update the approved KYC record for this user
        try {
            $stmt = $db->query(
                "UPDATE kyc_verifications SET first_name = ?, last_name = ?, dob = ?, phone = ?, id_number = ?, bvn = ?, address_line1 = ?, city = ?, state = ?, postal_code = ?, country = ? WHERE user_id = ? AND status = 'approved' ORDER BY id DESC LIMIT 1",
                [$firstName, $lastName, $dob, $phone, $idNumber, $bvn, $line1, $city, $state, $postalCode, $country, $userId]
            );
            
            SecurityLogger::info('KYC Details Updated', "User updated their KYC structured details.");
            header('Location: /profile?success=kyc_details_updated');
        } catch (\Exception $e) {
            error_log("Failed to update KYC details: " . $e->getMessage());
            header('Location: /profile?error=db_error');
        }
        exit;
    }
}
