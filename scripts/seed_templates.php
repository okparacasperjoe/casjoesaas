<?php
require 'app/Core/Database.php';
$pdo = App\Core\Database::getInstance()->getConnection();
$tenantId = 1; // Default tenant

echo "--- Seeding Templates ---\n";

$templates = [
    [
        'name' => 'Modern Welcome',
        'subject' => 'Welcome to our community!',
        'content' => '<div style="background:#f4f6f8;padding:40px 0;font-family:sans-serif;">
  <div style="max-width:600px;margin:0 auto;background:white;border-radius:8px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.05);">
    <div style="background:#000066;padding:30px;text-align:center;">
      <h1 style="color:white;margin:0;">Welcome!</h1>
    </div>
    <div style="padding:40px;">
      <h2 style="color:#333;">Hello there,</h2>
      <p style="color:#555;line-height:1.6;">Thanks for joining us! We are thrilled to have you on board. Our goal is to provide you with the best experience possible.</p>
      <div style="text-align:center;margin:30px 0;">
        <a href="#" style="background:#FFA600;color:black;text-decoration:none;padding:12px 25px;border-radius:4px;font-weight:bold;">Get Started</a>
      </div>
      <p style="color:#555;">If you have any questions, feel free to reply to this email.</p>
    </div>
    <div style="background:#f9f9f9;padding:20px;text-align:center;color:#999;font-size:12px;">
      &copy; 2024 Casjoe. All rights reserved.<br>
      <a href="{{unsubscribe_url}}" style="color:#666;">Unsubscribe</a>
    </div>
  </div>
</div>'
    ],
    [
        'name' => 'Monthly Newsletter',
        'subject' => 'This Month\'s Updates',
        'content' => '<div style="background:#fff;font-family:Georgia, serif;color:#333;">
  <div style="max-width:600px;margin:0 auto;border-bottom:4px solid #000066;">
    <div style="padding:30px 0;text-align:center;">
      <h1 style="margin:0;font-family:sans-serif;color:#000066;letter-spacing:1px;">THE NEWSLETTER</h1>
      <p style="margin:10px 0 0;color:#888;font-style:italic;">Issue #42 | December 2025</p>
    </div>
  </div>
  <div style="max-width:600px;margin:30px auto;">
    <h2 style="font-family:sans-serif;">Big News Today</h2>
    <p style="line-height:1.8;">We have some exciting news to share with you this month. Casjoe has introduced several new AI-driven features designed to streamline your business operations.</p>
    <img src="https://via.placeholder.com/600x300" style="width:100%;border-radius:4px;margin:20px 0;" alt="Featured">
    <h3 style="font-family:sans-serif;">Highlights</h3>
    <ul style="line-height:1.8;">
      <li>Feature One implemented successfully.</li>
      <li>Community grew by 200 members.</li>
      <li>New roadmap announced.</li>
    </ul>
    <div style="text-align:center;margin-top:40px;border-top:1px solid #eee;padding-top:20px;">
      <a href="{{unsubscribe_url}}" style="color:#999;text-decoration:none;font-size:12px;font-family:sans-serif;">Unsubscribe from this list</a>
    </div>
  </div>
</div>'
    ],
    [
        'name' => 'Special Offer',
        'subject' => 'Exclusive Deal Inside!',
        'content' => '<div style="background:#111;padding:40px 0;font-family:sans-serif;">
  <div style="max-width:600px;margin:0 auto;background:#222;border-radius:12px;overflow:hidden;color:white;border:1px solid #333;">
    <div style="padding:40px;text-align:center;">
       <span style="background:#FFA600;color:black;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:bold;text-transform:uppercase;">Limited Time</span>
       <h1 style="font-size:42px;margin:20px 0 10px;">50% OFF</h1>
       <p style="color:#ccc;font-size:18px;">On all premium plans this weekend only.</p>
       <div style="margin:40px 0;">
         <a href="#" style="background:#000066;color:white;text-decoration:none;padding:15px 40px;border-radius:50px;font-weight:bold;font-size:18px;border:2px solid #000066;">Claim Offer</a>
       </div>
       <p style="color:#666;font-size:14px;">Offer valid until Sunday midnight.</p>
    </div>
    <div style="background:#1a1a1a;padding:20px;text-align:center;color:#555;font-size:12px;">
      You received this email because you are a VIP member.<br>
      <a href="{{unsubscribe_url}}" style="color:#777;">Unsubscribe</a>
    </div>
  </div>
</div>'
    ]
];

$stmt = $pdo->prepare("INSERT INTO cm_templates (tenant_id, name, subject, content) VALUES (?, ?, ?, ?)");

foreach($templates as $t) {
    // Check duplicates
    $check = $pdo->prepare("SELECT id FROM cm_templates WHERE name = ? AND tenant_id = ?");
    $check->execute([$t['name'], $tenantId]);
    if($check->fetch()) {
        echo "Skipping {$t['name']} (Exists)\n";
        continue;
    }
    
    $stmt->execute([$tenantId, $t['name'], $t['subject'], $t['content']]);
    echo "Inserted: {$t['name']}\n";
}

echo "Done.\n";
