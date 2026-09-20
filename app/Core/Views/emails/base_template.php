<?php
/**
 * Base Email Template
 * Variables available: $content, $title (optional)
 */

$year = date('Y');
$tenant = \App\Core\TenantContext::getTenant();

$defaultCasjoeLogo = 'https://app.casjoe.com/casjoe_logo.png';
$settingLogo = App\Core\EmailTemplate::getSetting('email_logo_url');
if (empty($settingLogo) || strpos($settingLogo, 'casjoe') === false) {
    $settingLogo = $defaultCasjoeLogo;
}

if ($tenant && !empty($tenant['name']) && !in_array(strtolower(trim($tenant['name'])), ['casjoe', 'casjoe llc', 'treasy emporium', 'treasy  emporium'])) {
    $logoUrl = !empty($tenant['logo']) ? $tenant['logo'] : $settingLogo;
    $brandColor = !empty($tenant['brand_color']) ? $tenant['brand_color'] : App\Core\EmailTemplate::getSetting('email_brand_color', '#000066');
    $supportEmail = !empty($tenant['support_email']) ? $tenant['support_email'] : (!empty($tenant['email']) ? $tenant['email'] : App\Core\EmailTemplate::getSetting('email_support_email', 'support@casjoe.com'));
    $companyName = !empty($tenant['name']) ? $tenant['name'] : 'Casjoe LLC';
    
    // Attempt to resolve website url
    if (!empty($tenant['domain'])) {
        $websiteUrl = 'https://' . $tenant['domain'];
    } elseif (!empty($tenant['subdomain'])) {
        $websiteUrl = 'https://' . $tenant['subdomain'] . '.casjoe.com';
    } else {
        $websiteUrl = 'https://app.casjoe.com';
    }
} else {
    $logoUrl = $settingLogo;
    $brandColor = App\Core\EmailTemplate::getSetting('email_brand_color', '#000066');
    $supportEmail = App\Core\EmailTemplate::getSetting('email_support_email', 'support@casjoe.com');
    $companyName = 'Casjoe LLC';
    $websiteUrl = 'https://app.casjoe.com';
}

