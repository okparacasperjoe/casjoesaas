<?php

namespace App\Modules\CasjoeAcademy\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\SubscriptionManager;
use PDO;

class BusinessController
{
    private $pdo;
    private $tenantId;

    public function __construct() {
        $this->tenantId = TenantContext::getTenantId();
        // Note: Subscription check removed to allow access without active subscription
        // SubscriptionManager::requireActive($this->tenantId);
        $this->pdo = Database::getInstance()->getConnection();
    }

    // Dashboard: Overview of Team Learning
    public function dashboard() {
        $tenantId = $this->tenantId;
        
        // Stats
        $totalStaff = $this->pdo->query("SELECT COUNT(*) FROM users WHERE tenant_id = $tenantId")->fetchColumn();
        $totalLicenses = $this->pdo->query("SELECT SUM(seats_total) FROM academy_licenses WHERE tenant_id = $tenantId")->fetchColumn() ?? 0;
        $activeLearners = $this->pdo->query("SELECT COUNT(DISTINCT user_id) FROM academy_certificates WHERE tenant_id = $tenantId")->fetchColumn(); // Approx
        
        // Licenses Owned
        $licenses = $this->pdo->query("
            SELECT l.*, c.title, c.thumbnail 
            FROM academy_licenses l 
            JOIN academy_courses c ON l.course_id = c.id 
            WHERE l.tenant_id = $tenantId
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Team Leaderboard / Progress
        $team = $this->pdo->query("
            SELECT u.id, u.name, u.email,
                   COUNT(e.id) as active_courses,
                   IFNULL(AVG(e.progress_percent), 0) as avg_progress,
                   IFNULL(g.total_points, 0) as total_points
            FROM users u
            LEFT JOIN academy_enrollments e ON u.id = e.user_id
            LEFT JOIN academy_gamification_stats g ON u.id = g.user_id AND u.tenant_id = g.tenant_id
            WHERE u.tenant_id = $tenantId
            GROUP BY u.id
            ORDER BY total_points DESC, avg_progress DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Include GamificationService to determine ranks if needed
        require_once __DIR__ . '/../Services/GamificationService.php';

        require __DIR__ . '/../Views/business/dashboard.php';
    }

    // Marketplace: Browse System Courses to Buy
    public function marketplace() {
        // Check if is_system_course column exists
        $stmt = $this->pdo->query("DESCRIBE academy_courses");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $hasSystemFlag = in_array('is_system_course', $cols);

        if ($hasSystemFlag) {
            // Show system courses OR published courses owned by this tenant (or global ones if we want)
            // For now, let's just show ALL published courses to ensure visibility
            $courses = $this->pdo->query("SELECT * FROM academy_courses WHERE status = 'published'")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $courses = $this->pdo->query("SELECT * FROM academy_courses WHERE status = 'published'")->fetchAll(PDO::FETCH_ASSOC);
        }
        
        require __DIR__ . '/../Views/business/marketplace.php';
    }

    // Buy License (Simulation)
    public function buyLicense() {
        $courseId = $_POST['course_id'];
        $seats = intval($_POST['seats']);
        
        if ($seats < 1) die("Invalid seats");

        // Casjoe Pay Logic would go here (Check balance, deduct funds)
        
        // Create/Update License
        $stmt = $this->pdo->prepare("INSERT INTO academy_licenses (tenant_id, course_id, seats_total, seats_used) VALUES (?, ?, ?, 0) ON DUPLICATE KEY UPDATE seats_total = seats_total + ?");
        // Note: ON DUPLICATE KEY UPDATE on (tenant_id, course_id) requires a UNIQUE index which we didn't add yet, so let's just insert new row or separate logic.
        // Actually, schema didn't enforce unique tenant_id+course_id. Simple Insert is fine for now, or we can check existing.
        
        // Simple logic: Check existing license
        $stmt = $this->pdo->prepare("SELECT id FROM academy_licenses WHERE tenant_id = ? AND course_id = ?");
        $stmt->execute([$this->tenantId, $courseId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $this->pdo->prepare("UPDATE academy_licenses SET seats_total = seats_total + ? WHERE id = ?")->execute([$seats, $existing['id']]);
        } else {
             $this->pdo->prepare("INSERT INTO academy_licenses (tenant_id, course_id, seats_total, seats_used) VALUES (?, ?, ?, 0)")->execute([$this->tenantId, $courseId, $seats]);
        }

        header("Location: /academy/business");
    }

    // Assign Seat View
    public function assignView() {
        $licenseId = $_GET['license_id'];
        
        // Get License
        $stmt = $this->pdo->prepare("SELECT l.*, c.title FROM academy_licenses l JOIN academy_courses c ON l.course_id = c.id WHERE l.id = ? AND l.tenant_id = ?");
        $stmt->execute([$licenseId, $this->tenantId]);
        $license = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$license) die("License not found");

        // Get Staff not yet enrolled
        $stmt = $this->pdo->prepare("
            SELECT u.* 
            FROM users u 
            WHERE u.tenant_id = ? 
            AND u.id NOT IN (
                SELECT user_id FROM academy_enrollments WHERE course_id = ?
            )
        ");
        $stmt->execute([$this->tenantId, $license['course_id']]);
        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure Admin/Self is included if eligible
        $userId = $_SESSION['user_id'] ?? 0;
        $isEnrolled = false;
        foreach ($staff as $s) {
            if ($s['id'] == $userId) {
                $isEnrolled = true; // Actually, if they are in this list, they are NOT enrolled yet.
                break; 
            }
        }

        // If not in list, check if we need to add them (maybe they were filtered out by tenant check? unlikely, but good for safety)
        // If they are in the list, great. 
        // Wait, if they ARE in the list, $isEnrolled logic above is backwards. 
        // The list contains UN-enrolled people.
        
        // Let's explicitly check if current user is enrolled.
        $stmt = $this->pdo->prepare("SELECT id FROM academy_enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$userId, $license['course_id']]);
        $alreadyEnrolled = $stmt->fetch();

        if (!$alreadyEnrolled) {
             // Check if already in $staff
             $inList = false;
             foreach ($staff as $s) {
                 if ($s['id'] == $userId) $inList = true;
             }

             if (!$inList) {
                 // Fetch current user details
                 $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
                 $stmt->execute([$userId]);
                 $me = $stmt->fetch(PDO::FETCH_ASSOC);
                 if ($me) {
                     // Add "Me" to the top
                     array_unshift($staff, $me);
                 }
             }
        }

        // Diagnostics
        $debug = [
            'tenant_id' => $this->tenantId,
            'user_id' => $userId,
            'is_enrolled' => $alreadyEnrolled ? 'Yes' : 'No',
            'staff_count_raw' => count($staff),
            'total_users_in_tenant' => $this->pdo->query("SELECT COUNT(*) FROM users WHERE tenant_id = {$this->tenantId}")->fetchColumn()
        ];

        require __DIR__ . '/../Views/business/assign.php';
    }

    // Process Assignment
    public function assignStore() {
        $licenseId = $_POST['license_id'];
        $userId = $_POST['user_id'];

        // Verify License Seats
        $stmt = $this->pdo->prepare("SELECT * FROM academy_licenses WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$licenseId, $this->tenantId]);
        $license = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($license['seats_used'] >= $license['seats_total']) {
            die("No seats available. Purchase more.");
        }

        // Enroll User
        $this->pdo->prepare("INSERT INTO academy_enrollments (user_id, course_id, progress_percent) VALUES (?, ?, 0)")->execute([$userId, $license['course_id']]);
        
        // Update License Count
        $this->pdo->prepare("UPDATE academy_licenses SET seats_used = seats_used + 1 WHERE id = ?")->execute([$licenseId]);

        header("Location: /academy/business");
    }
}

