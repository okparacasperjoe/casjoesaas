<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Auth;
use App\Core\Database;

class ShortenerController
{
    public function index()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $stmt = $db->query("SELECT * FROM links_short_urls WHERE tenant_id = ? AND user_id = ? ORDER BY created_at DESC", 
            [$user['tenant_id'], $user['id']]);
        $urls = $stmt->fetchAll();

        require __DIR__ . '/../Views/shortener/index.php';
    }

    public function create()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../Views/shortener/create.php';
    }

    public function store()
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();

        $longUrl = $_POST['long_url'];
        $shortCode = $_POST['short_code'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

        if (empty($longUrl)) {
             header('Location: /links/short/create?error=missing_url');
             exit;
        }

        // Auto-generate code if empty
        if (empty($shortCode)) {
            $shortCode = substr(md5(uniqid()), 0, 6);
        }

        try {
            $db->query("INSERT INTO links_short_urls (tenant_id, user_id, long_url, short_code, password, expires_at) VALUES (?, ?, ?, ?, ?, ?)", 
                [$user['tenant_id'], $user['id'], $longUrl, $shortCode, $password, $expiresAt]
            );
            header('Location: /links/short');
        } catch (\PDOException $e) {
             header('Location: /links/short/create?error=code_exists');
        }
    }

    public function edit($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $id = $params['id'] ?? 0;

        $stmt = $db->prepare("SELECT * FROM links_short_urls WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $user['tenant_id']]);
        $url = $stmt->fetch();

        if (!$url) {
            header('Location: /links/short');
            exit;
        }

        require __DIR__ . '/../Views/shortener/edit.php';
    }

    public function update($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $id = $params['id'] ?? 0;

        $longUrl = $_POST['long_url'] ?? '';
        $shortCode = $_POST['short_code'] ?? '';

        $stmt = $db->prepare("UPDATE links_short_urls SET long_url = ?, short_code = ? WHERE id = ? AND tenant_id = ?");
        try {
            $stmt->execute([$longUrl, $shortCode, $id, $user['tenant_id']]);
        } catch (\PDOException $e) {}

        header('Location: /links/short');
        exit;
    }

    public function delete($params)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = Database::getInstance();
        $id = $params['id'] ?? 0;

        $stmt = $db->prepare("DELETE FROM links_short_urls WHERE id = ? AND tenant_id = ?");
        $stmt->execute([$id, $user['tenant_id']]);

        header('Location: /links/short');
        exit;
    }
}
