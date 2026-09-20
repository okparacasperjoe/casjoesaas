<?php

namespace App\Core;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Mailer;
use App\Core\Services\SecurityLogger;

class Auth
{

    public static function login($email, $password)
    {
        $db = Database::getInstance();
        $tenantId = TenantContext::getTenantId();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 0. Check if IP address is currently banned in blocked_ips
        try {
            $stmtBlock = $db->query("SELECT reason FROM blocked_ips WHERE ip_address = ?", [$ip]);
            if ($stmtBlock && ($blockRow = $stmtBlock->fetch())) {
                SecurityLogger::danger('security.blocked_login_attempt', "Blocked IP attempted login for: $email");
                return ['error' => 'Access denied: Your IP address is blocked across the platform due to repeated security violations.'];
            }
        } catch (\Exception $e) {
            // Ignore if blocked_ips table does not exist yet
        }

        // 1. Try Current Tenant Context
        $stmt = $db->query("SELECT * FROM users WHERE email = ? AND tenant_id = ?", [$email, $tenantId]);
        $user = $stmt->fetch();

        // 2. If User Not Found, perform Global Lookup (Casjoe SaaS user emails are globally unique)
        if (!$user) {
            $stmt = $db->query("SELECT * FROM users WHERE email = ?", [$email]);
            $user = $stmt->fetch();
        }

        if ($user && password_verify($password, $user['password'])) {
            // Check Email Verification
            if (!$user['is_verified']) {
                SecurityLogger::warning('auth.login_failed', "Unverified email login attempt: $email", $user['id']);
                return ['error' => 'Email not verified. Please verify your email to continue.', 'unverified_email' => $email];
            }

            // Check 2FA
            if ($user['two_factor_enabled']) {
                $_SESSION['2fa_pending_user_id'] = $user['id'];
                return ['require_2fa' => true];
            }

            // Success
            self::setSession($user);
            
            // Single Session Enforcement: Update DB with current session ID
            $db->query("UPDATE users SET current_session_id = ?, last_login = NOW(), retention_email_sent = NULL WHERE id = ?", [session_id(), $user['id']]);

            SecurityLogger::info('auth.login_success', "User logged in: $email", $user['id']);
            return true;
        }

        // Failure handling & Brute-Force Rate Limiting
        SecurityLogger::warning('auth.login_failed', "Invalid credentials for: $email");

        try {
            // Check how many failed login attempts originated from this IP in the last 15 minutes
            $stmtFailCount = $db->query("SELECT COUNT(*) FROM security_logs WHERE ip_address = ? AND event_type = 'auth.login_failed' AND created_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)", [$ip]);
            $failedCount = (int) ($stmtFailCount ? $stmtFailCount->fetchColumn() : 0);

            if ($failedCount >= 5) {
                // Auto-block the IP address across the platform
                $db->query("INSERT IGNORE INTO blocked_ips (ip_address, reason) VALUES (?, 'Auto-blocked by SOC Firewall: 5 failed login attempts within 15 minutes')", [$ip]);
                SecurityLogger::danger('security.auto_blocked', "IP auto-blocked by SOC firewall after 5 consecutive failed logins for: $email");
                return ['error' => 'Access denied: Your IP address has been automatically blocked by the SOC firewall due to repeated failed login attempts.'];
            }
        } catch (\Exception $e) {
            // Failsafe
        }

        return ['error' => 'Invalid credentials'];
    }

    public static function setSession($user)
    {
        session_regenerate_id(true); // Prevent Session Fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['tenant_id'] = $user['tenant_id']; // This enables Virtual Switching via TenantContext
        $_SESSION['user_name'] = $user['name'] ?? 'User';
        $_SESSION['user_email'] = $user['email'] ?? '';
        $_SESSION['user_phone'] = $user['phone'] ?? '';
        $_SESSION['currency'] = $user['currency'] ?? 'USD';
        $_SESSION['referral_code'] = $user['referral_code'] ?? '';
    }

