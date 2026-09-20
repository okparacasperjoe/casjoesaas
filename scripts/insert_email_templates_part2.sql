-- Continuation: Adding remaining 20 templates with premium UI
-- ========================================
-- NEWSLETTER TEMPLATES (5)
-- ========================================
INSERT INTO `cm_templates` (tenant_id, name, subject, content)
VALUES (
        NULL,
        'Weekly Digest - Premium',
        '📰 Your Weekly Roundup from {{company_name}}',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eceff1;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;">
<tr><td style="background:#263238;padding:25px;text-align:center;">
<h1 style="color:#fff;margin:0;font-size:28px;">{{company_name}} DIGEST</h1>
<p style="color:#90a4ae;margin:5px 0 0;font-size:14px;">Your Weekly Newsletter</p>
</td></tr>
<tr><td><img src="https://images.pexels.com/photos/1591062/pexels-photo-1591062.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="250" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:35px;">
<h2 style="color:#263238;font-size:24px;margin:0 0 15px;">This Week''s Highlights</h2>
<div style="border-left:4px solid #00acc1;padding-left:15px;margin-bottom:25px;">
<h3 style="color:#00acc1;margin:0 0 8px;font-size:18px;">Featured Story</h3>
<p style="color:#546e7a;line-height:1.7;margin:0;">Discover how AI is transforming the future of work. Our latest research reveals surprising insights...</p>
</div>
<div style="background:#f5f5f5;padding:20px;border-radius:6px;margin-bottom:20px;">
<h4 style="color:#263238;margin:0 0 10px;">Quick Reads:</h4>
<ul style="color:#546e7a;line-height:1.8;margin:0;">
<li>5 Productivity Hacks You Need to Try</li>
<li>Customer Success Story: Acme Corp</li>
<li>Product Update: New Features This Week</li>
</ul>
</div>
<center><a href="{{link}}" style="display:inline-block;background:#00acc1;color:#fff;padding:14px 35px;text-decoration:none;border-radius:4px;font-weight:bold;">Read Full Newsletter</a></center>
</td></tr>
<tr><td style="padding:25px;background:#263238;text-align:center;">
<div style="margin-bottom:15px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/linkedin.png"></a>
<a href="#" style="margin:0 0 0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/youtube-play.png"></a>
</div>
<p style="color:#90a4ae;font-size:12px;margin:0;">&copy; {{year}} {{company_name}} | <a href="{{unsubscribe_url}}" style="color:#90a4ae;">Unsubscribe</a></p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'CEO Update - Premium',
        '💼 Message from our CEO',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fff;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0">
<tr><td style="padding:30px 0;text-align:center;">
<img src="{{logo_url}}" style="height:45px;" alt="Logo">
</td></tr>
<tr><td><img src="https://images.pexels.com/photos/3183197/pexels-photo-3183197.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="300" style="display:block;width:100%;border-radius:8px;"></td></tr>
<tr><td style="padding:40px 30px;font-family:Georgia,serif;">
<div style="border-bottom:2px solid#000;padding-bottom:15px;margin-bottom:25px;">
<small style="color:#999;font-size:12px;text-transform:uppercase;letter-spacing:1px;">From the CEO''s Desk</small>
<h1 style="color:#000;margin:8px 0 0;font-size:32px;">An Important Update</h1>
</div>
<p style="color:#333;font-size:16px;line-height:1.9;margin:0 0 20px;">Dear Team and Partners,</p>
<p style="color:#333;font-size:16px;line-height:1.9;margin:0 0 20px;">I wanted to personally share our vision for the upcoming quarter. We''re embarking on an exciting journey that will redefine our industry...</p>
<p style="color:#333;font-size:16px;line-height:1.9;margin:0 0 20px;"><strong>Our 3 Key Focus Areas:</strong></p>
<ol style="color:#333;font-size:16px;line-height:1.9;">
<li>Innovation & Product Excellence</li>
<li>Customer-Centric Growth</li>
<li>Sustainable Operations</li>
</ol>
<p style="color:#333;font-size:16px;line-height:1.9;margin:25px 0 0;">Best regards,<br><strong>CEO Name</strong></p>
</td></tr>
<tr><td style="padding:20px;background:#f8f8f8;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#"><img src="https://img.icons8.com/color/36/facebook.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/36/twitter.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/36/linkedin.png"></a>
</div>
<p style="color:#777;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Industry Insights - Premium',
        '💡 Industry Trends Report',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#667eea,#764ba2);padding:45px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;overflow:hidden;">
<tr><td style="padding:40px 35px;text-align:center;">
<div style="display:inline-block;background:#667eea;color:#fff;padding:6px 18px;border-radius:20px;font-size:11px;font-weight:bold;margin-bottom:20px;">INDUSTRY INSIGHT</div>
<h1 style="color:#1a202c;font-size:32px;margin:0 0 12px;">The Future of Work</h1>
<p style="color:#718096;font-size:14px;margin:0;">2026 Trends Report</p>
</td></tr>
<tr><td><img src="https://images.pexels.com/photos/3184292/pexels-photo-3184292.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="320" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:35px;">
<p style="font-size:17px;line-height:1.8;color:#2d3748;margin:0 0 25px;">Did you know that <strong>78% of businesses</strong> are transitioning to hybrid work models? Here''s what this means for your industry.</p>
<div style="background:#f7fafc;padding:25px;border-left:4px solid #667eea;margin-bottom:25px;">
<h3 style="color:#2d3748;margin:0 0 12px;font-size:18px;">Key Takeaways:</h3>
<ul style="color:#4a5568;line-height:1.8;margin:0;">
<li>Remote collaboration tools up 340%</li>
<li>AI adoption accelerating across sectors</li>
<li>Sustainability becoming a priority</li>
</ul>
</div>
<center><a href="{{link}}" style="display:inline-block;background:#667eea;color:#fff;padding:15px 35px;text-decoration:none;border-radius:6px;font-weight:bold;">Download Full Report</a></center>
</td></tr>
<tr><td style="padding:25px;background:#1a202c;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#"><img src="https://img.icons8.com/ios-glyphs/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin-left:10px;"><img src="https://img.icons8.com/ios-glyphs/28/ffffff/twitter.png"></a>
<a href="#" style="margin-left:10px;"><img src="https://img.icons8.com/ios-glyphs/28/ffffff/linkedin.png"></a>
</div>
<p style="color:#a0aec0;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Product Tips - Premium',
        '💡 Pro Tips: Get More from {{product_name}}',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="background:#10b981;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;font-size:30px;">💡 Pro Tip Tuesday</h1>
</td></tr>
<tr><td><img src="https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="280" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:35px;">
<h2 style="color:#1f2937;font-size:24px;margin:0 0 15px;">Did You Know?</h2>
<p style="font-size:16px;line-height:1.7;color:#4b5563;margin:0 0 25px;">You can automate your entire workflow using our keyboard shortcuts! Press <code style="background:#f3f4f6;padding:3px 8px;border-radius:4px;color:#10b981;font-weight:bold;">Ctrl + K</code> to access the command palette.</p>
<div style="background:#f9fafb;padding:20px;border-radius:6px;margin-bottom:25px;">
<h4 style="color:#1f2937;margin:0 0 12px;">Watch This 2-Min Tutorial:</h4>
<a href="{{link}}"><img src="https://images.pexels.com/photos/265685/pexels-photo-265685.jpeg?auto=compress&cs=tinysrgb&w=600&h=300" width="100%" style="border-radius:4px;"></a>
</div>
<center><a href="{{link}}" style="display:inline-block;background:#10b981;color:#fff;padding:14px 32px;text-decoration:none;border-radius:6px;font-weight:bold;">View All Tips</a></center>
</td></tr>
<tr><td style="padding:20px;background:#1f2937;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/32/facebook.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/32/twitter.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/32/youtube.png"></a>
</div>
<p style="color:#9ca3af;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Curated Content - Premium',
        '❤️ 5 Things We Love This Week',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#ffe5e5;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;">
<tr><td style="padding:40px;text-align:center;font-family:Arial,sans-serif;">
<h1 style="color:#ec4899;font-size:36px;margin:0 0 10px;">❤️ Our Favorites</h1>
<p style="color:#9ca3af;font-size:14px;margin:0 0 30px;">Curated just for you</p>
</td></tr>
<tr><td style="padding:0 35px 35px;">
<div style="border-bottom:2px solid #fecaca;margin-bottom:20px;padding-bottom:20px;">
<img src="https://images.pexels.com/photos/1181467/pexels-photo-1181467.jpeg?auto=compress&cs=tinysrgb&w=600&h=200" width="100%" style="border-radius:6px;display:block;margin-bottom:15px;">
<h3 style="color:#1f2937;margin:0 0 8px;">📚 Read: "The Art of Deep Work"</h3>
<p style="color:#6b7280;margin:0;">A must-read for anyone looking to boost productivity.</p>
</div>
<div style="border-bottom:2px solid #fecaca;margin-bottom:20px;padding-bottom:20px;">
<h3 style="color:#1f2937;margin:0 0 8px;">🛠️ Tool: Notion Templates</h3>
<p style="color:#6b7280;margin:0;">Beautiful pre-made templates to organize your life.</p>
</div>
<div style="border-bottom:2px solid #fecaca;margin-bottom:20px;padding-bottom:20px;">
<h3 style="color:#1f2937;margin:0 0 8px;">🎧 Podcast: Tech Talks Daily</h3>
<p style="color:#6b7280;margin:0;">Latest episode on AI breakthroughs.</p>
</div>
<div style="border-bottom:2px solid #fecaca;margin-bottom:20px;padding-bottom:20px;">
<h3 style="color:#1f2937;margin:0 0 8px;">💡 Quote of the Week</h3>
<p style="color:#6b7280;font-style:italic;margin:0;">"Ship it before it''s perfect." - Seth Godin</p>
</div>
<div>
<h3 style="color:#1f2937;margin:0 0 8px;">🎨 Design: Dribbble Shots</h3>
<p style="color:#6b7280;margin:0;">Inspiring UI designs from this week.</p>
</div>
</td></tr>
<tr><td style="padding:20px;background:#1f2937;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/30/facebook.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/instagram-new.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/twitter.png"></a>
</div>
<p style="color:#9ca3af;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    );
-- Continue with the remaining templates...