<?php

require_once __DIR__ . '/../app/core/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

echo "Seeding Professional Email Templates...\n";

$templates = [
    [
        'name' => 'Tech Product Launch',
        'subject' => 'Introducing the Future: {{product_name}}',
        'sector' => 'Technology',
        'image' => 'https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#2563eb'
    ],
    [
        'name' => 'Fashion & Retail Sale',
        'subject' => 'Flash Sale: Up to 50% Off Top Trends',
        'sector' => 'Retail',
        'image' => 'https://images.pexels.com/photos/934070/pexels-photo-934070.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#be123c'
    ],
    [
        'name' => 'Modern Real Estate Listing',
        'subject' => 'Just Listed: Dream Home in {{location}}',
        'sector' => 'Real Estate',
        'image' => 'https://images.pexels.com/photos/323780/pexels-photo-323780.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#0f172a'
    ],
    [
        'name' => 'Healthcare & Wellness Advice',
        'subject' => '5 Tips for a Healthier You',
        'sector' => 'Healthcare',
        'image' => 'https://images.pexels.com/photos/40568/medical-appointment-doctor-healthcare-40568.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#059669'
    ],
    [
        'name' => 'Education & Online Course',
        'subject' => 'Master a New Skill Today',
        'sector' => 'Education',
        'image' => 'https://images.pexels.com/photos/1181673/pexels-photo-1181673.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#7c3aed'
    ],
    [
        'name' => 'Restaurant & Food Promo',
        'subject' => 'Taste the Magic: New Menu Items',
        'sector' => 'Restaurant',
        'image' => 'https://images.pexels.com/photos/262978/pexels-photo-262978.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#ea580c'
    ],
    [
        'name' => 'Travel & Adventure Deal',
        'subject' => 'Your Next Getaway Awaits',
        'sector' => 'Travel',
        'image' => 'https://images.pexels.com/photos/1371360/pexels-photo-1371360.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#0284c7'
    ],
    [
        'name' => 'Finance & Consulting Update',
        'subject' => 'Market Insights for {{month}}',
        'sector' => 'Finance',
        'image' => 'https://images.pexels.com/photos/159888/pexels-photo-159888.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#1e293b'
    ],
    [
        'name' => 'Fitness & Gym Motivation',
        'subject' => 'Crush Your Goals This Week',
        'sector' => 'Fitness',
        'image' => 'https://images.pexels.com/photos/841130/pexels-photo-841130.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#000000'
    ],
    [
        'name' => 'Non-Profit & Charity Drive',
        'subject' => 'Make a Difference Today',
        'sector' => 'Non-Profit',
        'image' => 'https://images.pexels.com/photos/6994963/pexels-photo-6994963.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'color' => '#166534'
    ]
];

foreach ($templates as $t) {
    $content = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .header { background-color: {$t['color']}; padding: 30px; text-align: center; }
    .header h1 { margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .hero-image { width: 100%; height: auto; display: block; }
    .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
    .content h2 { margin-top: 0; color: {$t['color']}; font-size: 22px; }
    .btn { display: inline-block; background-color: {$t['color']}; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    .footer { background-color: #eeeeee; padding: 20px; text-align: center; font-size: 12px; color: #666666; }
    .social-links { margin-top: 10px; }
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$t['sector']}</h1>
        </div>
        <img src="{$t['image']}" alt="Header Image" class="hero-image">
        <div class="content">
            <h2>Big News!</h2>
            <p>Dear {{subscriber_name}},</p>
            <p>We are excited to share some amazing news specifically curated for our <strong>{$t['sector']}</strong> community. We've been working hard to bring you the best experience possible.</p>
            <p>Check out what's new and take advantage of our exclusive offers designed just for you.</p>
            <br>
            <div style="text-align: center;">
                <a href="#" class="btn">Learn More</a>
            </div>
            <br>
            <p>Best regards,<br><strong>{{company_name}} Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{current_year}} {{company_name}}. All rights reserved.</p>
            <p>123 Business Rd, Tech City, TC 90210</p>
            <p><a href="{{unsubscribe_link}}" style="color: #666;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
HTML;

    // Check if exists to avoid duplicates (optional, based on name)
    $check = $db->prepare("SELECT id FROM cm_templates WHERE name = ? AND tenant_id IS NULL");
    $check->execute([$t['name']]);
    
    if (!$check->fetch()) {
        $stmt = $db->prepare("INSERT INTO cm_templates (tenant_id, name, subject, content) VALUES (NULL, ?, ?, ?)");
        $stmt->execute([$t['name'], $t['subject'], $content]);
        echo "Inserted: {$t['name']}\n";
    } else {
        echo "Skipped (Exists): {$t['name']}\n";
    }
}

echo "Done seeding templates.\n";
