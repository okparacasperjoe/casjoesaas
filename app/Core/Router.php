<?php

namespace App\Core;

class Router
{
    private static $routes = [];

    public static function add($method, $path, $callback)
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public static function get($path, $callback)
    {
        self::add('GET', $path, $callback);
    }

    public static function post($path, $callback)
    {
        self::add('POST', $path, $callback);
    }

    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Trim trailing slash for non-root paths to handle /admin/ vs /admin
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }
        $method = $_SERVER['REQUEST_METHOD'];

        // ── Visitor Tracking (lightweight, fire-and-forget) ──
        self::trackVisitor($uri, $method);

        foreach (self::$routes as $route) {
            // Simple string match or basic parameter logic could go here
            // For now, exact match or simple regex support

            // Extract parameter names from route path
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route['path'], $paramNames);
            $paramList = $paramNames[1] ?? [];

            // Convert /user/{id} to regex, allowing dashes and dots in parameters for filenames/slugs
            $pattern = "#^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_.-]+)', $route['path']) . "$#D";

            if (($method === $route['method'] || ($method === 'HEAD' && $route['method'] === 'GET')) && preg_match($pattern, $uri, $matches)) {
                // Global Authentication check for Restricted Areas
                $protectedPrefixes = [
                    '/erp', '/admin', '/casper-joe', '/dashboard', '/profile',
                    '/settings', '/billing', '/notifications', '/security',
                    '/ai-office', '/ai-builder', '/onboarding', '/pay',
                    '/academy', '/cloud', '/smart-forms', '/mail'
                ];

                $isProtected = false;
                foreach ($protectedPrefixes as $prefix) {
                    if ($uri === $prefix || strpos($uri, $prefix . '/') === 0) {
                        $isProtected = true;
                        break;
                    }
                }

                if (strpos($uri, '/cloud/asset/') === 0 || strpos($uri, '/cloud/preview') === 0 || strpos($uri, '/cloud/share') === 0 || strpos($uri, '/cloud/file/share') === 0 || strpos($uri, '/cloud/file/download') === 0 || strpos($uri, '/cloud/share/download') === 0) {
                    $isProtected = false;
                }

                // Exempt public payment and checkout endpoints
                $publicPayPrefixes = [
                    '/pay/checkout', '/pay/link/', '/pay/process/', '/pay/link-callback',
                    '/pay/request/', '/pay/webhook', '/casjoe-pay/checkout',
                    '/casjoe-pay/link/', '/casjoe-pay/process/', '/casjoe-pay/link-callback'
                ];
                $isPublicPay = false;
                foreach ($publicPayPrefixes as $pubPfx) {
                    if (strpos($uri, $pubPfx) === 0) {
                        $isProtected = false;
                        $isPublicPay = true;
                        break;
                    }
                }

                if ($isProtected && !\App\Core\Auth::check()) {
                    header('Location: /login');
                    exit;
                }

                // Casjoe Pay Global PIN Security Intercept
                if (!$isPublicPay && (strpos($uri, '/pay') === 0 || strpos($uri, '/casjoe-pay') === 0)) {
                    // Exclude the PIN verification and API endpoints
                    $excludePaths = ['/pay/pin/verify', '/pay/pin/setup', '/pay/pin/process', '/pay/api/rate', '/casjoe-pay/api/rate'];
                    if (!in_array($uri, $excludePaths)) {
                        // Check if PIN verified in this session
                        if (empty($_SESSION['pay_pin_verified'])) {
                            // Check if they even have a PIN setup
                            $dbCon = \App\Core\Database::getInstance()->getConnection();
                            $stmtPin = $dbCon->prepare("SELECT transaction_pin FROM users WHERE id = ?");
                            $stmtPin->execute([$_SESSION['user_id']]);
                            $userPin = $stmtPin->fetchColumn();
                            
                            if (empty($userPin)) {
                                header('Location: /pay/pin/setup');
                            } else {
                                header('Location: /pay/pin/verify');
                            }
                            exit;
                        }
                    }
                }

                // Ensure all named parameters from paramList are preserved
                $cleanMatches = [];
                foreach ($paramList as $idx => $pName) {
                    $cleanMatches[$pName] = $matches[$pName] ?? ($matches[$idx + 1] ?? null);
                }
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $cleanMatches[$key] = $value;
                    }
                }
                $matches = $cleanMatches;

                if (is_callable($route['callback'])) {
                    call_user_func($route['callback'], $matches);
                } elseif (is_array($route['callback'])) {
                    $controller = new $route['callback'][0]();
                    $method = $route['callback'][1];
                    call_user_func([$controller, $method], $matches);
                }
                return;
            }
        }

        http_response_code(404);
        if (file_exists(__DIR__ . '/Views/404.php')) {
            require __DIR__ . '/Views/404.php';
        } else {
            echo "404 Not Found";
        }
    }

    /**
     * Lightweight visitor tracking — logs page visits to site_visitors table.
     * Silent: will never break routing even if table doesn't exist.
     */
    private static function trackVisitor(string $uri, string $method): void
    {
        // Only track GET requests (page views, not form submissions / API calls)
        if ($method !== 'GET') return;

        // Skip static assets, debug files, and internal endpoints
        $skipPrefixes = ['/css/', '/js/', '/images/', '/fonts/', '/favicon', '/sw_debug', '/debug_', '/reset_cache', '/api/'];
        $skipExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.map'];
        foreach ($skipPrefixes as $prefix) {
            if (strpos($uri, $prefix) === 0) return;
        }
        foreach ($skipExtensions as $ext) {
            if (substr($uri, -strlen($ext)) === $ext) return;
        }

        try {
            $pdo = Database::getInstance()->getConnection();

            $ip        = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            // If multiple IPs (comma-separated from proxy), take the first
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
            $referrer  = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);
            $userId    = $_SESSION['user_id'] ?? null;

            // Simple bot detection
            $botKeywords = ['bot', 'crawl', 'spider', 'slurp', 'facebook', 'twitter', 'whatsapp', 'telegram', 'curl', 'wget', 'python', 'java/', 'go-http'];
            $isBot = 0;
            $uaLower = strtolower($userAgent);
            foreach ($botKeywords as $kw) {
                if (strpos($uaLower, $kw) !== false) {
                    $isBot = 1;
                    break;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO site_visitors (ip_address, url, user_agent, referrer, user_id, is_bot) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$ip, substr($uri, 0, 500), $userAgent, $referrer, $userId, $isBot]);
        } catch (\Throwable $e) {
            // Silently fail — never break the site for analytics
        }
    }
}
