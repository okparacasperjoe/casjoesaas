<?php
$content = file_get_contents('c:\Users\UK USER\.gemini\antigravity\scratch\php-saas\app\Modules\CasjoeERP\Controllers\EmployeeController.php');
$lines = explode("\n", $content);
$open = 0;
$close = 0;
$depth = 0;
$output = "";

foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    $o = substr_count($line, '{');
    $c = substr_count($line, '}');
    $depth += $o - $c;
    if ($o > 0 || $c > 0) {
        $output .= "Line $lineNum | Open: $o | Close: $c | Depth: $depth | Content: " . trim($line) . "\n";
    }
}
$output .= "Final Depth: $depth\n";
file_put_contents('c:\Users\UK USER\.gemini\antigravity\scratch\php-saas\scripts\brace_count.txt', $output);
echo "Done\n";
