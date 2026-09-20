<?php

namespace App\Core\Services;

use App\Core\Mailer;
use App\Core\EmailTemplate;

class NotificationService
{
    /**
     * Send a transactional email with the standard Casjoe UI wrapper.
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $title Content title (displayed inside body)
     * @param string $contentHtml Main body content (HTML)
     */
    public static function sendTransactional($to, $subject, $title, $contentHtml)
    {
        // Load Settings for Template
        $logoUrl = 'https://app.casjoe.com/assets/casjoe_logo.webp'; // Hardcoded fallback or fetch from settings
        $brandColor = '#000066';
        $supportEmail = 'support@casjoe.com';
        $year = date('Y');

        // Construct HTML
        $html = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
                .email-container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
                .email-header { background: $brandColor; padding: 30px; text-align: center; }
                .email-header img { max-width: 150px; height: auto; }
                .email-body { padding: 40px 30px; color: #333; line-height: 1.6; }
                .email-body h2 { color: $brandColor; margin-top: 0; }
                .email-footer { background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eee; font-size: 13px; color: #666; }
                .social-links { margin: 20px 0; }
                .social-links a { margin: 0 5px; text-decoration: none; color: $brandColor; font-weight: bold; }
                .disclaimer { margin-top: 20px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <!-- Logo Placeholder (If image fails, text shows) -->
                    <div style='color: white; font-size: 24px; font-weight: bold;'>Casjoe</div>
                </div>
                
                <div class='email-body'>
                    <h2>$title</h2>
                    $contentHtml
                </div>
                
                <div class='email-footer'>
                   <div class='social-links'>
                        <a href='#'>Facebook</a> | <a href='#'>Twitter</a> | <a href='#'>Instagram</a> | <a href='#'>LinkedIn</a>
                   </div>
                   
                   <p>Need help? <a href='mailto:$supportEmail'>$supportEmail</a></p>
                   <p>&copy; $year Casjoe LLC. All rights reserved.</p>
                   
                   <div class='disclaimer'>
                        <strong>Security Alert:</strong> If you did not initiate this transaction, please contact our support team immediately via the in-app chat or email us at <a href='mailto:$supportEmail'>$supportEmail</a>.
                   </div>
                </div>
            </div>
        </body>
        </html>
        ";

        // Send via Mailer
        return Mailer::send($to, $subject, $html);
    }
}
