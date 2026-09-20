<?php

use App\Core\Services\LanguageService;

/**
 * Global translation helper
 */
if (!function_exists('__')) {
    function __($key, $default = null) {
        return LanguageService::translate($key, $default);
    }
}

/**
 * Clean path helper
 */
if (!function_exists('base_url')) {
    function base_url($path = '') {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

/**
 * CSRF Token helper
 */
if (!function_exists('csrf_field')) {
    function csrf_field() {
        $token = \App\Core\Services\CsrfService::getToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