    public static function user()
    {
        if (!isset($_SESSION['user_id']))
            return null;

        // Re-validate against DB to catch deleted users or role changes
        // This adds a DB query per request, but essential for security in this context.
        // Optimization: Could use a shorter-lived cache or verify only critical actions, 
        // but for this specific "deleted user" issue, we must check.
        
        $db = Database::getInstance();
        $user = null;
        try {
            $stmt = $db->query("SELECT id, role, tenant_id, email, password, name, phone, currency, referral_code, kyc_status, current_session_id, location_id, avatar, accessible_modules FROM users WHERE id = ?", [$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch (\PDOException $e) {
            try {
                $stmt = $db->query("SELECT id, role, tenant_id, email, password, name, phone, currency, referral_code, kyc_status, current_session_id, location_id, avatar FROM users WHERE id = ?", [$_SESSION['user_id']]);
                $user = $stmt->fetch();
            } catch (\PDOException $e2) {
                $stmt = $db->query("SELECT id, role, tenant_id, email, password, name, phone, currency, referral_code, kyc_status, current_session_id, location_id FROM users WHERE id = ?", [$_SESSION['user_id']]);
                $user = $stmt->fetch();
            }
        }

        if (!$user) {
            // User deleted
            self::logout();
            return null;
        }

        // Check for Single Session Validity
        // BYPASS if impersonating (Admins shouldn't be kicked out by the target user's active sessions)
        $isImpersonating = isset($_SESSION['impersonator_id']);
        
        if (!$isImpersonating && isset($user['current_session_id']) && $user['current_session_id'] !== session_id()) {
            SecurityLogger::warning('auth.session_terminated', "Concurrent login detected. Terminating session.", $_SESSION['user_id']);
            self::logout();
            return null; // Will trigger login redirect in UI or controller
        }

        // Check if user's IP is currently banned in blocked_ips
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stmtBlock = $db->query("SELECT reason FROM blocked_ips WHERE ip_address = ?", [$ip]);
            if ($stmtBlock && $stmtBlock->fetch()) {
                SecurityLogger::danger('security.blocked_session_terminated', "Terminated active session for banned IP: $ip", $_SESSION['user_id']);
                self::logout();
                return null;
            }
        } catch (\Exception $e) {
            // Ignore if blocked_ips table does not exist yet
        }

        // Optional: specific check for Casper (or any deleted user logic if soft deletes used, but schema said DELETE CASCADE?)
        // Schema: users table doesn't have deleted_at. So row is gone. Above check works.

        // Update Session Data if needed (roles etc) - keeping it simple
        return [
            'id' => $_SESSION['user_id'],
            'role' => $user['role'], // Ensure role is current
            'tenant_id' => $_SESSION['tenant_id'], // Keep current context
            'name' => $user['name'] ?? $_SESSION['user_name'] ?? 'User',
            'email' => $user['email'],
            'phone' => $user['phone'] ?? $_SESSION['user_phone'] ?? '',
            'currency' => $user['currency'] ?? $_SESSION['currency'] ?? 'USD',
            'referral_code' => $user['referral_code'] ?? '',
            'kyc_status' => $user['kyc_status'] ?? 'unverified',
            'accessible_modules' => $user['accessible_modules'] ?? null
        ];
    }

    public static function isAdmin()
    {
        $user = self::user();
        return $user && in_array($user['role'], ['admin', 'super_admin']);
    }

    public static function isSuperAdmin()
    {
        $user = self::user();
        if (!$user) return false;

        $email = strtolower(trim($user['email'] ?? ''));

        // Primary platform Super Admin account
        if ($email === 'admin2@casjoe.com') {
            return true;
        }

        // Must have role super_admin AND belong to master platform tenant (tenant 1)
        if (($user['role'] ?? '') === 'super_admin' && (int)($user['tenant_id'] ?? 0) === 1) {
            return true;
        }

        return false;
    }

    public static function isModerator()
    {
        $user = self::user();
        return $user && ($user['role'] ?? '') === 'moderator';
    }

    public static function denySuperAdminAccess($resource = 'Super Admin Control Center')
    {
        header('HTTP/1.1 403 Forbidden');
        $user = self::user();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $uri = $_SERVER['REQUEST_URI'] ?? '/casper-joe';
        
        // Log unauthorized attempt to SOC security logs
        try {
            $userDesc = $user ? "User #{$user['id']} ({$user['email']})" : "Unauthenticated Session";
            $logMsg = "Unauthorized access attempt to Super Admin resource: '$resource' (URI: $uri) by $userDesc from IP: $ip";
            \App\Core\Services\SecurityLogger::danger('security.unauthorized_admin_access', $logMsg, $user['id'] ?? null);
        } catch (\Exception $e) {
            // Ignore logging failsafe
        }

        $incidentCode = 'SEC-' . strtoupper(substr(md5($ip . $uri . time()), 0, 8));
        require __DIR__ . '/Views/errors/403.php';
        exit;
    }

    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function logout()
    {
        session_destroy();
    }

    public static function register($email, $password, $name = null, $phone = null, $businessName = null, $referredByCode = null, $isVerified = false, $currency = 'NGN')
    {
        $db = Database::getInstance();
        
        
        // Check if email already exists globally
        $stmt = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
        if ($stmt->fetch()) {
            return ['error' => 'Email address already registered.'];
        }

        // Create New Tenant for this User
        // Use business name if provided, else name, else 'My Business'
        $tenantName = $businessName ?: ($name ? "$name's Business" : "My Business");
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tenantName), '-'));
        
