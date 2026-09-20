<?php
namespace App\Core\Controllers;
use App\Core\Auth;
use App\Core\AI\BusinessManager;
class AIController {
    public function getBriefing() {
        $user = Auth::user();
        if (!$user) { header("HTTP/1.1 401 Unauthorized"); return; }
        $manager = new BusinessManager($user["tenant_id"], $user["name"] ?? 'User');
        echo json_encode(["status"=>"success", "summary"=>$manager->getMorningBriefing()]);
    }
}
