<?php
require_once __DIR__ . "/../app/Core/bootstrap.php";
require_once __DIR__ . "/../app/Modules/CasjoePay/Services/StroWalletService.php";

$strowallet = new \App\Modules\CasjoePay\Services\StroWalletService();
$res = $strowallet->createCustomer(
    "miracle" . time() . "@casjoe.com",
    "Miracle",
    "Casper",
    "+2347035323038",
    "1990-04-15",
    "85170798348",
    "https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg",
    "https://upload.wikimedia.org/wikipedia/commons/a/a7/React-icon.svg"
);
echo "Create Customer Response:\n";
print_r($res);
@unlink(__FILE__);
