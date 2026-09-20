<?php

namespace App\Core\Services;

class LanguageService
{
    private static $translations = [];
    private static $currentLocale = 'en';

    public static function load($locale = 'en')
    {
        self::$currentLocale = $locale;
        $path = __DIR__ . '/../lang/' . $locale . '.json';
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            self::$translations = json_decode($content, true) ?: [];
        }
    }

    public static function translate($key, $default = null)
    {
        if (empty(self::$translations)) {
            self::load(self::$currentLocale);
        }

        return self::$translations[$key] ?? ($default ?: $key);
    }

    public static function setLocale($locale)
    {
        self::load($locale);
        $_SESSION['locale'] = $locale;
    }

    public static function getLocale()
    {
        return $_SESSION['locale'] ?? self::$currentLocale;
    }
}
