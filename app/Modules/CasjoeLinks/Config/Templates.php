<?php

namespace App\Modules\CasjoeLinks\Config;

class Templates
{
    public static function all()
    {
        return [
            'default' => [
                'name' => 'Clean Slate',
                'bg_color' => '#ffffff',
                'text_color' => '#000000',
                'btn_bg' => '#f8f9fa',
                'btn_text' => '#000000',
                'font' => 'sans-serif'
            ],
            'dark_mode' => [
                'name' => 'Midnight',
                'bg_color' => '#121212',
                'text_color' => '#ffffff',
                'btn_bg' => '#333333',
                'btn_text' => '#ffffff',
                'font' => 'sans-serif'
            ],
            'ocean' => [
                'name' => 'Ocean Breeze',
                'bg_color' => '#e0f7fa',
                'text_color' => '#006064',
                'btn_bg' => '#00acc1',
                'btn_text' => '#ffffff',
                'font' => 'serif'
            ],
            'poppy' => [
                'name' => 'Sunset',
                'bg_color' => '#fff3e0',
                'text_color' => '#bf360c',
                'btn_bg' => '#ff5722',
                'btn_text' => '#ffffff',
                'font' => 'cursive'
            ],
            'forest' => [
                'name' => 'Forest',
                'bg_color' => '#1b5e20',
                'text_color' => '#e8f5e9',
                'btn_bg' => '#2e7d32',
                'btn_text' => '#ffffff',
                'font' => 'sans-serif'
            ],
            'space' => [
                'name' => 'Deep Space',
                'bg_color' => '#000000',
                'bg_image' => 'https://images.unsplash.com/photo-1534796636912-3b95b3ab5986?ixlib=rb-1.2.1&auto=format&fit=crop&w=1951&q=80',
                'text_color' => '#ffffff',
                'btn_bg' => 'rgba(255, 255, 255, 0.1)',
                'btn_text' => '#ffffff',
                'glass' => true,
                'font' => 'sans-serif'
            ],
            'glass' => [
                'name' => 'Glassmorphism',
                'bg_color' => '#8e2de2',
                'bg_image' => 'https://images.unsplash.com/photo-1557683316-973673baf926?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80', 
                'text_color' => '#ffffff',
                'btn_bg' => 'rgba(255, 255, 255, 0.2)',
                'btn_text' => '#ffffff',
                'glass' => true,
                'font' => 'sans-serif'
            ],
            'modern' => [
                'name' => 'Modern Profile',
                'bg_color' => '#b8c5b4',
                'text_color' => '#2d3a2e',
                'btn_bg' => '#ffffff',
                'btn_text' => '#2d3a2e',
                'font' => 'sans-serif',
                'layout' => 'modern'
            ],
            'aurora' => [
                'name' => 'Aurora Borealis',
                'bg_color' => '#0f172a',
                'bg_image' => 'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?auto=format&fit=crop&w=1951&q=80',
                'text_color' => '#ffffff',
                'btn_bg' => 'rgba(255, 255, 255, 0.15)',
                'btn_text' => '#ffffff',
                'font' => 'sans-serif',
                'layout' => 'modern',
                'glass' => true
            ],
            'cyberpunk' => [
                'name' => 'Cyberpunk Neon',
                'bg_color' => '#0d0221',
                'bg_image' => 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?auto=format&fit=crop&w=1951&q=80',
                'text_color' => '#00ffcc',
                'btn_bg' => 'rgba(20, 20, 20, 0.8)',
                'btn_text' => '#ff00ff',
                'font' => 'sans-serif',
                'layout' => 'modern',
                'glass' => true
            ],
            'minimal_cream' => [
                'name' => 'Minimal Cream',
                'bg_color' => '#fdfbf7',
                'text_color' => '#2c2c2c',
                'btn_bg' => '#1a1a1a',
                'btn_text' => '#fdfbf7',
                'font' => 'sans-serif',
                'layout' => 'modern'
            ],
            'holographic' => [
                'name' => 'Holographic Glass',
                'bg_color' => '#c2e9fb',
                'bg_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=2564&q=80',
                'text_color' => '#1e293b',
                'btn_bg' => 'rgba(255, 255, 255, 0.4)',
                'btn_text' => '#0f172a',
                'font' => 'sans-serif',
                'layout' => 'modern',
                'glass' => true
            ],
            'monochrome_chic' => [
                'name' => 'Monochrome Chic',
                'bg_color' => '#111111',
                'text_color' => '#ffffff',
                'btn_bg' => '#ffffff',
                'btn_text' => '#111111',
                'font' => 'sans-serif',
                'layout' => 'modern'
            ]
        ];
    }
}