// Ensure logo has absolute URL for email clients
if (strpos($logoUrl, 'http') !== 0) {
    $logoUrl = 'https://app.casjoe.com' . (strpos($logoUrl, '/') === 0 ? '' : '/') . $logoUrl;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title ?? 'Notification' ?> | Casjoe</title>
    <!-- Note: Email clients strip external stylesheets and scripts, so all CSS is inline or in a style block -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f5f7fa;
            padding: 40px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            /* Email client safe shadow fallback */
            box-shadow: 0 10px 40px rgba(0,0,102,0.08);
        }
        
        .email-header {
            background-color: <?= $brandColor ?>;
            background-image: linear-gradient(135deg, <?= $brandColor ?> 0%, #000088 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header img {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .email-body {
            padding: 45px 35px;
            color: #334155;
            line-height: 1.7;
            font-size: 16px;
        }
        
        .email-body h1 {
            color: <?= $brandColor ?>;
            font-size: 26px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        .email-body h2 {
            color: <?= $brandColor ?>;
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .email-body p {
            margin: 16px 0;
            color: #475569;
        }
        
        .btn {
            display: inline-block;
            padding: 16px 40px;
            background-color: #FFA600;
            color: #000066 !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            margin: 25px 0;
            text-align: center;
        }
        
        .highlight-box {
            background-color: rgba(0,0,102,0.03);
            border-left: 4px solid #FFA600;
            padding: 24px;
            margin: 30px 0;
            border-radius: 0 12px 12px 0;
            color: #1e293b;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 35px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .social-links {
            margin: 0 0 25px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
            color: <?= $brandColor ?>;
            font-weight: 600;
            font-size: 14px;
        }
        
        .footer-text {
            color: #64748b;
            font-size: 13px;
            margin: 10px 0;
            line-height: 1.5;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: <?= $brandColor ?>;
            text-decoration: none;
            margin: 0 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 20px 10px; }
            .email-container { border-radius: 16px; }
            .email-body { padding: 30px 20px; }
            .email-body h1 { font-size: 22px; }
            .btn { display: block; width: auto; padding: 16px 20px; }
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title ?? 'Notification' ?> | Casjoe</title>
    <!-- Note: Email clients strip external stylesheets and scripts, so all CSS is inline or in a style block -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-wrapper {
            width: 100%;
            background-color: #f5f7fa;
            padding: 40px 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            /* Email client safe shadow fallback */
            box-shadow: 0 10px 40px rgba(0,0,102,0.08);
        }
        
        .email-header {
            background-color: <?= $brandColor ?>;
            background-image: linear-gradient(135deg, <?= $brandColor ?> 0%, #000088 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header img {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .email-body {
            padding: 45px 35px;
            color: #334155;
            line-height: 1.7;
            font-size: 16px;
        }
        
        .email-body h1 {
            color: <?= $brandColor ?>;
            font-size: 26px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        .email-body h2 {
            color: <?= $brandColor ?>;
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .email-body p {
            margin: 16px 0;
            color: #475569;
        }
        
        .btn {
            display: inline-block;
            padding: 16px 40px;
            background-color: #FFA600;
            color: #000066 !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            margin: 25px 0;
            text-align: center;
        }
        
        .highlight-box {
            background-color: rgba(0,0,102,0.03);
            border-left: 4px solid #FFA600;
            padding: 24px;
            margin: 30px 0;
            border-radius: 0 12px 12px 0;
            color: #1e293b;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 35px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .social-links {
            margin: 0 0 25px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
            color: <?= $brandColor ?>;
            font-weight: 600;
            font-size: 14px;
        }
        
        .footer-text {
            color: #64748b;
            font-size: 13px;
            margin: 10px 0;
            line-height: 1.5;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: <?= $brandColor ?>;
            text-decoration: none;
            margin: 0 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 20px 10px; }
            .email-container { border-radius: 16px; }
            .email-body { padding: 30px 20px; }
            .email-body h1 { font-size: 22px; }
            .btn { display: block; width: auto; padding: 16px 20px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header with Logo -->
            <div class="email-header">
                <a href="<?= htmlspecialchars($websiteUrl) ?>" target="_blank">
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($companyName) ?> Logo">
                </a>
            </div>
            
            <!-- Main Content -->
            <div class="email-body">
                <?= $content ?>
            </div>
            
            <!-- Footer with Social Media -->
            <div class="email-footer">
                <!-- Social Media Links (Text-based for email safety) -->
                <div class="social-links">
                    <?php
                    $socials = [
                        'social_facebook' => 'Facebook',
                        'social_twitter' => 'X (Twitter)',
                        'social_instagram' => 'Instagram',
                        'social_linkedin' => 'LinkedIn',
                        'social_youtube' => 'YouTube'
                    ];
                    
                    if (!$tenant): // Only show global social links if NOT a tenant
                        foreach ($socials as $key => $label):
                            $url = App\Core\EmailTemplate::getSetting($key);
                            if (!empty($url)):
                        ?>
                            <a href="<?= htmlspecialchars($url) ?>"><?= $label ?></a>
                        <?php endif; endforeach; 
                    else: 
                        // Show tenant specific social links
                        foreach ($socials as $key => $label):
                            $url = App\Core\TenantContext::getSetting($key);
                            if (!empty($url)):
                        ?>
                            <a href="<?= htmlspecialchars($url) ?>"><?= $label ?></a>
                        <?php endif; endforeach; 
                    endif; 
                    ?>
                </div>
                
                <!-- Footer Links -->
                <div class="footer-links">
                    <?php if ($tenant): ?>
                        <a href="<?= htmlspecialchars($websiteUrl) ?>">Website</a> | 
                        <a href="mailto:<?= htmlspecialchars($supportEmail) ?>">Contact Support</a>
                    <?php else: ?>
                        <a href="https://app.casjoe.com">Dashboard</a> | 
                        <a href="https://app.casjoe.com/support">Support Center</a> | 
                        <a href="https://app.casjoe.com/privacy">Privacy Policy</a>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Text -->
                <p class="footer-text">
                    Need help? We're here for you at <a href="mailto:<?= htmlspecialchars($supportEmail) ?>" style="color: <?= $brandColor ?>; text-decoration: none; font-weight: 600;"><?= htmlspecialchars($supportEmail) ?></a>
                </p>
                <p class="footer-text">
                    &copy; <?= $year ?> <?= htmlspecialchars($companyName) ?>. All rights reserved.
                </p>
                <p class="footer-text" style="font-size: 11px; margin-top: 20px;">
                    This email was sent to you on behalf of <?= htmlspecialchars($companyName) ?>. <br>
                    Please do not reply directly to this automated email address.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
