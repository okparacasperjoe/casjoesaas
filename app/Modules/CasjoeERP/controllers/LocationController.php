<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Auth;
use PDO;

class LocationController
{
    private $pdo;
    private $tenantId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        $this->requireAdmin();
    }

    /** Only admins can manage locations */
    private function requireAdmin()
    {
        $role = Auth::user()['role'] ?? '';
        if ($role !== 'admin') {
            header('Location: /erp/dashboard');
            exit;
        }
    }

    /** List all locations for this tenant + show staff assignment */
    public function index()
    {
        $stmt = $this->pdo->prepare("
            SELECT l.*,
                COUNT(u.id) AS staff_count
            FROM erp_locations l
            LEFT JOIN users u ON u.location_id = l.id AND u.tenant_id = l.tenant_id
            WHERE l.tenant_id = ?
            GROUP BY l.id
            ORDER BY l.name ASC
        ");
        $stmt->execute([$this->tenantId]);
        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all staff (non-admin users) with their current location assignment
        $stmtStaff = $this->pdo->prepare("
            SELECT u.id, u.name, u.email, u.role, u.location_id, l.name AS location_name
            FROM users u
            LEFT JOIN erp_locations l ON l.id = u.location_id
            WHERE u.tenant_id = ? AND u.role != 'admin'
            ORDER BY u.name ASC
        ");
        $stmtStaff->execute([$this->tenantId]);
        $staff = $stmtStaff->fetchAll(PDO::FETCH_ASSOC);

        $isAdmin = true;
        require __DIR__ . '/../Views/locations/index.php';
    }

    public function create()
    {
        $isAdmin = true;
        require __DIR__ . '/../Views/locations/create.php';
    }

    public function store()
    {
        $name    = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');

        if (!$name) {
            header('Location: /erp/locations/create?error=name_required');
            exit;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO erp_locations (tenant_id, name, address, phone, status)
            VALUES (?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$this->tenantId, $name, $address, $phone]);

        header('Location: /erp/locations?success=created');
        exit;
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: /erp/locations'); exit; }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_locations WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);
        $location = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$location) die("Location not found.");

        $isAdmin = true;
        require __DIR__ . '/../Views/locations/edit.php';
    }

    public function update()
    {
        $id      = $_POST['id'] ?? null;
        $name    = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $status  = $_POST['status'] ?? 'active';

        if (!$id || !$name) {
            header('Location: /erp/locations');
            exit;
        }

        $stmt = $this->pdo->prepare("
            UPDATE erp_locations SET name=?, address=?, phone=?, status=?
            WHERE id=? AND tenant_id=?
        ");
        $stmt->execute([$name, $address, $phone, $status, $id, $this->tenantId]);

        header('Location: /erp/locations?success=updated');
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { header('Location: /erp/locations'); exit; }

        // Unassign staff from this location before deleting
        $stmt = $this->pdo->prepare("UPDATE users SET location_id = NULL WHERE location_id = ? AND tenant_id = ?");
        $stmt->execute([$id, $this->tenantId]);

        // Unassign inventory from this location (set to NULL, not delete)
        $stmt2 = $this->pdo->prepare("UPDATE erp_inventory_items SET location_id = NULL WHERE location_id = ? AND tenant_id = ?");
        $stmt2->execute([$id, $this->tenantId]);

        $stmt3 = $this->pdo->prepare("DELETE FROM erp_locations WHERE id = ? AND tenant_id = ?");
        $stmt3->execute([$id, $this->tenantId]);

        header('Location: /erp/locations?success=deleted');
        exit;
    }

    /** Assign a staff member to a location */
    public function assignStaff()
    {
        $userId     = $_POST['user_id'] ?? null;
        $locationId = $_POST['location_id'] ?? null;

        if (!$userId) {
            header('Location: /erp/locations');
            exit;
        }

        // $locationId can be empty string (means "no location"), convert to NULL
        $locationId = ($locationId !== '' && $locationId !== null) ? (int)$locationId : null;

        $stmt = $this->pdo->prepare("UPDATE users SET location_id = ? WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$locationId, $userId, $this->tenantId]);

        header('Location: /erp/locations?success=assigned');
        exit;
    }
}