        // Ensure unique slug (simple check)
        $stmt = $db->query("SELECT id FROM tenants WHERE subdomain = ?", [$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . uniqid();
        }

        try {
            // Insert Tenant
            // Default Onboarding Step 1
            $db->query(
                "INSERT INTO tenants (name, subdomain, domain, status, onboarding_step, currency) VALUES (?, ?, NULL, 'active', 1, ?)", 
                [$tenantName, $slug, $currency]
            );
            $newTenantId = $db->getConnection()->lastInsertId();

            // Enable Default Modules (Casjoe Pay & Casjoe Business School)
            $defaultSlugs = ['casjoe-pay', 'casjoe-academy'];
            $placeholders = implode(',', array_fill(0, count($defaultSlugs), '?'));
            
            $stmt = $db->query("SELECT id FROM modules WHERE slug IN ($placeholders)", $defaultSlugs);
            $moduleIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($moduleIds as $mid) {
                $db->query("INSERT INTO tenant_modules (tenant_id, module_id, status) VALUES (?, ?, 'enabled')", [$newTenantId, $mid]);
            }

            // Create Free Trial Subscription for the new tenant
            $trialEnd = date('Y-m-d H:i:s', strtotime('+30 days'));
            $db->query(
                "INSERT INTO subscriptions (tenant_id, status, plan, trial_ends_at) VALUES (?, 'trial', 'free', ?)",
                [$newTenantId, $trialEnd]
            );

            // Seed Default CRM Stages (Fix for Funnels)
            $defaultStages = [
                ['name' => 'Lead In', 'color' => '#3498db'],
                ['name' => 'Qualified', 'color' => '#2ecc71'],
                ['name' => 'Proposal', 'color' => '#f1c40f'],
                ['name' => 'Negotiation', 'color' => '#e67e22'],
                ['name' => 'Won', 'color' => '#27ae60'],
                ['name' => 'Lost', 'color' => '#e74c3c']
            ];
            
            $stageSortOrder = 1;
            // Removed pipeline_id as per server schema
            $stageInsertStmt = $db->prepare("INSERT INTO erp_crm_stages (tenant_id, name, color, sort_order) VALUES (?, ?, ?, ?)");
            
            foreach ($defaultStages as $stage) {
                // Using newTenantId which is in scope
                $stageInsertStmt->execute([$newTenantId, $stage['name'], $stage['color'], $stageSortOrder++]);
            }

        } catch (\PDOException $e) {
             return ['error' => 'Failed to create organization: ' . $e->getMessage()];
        }


        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));
        $referralCode = strtoupper(substr(md5(uniqid($email, true)), 0, 8)); // Generate unique code
        $referredBy = null;

        // Resolve Referral Code (Global lookup? Or in context? Referrals might be global)
        // Assuming referral code is unique across system
        if ($referredByCode) {
            $stmt = $db->query("SELECT id FROM users WHERE referral_code = ?", [$referredByCode]);
            $referrer = $stmt->fetch();
            if ($referrer) {
                $referredBy = $referrer['id'];
            }
        }

        try {
            $isVerifiedInt = $isVerified ? 1 : 0;
            $db->query(
                "INSERT INTO users (tenant_id, email, password, verification_token, name, phone, business_name, referral_code, referred_by, role, is_verified, currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin', ?, ?)",
                [$newTenantId, $email, $hash, $token, $name, $phone, $businessName, $referralCode, $referredBy, $isVerifiedInt, $currency]
            );

            // Send Verification Email ONLY if not already verified
            if (!$isVerified) {
                $baseUrl = getenv('APP_URL') ?: ("http://" . $_SERVER['HTTP_HOST']);
                $link = rtrim($baseUrl, '/') . "/verify?token=$token";

                Mailer::sendWithTemplate($email, "Welcome to Casjoe - Verify Your Email", 'verify_email', [
                    'name' => $name,
                    'verifyLink' => $link,
                    'subtitle' => 'Thanks for signing up! Please verify your email address to get started managing your business with Casjoe.',
                    'title' => 'Verify Your Email'
                ]);
            } else {
                // If verified automatically (OAuth), send Welcome Email immediately
                // We need the user ID. We inserted it.
                $newUserId = $db->getConnection()->lastInsertId();
                // But users table might not be last insert if we did other queries?
                // Actually lastInsertId works for the last INSERT statement on the connection.
                // The last insert was into `users` table just above.
                
                $userForWelcome = [
                    'id' => $newUserId,
                    'name' => $name,
                    'email' => $email,
                    'referral_code' => $referralCode
                ];
                
                self::sendWelcomeEmail($userForWelcome);
            }

            // Return referrer ID for potential rewards logic in controller
            return ['success' => true, 'referred_by' => $referredBy]; 
        } catch (\PDOException $e) {
            // Check for duplicate entry
            if ($e->getCode() == 23000) {
                return ['error' => 'Email address already registered.'];
            }
            return ['error' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public static function verifyEmail($token)
    {
        $db = Database::getInstance();
        
        // Lookup user by token globally (token is unique)
        $stmt = $db->query("SELECT id, tenant_id, email, name, referral_code FROM users WHERE verification_token = ?", [$token]);
        $user = $stmt->fetch();

        if ($user) {
            $db->query("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?", [$user['id']]);
            
            // Send Welcome Email
            self::sendWelcomeEmail($user);

            // Fetch the full user object to return for auto-login
            $stmt = $db->query("SELECT * FROM users WHERE id = ?", [$user['id']]);
            return $stmt->fetch();
        }
        return false;
    }

    public static function sendWelcomeEmail($user)
    {
        // $user array must contain: id, name, email, referral_code
        if (!$user || empty($user['email'])) return;

        $name = !empty($user['name']) ? $user['name'] : 'there';
        $email = $user['email'];
        $userId = $user['id'];
        // $referralCode = $user['referral_code']; // Used if we want to show it explicitly, but tracking link uses user ID to look it up or passed param

        $appUrl = getenv('APP_URL') ?: ("http://" . $_SERVER['HTTP_HOST']);
        $academyLink = $appUrl . "/track/welcome?type=academy&u=" . $userId;
        $referralLink = $appUrl . "/track/welcome?type=referral&u=" . $userId;

        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; line-height: 1.6; color: #333;'>
            <p>Hello <strong>$name</strong>,</p>

            <p>Welcome to <strong>Casjoe</strong>.</p>

            <p>You’re now inside a complete business ecosystem designed to help you <strong>run your business, sell products, get paid, and scale</strong> — all from one platform.</p>

            <h3 style='color: #000066; border-bottom: 2px solid #FFA600; display: inline-block; padding-bottom: 5px; margin-top: 30px;'>Start with Casjoe Business School</h3>

            <p>Your first smart move is to go through the <strong>Casjoe Mastery Course</strong> inside Casjoe Business School.<br>
            This course shows you how to:</p>
            <ul>
                <li>Use Casjoe the right way (not trial-and-error)</li>
                <li>Set up your business systems properly</li>
                <li>Activate the modules that actually matter for your goals</li>
                <li>Avoid common mistakes that slow businesses down</li>
            </ul>

            <p>👉 <strong>Access Casjoe Business School:</strong> <a href='$academyLink' style='color: #000066; font-weight: bold;'>$academyLink</a></p>

            <h3 style='color: #000066; border-bottom: 2px solid #FFA600; display: inline-block; padding-bottom: 5px; margin-top: 30px;'>Earn by Referring Others</h3>

            <p>Casjoe rewards growth.</p>

            <p>You have a personal referral link. When people sign up using your link and start using Casjoe, <strong>you earn rewards</strong>.</p>

            <p>👉 <strong>Your referral link:</strong> <a href='$referralLink' style='color: #000066; font-weight: bold;'>$referralLink</a></p>

            <p>Share it with:</p>
            <ul>
                <li>Business owners</li>
                <li>Vendors</li>
                <li>Teams</li>
                <li>Anyone who wants to build or grow a business</li>
            </ul>

            <h3 style='color: #000066; border-bottom: 2px solid #FFA600; display: inline-block; padding-bottom: 5px; margin-top: 30px;'>We’re Here to Help</h3>

            <p>You’re not alone on this platform.</p>

            <p>Our team is active and available to help you:</p>
            <ul>
                <li>Understand the ecosystem</li>
                <li>Set up your business correctly</li>
                <li>Get the most value from Casjoe</li>
            </ul>

            <p>If you ever feel stuck, reach out through our support channels inside the app.</p>

            <p>Build smart. Learn fast. Grow with systems.</p>

            <p>Welcome to Casjoe.</p>

            <p>—<br>
            <strong>The Casjoe Team</strong><br>
            Business, Connected.</p>
        </div>";

        try {
            Mailer::sendWithTemplate($email, "Welcome to Casjoe! 🎉", 'welcome', [
                'name' => $name,
                'dashboardUrl' => (getenv('APP_URL') ?: ('https://' . $_SERVER['HTTP_HOST'])) . '/dashboard',
                'title' => 'Welcome to Casjoe'
            ]);
        } catch (\Exception $e) {
            // Log but don't break flow
            error_log("Welcome Email Failed: " . $e->getMessage());
        }
    }
}
