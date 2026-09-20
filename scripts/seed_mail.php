<?php
require_once __DIR__ . '/../app/core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance()->getConnection();

echo "Seeding Mail Templates...\n";

$templates = [
    [
        'name' => 'Casjoe Modern Newsletter',
        'subject' => 'Latest Updates from {{company_name}}',
        'content' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee;">
    <div style="background: #000066; color: white; padding: 20px; text-align: center;">
        <h1>Newsletter</h1>
    </div>
    <div style="padding: 20px;">
        <h2>Hello {{subscriber_name}},</h2>
        <p>Here are the top stories for this week.</p>
        <p>[AI Generated Content Here]</p>
        <a href="#" style="background: #FFA600; color: white; padding: 10px 20px; text-decoration: none; display: inline-block;">Read More</a>
    </div>
    <div style="background: #f4f4f4; padding: 10px; text-align: center; font-size: 12px;">
        <p>&copy; 2024 {{company_name}}</p>
        <a href="{{unsubscribe_url}}">Unsubscribe</a>
    </div>
</div>'
    ],
    [
        'name' => 'Simple Announcement',
        'subject' => 'Important Announcement',
        'content' => '<div style="font-family: sans-serif; padding: 20px;">
    <h2>Big News!</h2>
    <p>Dear {{subscriber_name}},</p>
    <p>We are excited to announce our new feature...</p>
    <br>
    <p>Best,<br>{{company_name}} Team</p>
</div>'
    ]
];

$stmt = $pdo->prepare("INSERT INTO cm_templates (tenant_id, name, subject, content) VALUES (NULL, ?, ?, ?)");

foreach ($templates as $tpl) {
    // Check if exists (by name)
    $chk = $pdo->prepare("SELECT id FROM cm_templates WHERE name = ? AND tenant_id IS NULL");
    $chk->execute([$tpl['name']]);
    if (!$chk->fetch()) {
        $stmt->execute([$tpl['name'], $tpl['subject'], $tpl['content']]);
        echo "Seeded: {$tpl['name']}\n";
    }
}

echo "Template Seeding Complete.\n";
