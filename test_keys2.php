<?php
require_once __DIR__ . "/../app/Core/bootstrap.php";
echo "Defined PUB: [" . (defined("STROWALLET_PUBLIC_KEY") ? STROWALLET_PUBLIC_KEY : "UNDEFINED") . "]\n";
@unlink(__FILE__);
