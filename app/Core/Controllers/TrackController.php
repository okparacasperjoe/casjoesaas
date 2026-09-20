<?php

namespace App\Core\Controllers;

use App\Core\Database;
use App\Core\Auth;

class TrackController
{
    public function welcomeClick()
    {
        $type = $_GET['type'] ?? ''; // academy or referral
        $userId = $_GET['u'] ?? 0;
        
        // Basic Validation
        if (!$type || !$userId) {
            header('Location: /dashboard');
            exit;
        }

        // Log Click
        $db = Database::getInstance();
        try {
            // Create table if not exists (Lazy Migration)
            $db->query("CREATE TABLE IF NOT EXISTS link_clicks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                link_type VARCHAR(50),
                clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $db->query("INSERT INTO link_clicks (user_id, link_type) VALUES (?, ?)", [$userId, $type]);
        } catch (\Exception $e) {
            // Ignore logging errors, prioritize user flow
        }

        // Redirect
        if ($type === 'academy') {
            // Check for Casjoe Mastery course
            // If we can't find exact slug, go to academy root
            header('Location: /academy'); 
        } elseif ($type === 'referral') {
            // Get user's referral code to show them? 
            // The link provided in email is the *User's Referral Link* 
            // Wait, the user request says: "Your referral link: {{ReferralLink}}"
            // If the user clicks *their own* referral link, they just go to the registration page with their own ref code?
            // "When people sign up using your link... you earn"
            // So the {{ReferralLink}} in the email is https://app.casjoe.com/register?ref=XYZ
            // AND the user wants to "Track clicks on ... Referral links from this email".
            // So the email should contain: https://app.casjoe.com/track/welcome/referral?u=ID
            // And that redirects to... THE REFERRAL LINK?
            // Yes.
            
            // We need to fetch the user's code to construct the destination
            $stmt = $db->query("SELECT referral_code FROM users WHERE id = ?", [$userId]);
            $code = $stmt->fetchColumn();
            
            if ($code) {
                // Determine Registration URL (Tenant 1 or Global?)
                // Default global register
                header("Location: " . (getenv('APP_URL') ?: '') . "/register?ref=$code");
            } else {
                header('Location: /dashboard');
            }
        } else {
             header('Location: /dashboard');
        }
        exit;
    }
}
