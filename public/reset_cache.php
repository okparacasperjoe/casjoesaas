<?php
// Reset PHP opcache to ensure fresh files are served
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "opcache_reset() called successfully.\n";
} else {
    echo "opcache not available.\n";
}

// Also invalidate the specific files
$files = [
    __DIR__ . '/../app/Modules/CasjoePay/Controllers/CardController.php',
    __DIR__ . '/../app/Modules/CasjoePay/Views/cards.php',
];
foreach ($files as $f) {
    if (function_exists('opcache_invalidate') && file_exists($f)) {
        opcache_invalidate($f, true);
        echo "Invalidated: $f\n";
    }
}
echo "\nDone. Now hard-refresh the cards page (Ctrl+Shift+R).\n";
