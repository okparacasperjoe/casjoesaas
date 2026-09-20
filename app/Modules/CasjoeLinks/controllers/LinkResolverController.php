<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Database;

class LinkResolverController
{
    /**
     * Resolve and redirect short URLs
     */
    public function resolveShortUrl($params)
    {
        $code = $params['code'] ?? '';
        if (!$code) {
            http_response_code(404);
            exit("Link not found.");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM links_short_urls WHERE short_code = ? AND status = 'active'");
        $stmt->execute([$code]);
        $link = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$link) {
            http_response_code(404);
            exit("Link not found or deactivated.");
        }

        // Check Expiration
        if (!empty($link['expires_at']) && strtotime($link['expires_at']) < time()) {
            http_response_code(410);
            exit("This link has expired.");
        }

        // Check Max Clicks
        if ($link['max_clicks'] > 0 && $link['clicks'] >= $link['max_clicks']) {
            http_response_code(410);
            exit("This link has reached its maximum number of clicks.");
        }

        // Check Password Protection
        if (!empty($link['password'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $sessionKey = 'unlocked_link_' . $link['id'];
            
            if (empty($_SESSION[$sessionKey])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (password_verify($_POST['link_password'] ?? '', $link['password'])) {
                        $_SESSION[$sessionKey] = true;
                        header("Location: /l/" . $code);
                        exit;
                    } else {
                        $error = "Incorrect password.";
                    }
                }
                
                // Render simple password form
                echo '<!DOCTYPE html><html><head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png"><title>Password Protected Link</title>';
                echo '<style>body{font-family:sans-serif;background:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;} .card{background:#fff;padding:30px;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,0.1);text-align:center;max-width:400px;width:100%;} input{width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:5px;} button{background:#000066;color:#fff;border:none;padding:10px 20px;border-radius:5px;cursor:pointer;width:100%;} .err{color:red;margin-bottom:10px;}</style>';
                echo '</head><body><div class="card"><h2>Password Protected</h2><p>This link requires a password to proceed.</p>';
                if (!empty($error)) echo '<div class="err">'.$error.'</div>';
                echo '<form method="POST"><input type="password" name="link_password" placeholder="Enter Password" required><button type="submit">Unlock Link</button></form>';
                echo '</div></body></html>';
                exit;
            }
        }

        // Log Analytics
        $this->logAnalytics($link['tenant_id'], 'short_url', $link['id']);

        // Update Clicks
        $update = $db->prepare("UPDATE links_short_urls SET clicks = clicks + 1 WHERE id = ?");
        $update->execute([$link['id']]);

        // Redirect
        header("Location: " . $link['long_url']);
        exit;
    }

    /**
     * Resolve and render Bio Pages
     */
    public function resolveBioPage($params)
    {
        $slug = $params['slug'] ?? '';
        if (!$slug) {
            http_response_code(404);
            exit("Bio page not found.");
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM links_bio_pages WHERE slug = ? AND status = 'active'");
        $stmt->execute([$slug]);
        $bio = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$bio) {
            http_response_code(404);
            exit("Bio page not found or deactivated.");
        }

        // Log Analytics
        $this->logAnalytics($bio['tenant_id'], 'bio_page', $bio['id']);

        // Update Views
        $update = $db->prepare("UPDATE links_bio_pages SET views = views + 1 WHERE id = ?");
        $update->execute([$bio['id']]);

        // Parse Data
        $page = $bio;
        
        // Render the public view
        require __DIR__ . '/../Views/bio/public_modern.php';
    }

    /**
     * Log Analytics for Link Clicks/Views
     */
    private function logAnalytics($tenantId, $linkType, $linkId)
    {
        try {
            $db = Database::getInstance();
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Basic parsing (In production, use a dedicated library like get_browser or similar)
            $deviceType = (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) ? 'mobile' : 'desktop';
            $os = 'unknown';
            if (preg_match('/windows/i', $userAgent)) $os = 'Windows';
            elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'MacOS';
            elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux';
            elseif (preg_match('/android/i', $userAgent)) $os = 'Android';
            elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $os = 'iOS';

            $browser = 'unknown';
            if (preg_match('/chrome|crios/i', $userAgent)) $browser = 'Chrome';
            elseif (preg_match('/firefox|fxios/i', $userAgent)) $browser = 'Firefox';
            elseif (preg_match('/safari/i', $userAgent)) $browser = 'Safari';
            elseif (preg_match('/opr\//i', $userAgent)) $browser = 'Opera';
            elseif (preg_match('/edg/i', $userAgent)) $browser = 'Edge';

            $stmt = $db->prepare("INSERT INTO links_analytics (tenant_id, link_type, link_id, visitor_ip, device_type, os, browser, referrer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tenantId, $linkType, $linkId, $ip, $deviceType, $os, $browser, $referrer]);
        } catch (\Exception $e) {
            // Silently fail analytics if error
        }
    }
}
