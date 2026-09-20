require_once __DIR__ . '/../app/Core/bootstrap.php';

$db = \App\Core\Database::getInstance()->getConnection();

echo "<h1>Updating System Settings for Integrations & CMS</h1>";
echo "<pre>";

$settings = [
    // Google Analytics
    'google_analytics_id' => '',
    
    // Google reCaptcha
    'recaptcha_enabled' => '0',
    'recaptcha_site_key' => '',
    'recaptcha_secret_key' => '',
    
    // Tawk.to
    'tawk_enabled' => '0',
    'tawk_property_id' => '',
    
    // Pusher
    'pusher_enabled' => '0',
    'pusher_app_id' => '',
    'pusher_key' => '',
    'pusher_secret' => '',
    'pusher_cluster' => '',
    
    // Google Translate
    'google_translate_enabled' => '0',
    
    // Homepage CMS
    'home_hero_title' => 'One Account. Every Tool Your Business Needs.',
    'home_hero_subtitle' => 'The Complete Business Ecosystem',
    'home_hero_desc' => 'Operate, sell, get paid, grow, and scale—all from one connected ecosystem. Activate only the modules you need. No scattered tools. No fragmented data.',
    'home_hero_cta_text' => 'Start Your Business',
    'home_hero_cta_link' => '/register'
];

try {
    $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = setting_value");
    
    foreach ($settings as $key => $default) {
        $stmt->execute([$key, $default]);
        echo " - Checked/Inserted: $key\n";
    }
    
    echo "\nAll integration settings initialized.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";
