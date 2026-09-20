<?php
require_once __DIR__ . '/app/Core/bootstrap.php';
// Runs every 5 or 15 mins to publish scheduled posts
$count = \App\Modules\CasjoeLinks\Services\SocialPlannerService::publishScheduledPosts();
echo "Published {$count} scheduled social posts.\n";
