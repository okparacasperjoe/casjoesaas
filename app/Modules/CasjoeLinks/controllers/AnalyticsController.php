<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\TenantContext;

class AnalyticsController
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function dashboard()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();

        // 1. Total Stats (Last 30 Days)
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_visits,
                COUNT(DISTINCT visitor_ip) as unique_visitors
            FROM links_analytics 
            WHERE tenant_id = ? 
            AND visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ", [$this->tenantId]);
        $summary = $stmt->fetch();

        // 2. Device Breakdown
        $stmt = $this->db->query("
            SELECT device_type, COUNT(*) as count 
            FROM links_analytics 
            WHERE tenant_id = ? 
            GROUP BY device_type
        ", [$this->tenantId]);
        $devices = $stmt->fetchAll();

        // 3. Top Countries (Mocked as City/Country is null for now, using IP grouping or just raw data)
        // Since we didn't implement GeoIP, we'll skip Country map for now or show "Unknown"
        
        // 4. Top Referrers
        $stmt = $this->db->query("
            SELECT referrer, COUNT(*) as count 
            FROM links_analytics 
            WHERE tenant_id = ? 
            AND referrer IS NOT NULL 
            GROUP BY referrer 
            ORDER BY count DESC 
            LIMIT 5
        ", [$this->tenantId]);
        $referrers = $stmt->fetchAll();

        // 5. Recent Activity
        $stmt = $this->db->query("
            SELECT la.*, 
                   CASE 
                       WHEN link_type = 'short' THEN (SELECT short_code FROM links_short_urls WHERE id = la.link_id)
                       WHEN link_type = 'bio' THEN (SELECT slug FROM links_bio_pages WHERE id = la.link_id)
                   END as link_name
            FROM links_analytics la
            WHERE tenant_id = ? 
            ORDER BY visited_at DESC 
            LIMIT 10
        ", [$this->tenantId]);
        $recent = $stmt->fetchAll();

        require __DIR__ . '/../Views/analytics/dashboard.php';
    }
}

