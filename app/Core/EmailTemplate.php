<?php

namespace App\Core;

class EmailTemplate
{
    private static $settings = [];

    /**
     * Load settings from database
     */
    private static function loadSettings()
    {
        if (empty(self::$settings)) {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'smtp_%' OR setting_key LIKE 'email_%' OR setting_key LIKE 'social_%'");
            while ($row = $stmt->fetch()) {
                self::$settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        return self::$settings;
    }

    /**
     * Render email template with data
     * 
     * @param string $templateName Template file name (without .php)
     * @param array $data Data to pass to template
     * @return string Rendered HTML
     */
    public static function render($templateName, $data = [])
    {
        self::loadSettings();
        
        // Extract data for use in template
        extract($data);
        
        // Template paths
        $templatePath = __DIR__ . "/Views/emails/{$templateName}.php";
        $basePath = __DIR__ . "/Views/emails/base_template.php";
        
        // Render the specific template
        ob_start();
        if (file_exists($templatePath)) {
            require $templatePath;
        } else {
            // Fallback to generic template
            echo $data['message'] ?? 'No message provided.';
        }
        $content = ob_get_clean();
        
        // Wrap in base template
        ob_start();
        require $basePath;
        return ob_get_clean();
    }

    /**
     * Get setting value
     */
    public static function getSetting($key, $default = '')
    {
        self::loadSettings();
        return self::$settings[$key] ?? $default;
    }

    /**
     * Generate social media footer HTML
     */
    public static function getSocialFooter()
    {
        self::loadSettings();
        
        $socials = [
            'social_facebook' => ['icon' => 'logo-facebook', 'color' => '#1877F2', 'label' => 'Facebook'],
            'social_twitter' => ['icon' => 'logo-twitter', 'color' => '#1DA1F2', 'label' => 'Twitter'],
            'social_instagram' => ['icon' => 'logo-instagram', 'color' => '#E4405F', 'label' => 'Instagram'],
            'social_linkedin' => ['icon' => 'logo-linkedin', 'color' => '#0A66C2', 'label' => 'LinkedIn'],
            'social_youtube' => ['icon' => 'logo-youtube', 'color' => '#FF0000', 'label' => 'YouTube']
        ];
        
        $html = '<div style="text-align: center; margin-top: 30px;">';
        
        foreach ($socials as $key => $social) {
            if (!empty(self::$settings[$key])) {
                $url = self::$settings[$key];
                $html .= sprintf(
                    '<a href="%s" style="display: inline-block; margin: 0 10px; text-decoration: none;">
                        <span style="display: inline-block; width: 40px; height: 40px; line-height: 40px; background: %s; border-radius: 50%%; color: white; font-size: 20px;">
                            <ion-icon name="%s"></ion-icon>
                        </span>
                    </a>',
                    htmlspecialchars($url),
                    $social['color'],
                    $social['icon']
                );
            }
        }
        
        $html .= '</div>';
        return $html;
    }
}
