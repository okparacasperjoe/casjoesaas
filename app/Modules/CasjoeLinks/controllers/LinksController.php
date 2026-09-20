<?php

namespace App\Modules\CasjoeLinks\Controllers;

use App\Core\Auth;
use App\Core\View;

class LinksController
{
    public function dashboard()
    {
        // Require Login
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $user = Auth::user();
        $db = \App\Core\Database::getInstance();

        // Fetch Counts
        $stmtBio = $db->prepare("SELECT COUNT(*) FROM links_bio_pages WHERE tenant_id = ? AND user_id = ?");
        $stmtBio->execute([$user['tenant_id'], $user['id']]);
        $bioCount = $stmtBio->fetchColumn();

        $stmtShort = $db->prepare("SELECT COUNT(*) FROM links_short_urls WHERE tenant_id = ? AND user_id = ?");
        $stmtShort->execute([$user['tenant_id'], $user['id']]);
        $shortCount = $stmtShort->fetchColumn();

        $stmtQr = $db->prepare("SELECT COUNT(*) FROM links_qr_codes WHERE tenant_id = ? AND user_id = ?");
        $stmtQr->execute([$user['tenant_id'], $user['id']]);
        $qrCount = $stmtQr->fetchColumn();

        $stmtStatic = $db->prepare("SELECT COUNT(*) FROM links_static_sites WHERE tenant_id = ? AND user_id = ?");
        $stmtStatic->execute([$user['tenant_id'], $user['id']]);
        $staticCount = $stmtStatic->fetchColumn();

        // Load View
        require __DIR__ . '/../Views/dashboard.php';
    }
}
