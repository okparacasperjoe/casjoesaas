<?php

namespace App\Core\Controllers;

use App\Core\Auth;
use App\Core\Database;

class IntegrationApiController
{
    public function getIntegrations()
    {
        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $user = Auth::user();
        $tenantId = $user['tenant_id'];
        $type = $_GET['type'] ?? 'cloud';
        $db = Database::getInstance();
        $items = [];

        try {
            if ($type === 'cloud') {
                $stmt = $db->prepare("SELECT name, path, type as meta_type FROM cloud_files WHERE tenant_id = ? ORDER BY created_at DESC");
                $stmt->execute([$tenantId]);
                $files = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($files as $file) {
                    $items[] = [
                        'name' => $file['name'],
                        'url' => defined('APP_URL') ? APP_URL . '/' . ltrim($file['path'], '/') : '/' . ltrim($file['path'], '/'),
                        'meta_type' => $file['meta_type']
                    ];
                }
            } elseif ($type === 'links') {
                // Short links
                $stmt = $db->prepare("SELECT short_code, long_url FROM links_short_urls WHERE tenant_id = ? AND status = 'active' ORDER BY created_at DESC");
                $stmt->execute([$tenantId]);
                $links = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($links as $link) {
                    $items[] = [
                        'name' => 'Short Link: ' . $link['short_code'],
                        'url' => defined('APP_URL') ? APP_URL . '/l/' . $link['short_code'] : '/l/' . $link['short_code']
                    ];
                }
                
                // Bio Pages
                $stmt = $db->prepare("SELECT title, slug FROM links_bio_pages WHERE tenant_id = ? AND status = 'active' ORDER BY created_at DESC");
                $stmt->execute([$tenantId]);
                $bios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($bios as $bio) {
                    $items[] = [
                        'name' => 'Bio Page: ' . $bio['title'],
                        'url' => defined('APP_URL') ? APP_URL . '/bio/' . $bio['slug'] : '/bio/' . $bio['slug']
                    ];
                }
            } elseif ($type === 'smart_forms') {
                $stmt = $db->prepare("SELECT title, slug FROM sf_forms WHERE tenant_id = ? ORDER BY created_at DESC");
                $stmt->execute([$tenantId]);
                $forms = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($forms as $form) {
                    $items[] = [
                        'name' => $form['title'],
                        'url' => defined('APP_URL') ? APP_URL . '/f/' . $form['slug'] : '/f/' . $form['slug']
                    ];
                }
            } elseif ($type === 'academy') {
                $stmt = $db->prepare("SELECT title, slug FROM academy_courses WHERE tenant_id = ? ORDER BY created_at DESC");
                $stmt->execute([$tenantId]);
                $courses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($courses as $course) {
                    $items[] = [
                        'name' => $course['title'],
                        'url' => defined('APP_URL') ? APP_URL . '/academy/c/' . $course['slug'] : '/academy/c/' . $course['slug']
                    ];
                }
            } elseif ($type === 'shop') {
                $stmt = $db->prepare("SELECT title, slug FROM shop_products WHERE tenant_id = ? ORDER BY created_at DESC");
                $stmt->execute([$tenantId]);
                $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($products as $product) {
                    $items[] = [
                        'name' => $product['title'],
                        'url' => defined('APP_URL') ? APP_URL . '/shop/p/' . $product['slug'] : '/shop/p/' . $product['slug']
                    ];
                }
            }
        } catch (\PDOException $e) {
            // If table doesn't exist, ignore and return empty list
        }

        echo json_encode(['items' => $items]);
        exit;
    }
}
