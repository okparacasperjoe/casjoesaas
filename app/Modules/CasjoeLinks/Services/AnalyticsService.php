<?php

namespace App\Modules\CasjoeLinks\Services;

use App\Core\Database;
use App\Core\TenantContext;

class AnalyticsService
{
    private $db;
    private $tenantId;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();
    }

    public function log($type, $id)
    {
        // 1. Basic Info
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $referrer = $_SERVER['HTTP_REFERER'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // 2. Parse User Agent (Simple)
        $os = $this->getOS($ua);
        $browser = $this->getBrowser($ua);
        $device = $this->getDevice($ua);

        // 3. Insert
        // Note: GeoIP is omitted for performance/complexity reasons in this mock
        $this->db->query(
            "INSERT INTO links_analytics (tenant_id, link_type, link_id, visitor_ip, device_type, os, browser, referrer) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$this->tenantId, $type, $id, $ip, $device, $os, $browser, $referrer]
        );
        
        // 4. Update total views/clicks counters on the main table for fast retrieval
        if ($type === 'short') {
            $this->db->query("UPDATE links_short_urls SET clicks = clicks + 1 WHERE id = ?", [$id]);
        } elseif ($type === 'bio') {
            $this->db->query("UPDATE links_bio_pages SET views = views + 1 WHERE id = ?", [$id]);
        }
    }

    private function getOS($ua)
    {
        if (preg_match('/windows/i', $ua)) return 'Windows';
        if (preg_match('/macintosh|mac os x/i', $ua)) return 'macOS';
        if (preg_match('/linux/i', $ua)) return 'Linux';
        if (preg_match('/android/i', $ua)) return 'Android';
        if (preg_match('/iphone|ipad|ipod/i', $ua)) return 'iOS';
        return 'Unknown';
    }

    private function getBrowser($ua)
    {
        if (preg_match('/msie|trident/i', $ua)) return 'Internet Explorer';
        if (preg_match('/firefox/i', $ua)) return 'Firefox';
        if (preg_match('/chrome/i', $ua)) return 'Chrome'; 
        if (preg_match('/safari/i', $ua)) return 'Safari';
        if (preg_match('/opera|opr/i', $ua)) return 'Opera';
        return 'Unknown';
    }

    private function getDevice($ua)
    {
        if (preg_match('/mobile|android|touch/i', $ua)) return 'Mobile';
        if (preg_match('/tablet|ipad/i', $ua)) return 'Tablet';
        return 'Desktop';
    }
}

