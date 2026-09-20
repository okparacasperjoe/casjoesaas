-- ========================================
-- PREMIUM EMAIL TEMPLATES v2.0
-- Complete redesign with:
-- - Professional UI/UX Design
-- - Pexels Hero Images
-- - Social Media Integration
-- - Mobile Responsive
-- - Modern Color Schemes
-- ========================================
TRUNCATE TABLE `cm_templates`;
-- ========================================
-- PROMOTIONAL TEMPLATES (5)
-- ========================================
INSERT INTO `cm_templates` (tenant_id, name, subject, content)
VALUES -- 1. Flash Sale (Modern E-commerce)
    (
        NULL,
        'Flash Sale - Premium',
        '⚡ Flash Sale: Up to 50% Off - 24 Hours Only!',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,''Segoe UI'',Roboto,Helvetica,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
<!-- Hero Image -->
<tr><td><img src="https://images.pexels.com/photos/1055691/pexels-photo-1055691.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="300" style="display:block;width:100%;height:auto;"></td></tr>
<!-- Content -->
<tr><td style="padding:40px;text-align:center;">
<h1 style="color:#e74c3c;font-size:42px;margin:0 0 10px;">FLASH SALE</h1>
<p style="font-size:18px;color:#555;margin:0 0 30px;">24 Hours Only - Don''t Miss Out!</p>
<p style="font-size:16px;line-height:1.6;color:#333;margin:0 0 30px;">Hi {{name}},<br><br>Get ready for massive savings! Enjoy up to 50% off on select items for the next 24 hours. This is your chance to grab what you''ve been eyeing at unbeatable prices.</p>
<a href="{{link}}" style="display:inline-block;background:#e74c3c;color:#fff;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;box-shadow:0 4px 15px rgba(231,76,60,0.3);">Shop Now</a>
<p style="color:#999;font-size:13px;margin:30px 0 0;">Offer expires in 24 hours</p>
</td></tr>
<!-- Footer with Social Links -->
<tr><td style="background:#2c3e50;padding:30px;text-align:center;">
<div style="margin-bottom:20px;">
<a href="https://facebook.com/{{company_name}}" style="display:inline-block;margin:0 8px;"><img src="https://img.icons8.com/fluency/48/facebook-new.png" width="32" height="32" alt="Facebook"></a>
<a href="https://twitter.com/{{company_name}}" style="display:inline-block;margin:0 8px;"><img src="https://img.icons8.com/fluency/48/twitter.png" width="32" height="32" alt="Twitter"></a>
<a href="https://instagram.com/{{company_name}}" style="display:inline-block;margin:0 8px;"><img src="https://img.icons8.com/fluency/48/instagram-new.png" width="32" height="32" alt="Instagram"></a>
<a href="https://linkedin.com/company/{{company_name}}" style="display:inline-block;margin:0 8px;"><img src="https://img.icons8.com/fluency/48/linkedin.png" width="32" height="32" alt="LinkedIn"></a>
</div>
<p style="color:#95a5a6;font-size:14px;margin:0;">&copy; {{year}} {{company_name}}. All rights reserved.</p>
<p style="margin:10px 0 0;"><a href="{{unsubscribe_url}}" style="color:#95a5a6;font-size:12px;">Unsubscribe</a></p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    -- 2. Product Launch (Tech/Modern)
    (
        NULL,
        'Product Launch - Premium',
        '🚀 Introducing {{product_name}} - The Future is Here',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,''Segoe UI'',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
<tr><td><img src="https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="350" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:50px 40px;text-align:center;">
<div style="display:inline-block;background:#667eea;color:#fff;padding:8px 20px;border-radius:20px;font-size:12px;font-weight:bold;margin-bottom:20px;">NEW LAUNCH</div>
<h1 style="color:#1a202c;font-size:36px;margin:0 0 15px;font-weight:800;">Meet {{product_name}}</h1>
<p style="font-size:18px;color:#4a5568;margin:0 0 30px;">Reimagining what''s possible</p>
<p style="font-size:16px;line-height:1.8;color:#2d3748;margin:0 0 30px;text-align:left;">Hey {{name}},<br><br>We''re thrilled to unveil our latest innovation designed to transform the way you work. {{product_name}} combines cutting-edge technology with intuitive design.</p>
<div style="background:#f7fafc;padding:25px;border-radius:8px;margin-bottom:30px;text-align:left;">
<h3 style="color:#2d3748;margin:0 0 15px;font-size:18px;">Key Features:</h3>
<ul style="margin:0;padding-left:20px;color:#4a5568;line-height:1.8;">
<li>Lightning-fast performance</li>
<li>Seamless integrations</li>
<li>AI-powered insights</li>
</ul>
</div>
<a href="{{link}}" style="display:inline-block;background:#667eea;color:#fff;padding:16px 45px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;">Learn More</a>
</td></tr>
<tr><td style="background:#1a202c;padding:30px;text-align:center;">
<div style="margin-bottom:15px;">
<a href="https://facebook.com" style="margin:0 6px;"><img src="https://img.icons8.com/ios-filled/32/ffffff/facebook-new.png" width="28" alt="FB"></a>
<a href="https://twitter.com" style="margin:0 6px;"><img src="https://img.icons8.com/ios-filled/32/ffffff/twitter.png" width="28" alt="TW"></a>
<a href="https://instagram.com" style="margin:0 6px;"><img src="https://img.icons8.com/ios-filled/32/ffffff/instagram-new.png" width="28" alt="IG"></a>
<a href="https://youtube.com" style="margin:0 6px;"><img src="https://img.icons8.com/ios-filled/32/ffffff/youtube-play.png" width="28" alt="YT"></a>
</div>
<p style="color:#a0aec0;font-size:13px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    -- 3. Seasonal Discount (Winter Theme)
    (
        NULL,
        'Winter Sale - Premium',
        '❄️ Winter Wonderland Sale - 30% Off Everything',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#e8f4f8;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadading="0" cellspacing="0" style="background:#fff;border-radius:10px;overflow:hidden;">
<tr><td><img src="https://images.pexels.com/photos/265792/pexels-photo-265792.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="300" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:40px;text-align:center;font-family:Arial,sans-serif;">
<h1 style="color:#0077b6;font-size:40px;margin:0 0 10px;">Winter Wonderland ❄️</h1>
<p style="font-size:22px;color:#023e8a;margin:0 0 25px;font-weight:bold;">30% OFF EVERYTHING</p>
<p style="font-size:16px;line-height:1.7;color:#333;margin:0 0 25px;">Hello {{name}},<br><br>Warm up your winter with hot deals! Use code <strong style="background:#ffe066;padding:3px 8px;border-radius:4px;">WINTER30</strong> at checkout.</p>
<a href="{{link}}" style="display:inline-block;background:#0077b6;color:#fff;padding:15px 40px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:16px;">Start Shopping</a>
<p style="color:#888;font-size:13px;margin:25px 0 0;">Valid until end of season</p>
</td></tr>
<tr><td style="padding:30px;background:#023e8a;text-align:center;">
<div style="margin-bottom:15px;">
<a href="#" style="margin:0 5px;"><img src="https://img.icons8.com/color/40/facebook-new.png" alt="FB"></a>
<a href="#" style="margin:0 5px;"><img src="https://img.icons8.com/color/40/twitter--v1.png" alt="TW"></a>
<a href="#" style="margin:0 5px;"><img src="https://img.icons8.com/color/40/instagram-new--v1.png" alt="IG"></a>
</div>
<p style="color:#90e0ef;font-size:13px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    -- 4. Exclusive VIP Offer
    (
        NULL,
        'VIP Exclusive - Premium',
        '💎 VIP Early Access - Just For You',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#000;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;">
<tr><td><img src="https://images.pexels.com/photos/1342609/pexels-photo-1342609.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="280" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:50px;text-align:center;font-family:Georgia,serif;">
<div style="border:2px solid#d4af37;display:inline-block;padding:10px 25px;margin-bottom:20px;">
<span style="color:#d4af37;font-size:14px;font-weight:bold;letter-spacing:2px;">VIP EXCLUSIVE</span>
</div>
<h1 style="color:#1a1a1a;font-size:38px;margin:0 0 20px;">Early Access</h1>
<p style="font-size:16px;line-height:1.8;color:#333;margin:0 0 30px;">Dear {{name}},<br><br>As a valued VIP member, you get first access to our new collection before it launches to the public. This is your moment to shop without the rush.</p>
<a href="{{link}}" style="display:inline-block;background:#d4af37;color:#000;padding:16px 40px;text-decoration:none;font-weight:bold;letter-spacing:1px;">ENTER VIP SHOP</a>
</td></tr>
<tr><td style="padding:25px;background:#1a1a1a;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#" style="margin:0 6px;"><img src="https://img.icons8.com/ios-glyphs/30/d4af37/facebook-new.png"></a>
<a href="#" style="margin:0 6px;"><img src="https://img.icons8.com/ios-glyphs/30/d4af37/instagram-new.png"></a>
<a href="#" style="margin:0 6px;"><img src="https://img.icons8.com/ios-glyphs/30/d4af37/twitter.png"></a>
</div>
<p style="color:#888;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    -- 5. Review Request
    (
        NULL,
        'Review Request - Premium',
        '⭐ How was your experience with us?',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="padding:40px;text-align:center;font-family:Arial,sans-serif;">
<img src="https://images.pexels.com/photos/6476589/pexels-photo-6476589.jpeg?auto=compress&cs=tinysrgb&w=600&h=200" width="100" height="100" style="border-radius:50%;margin-bottom:20px;">
<h2 style="color:#333;font-size:28px;margin:0 0 15px;">We''d Love Your Feedback!</h2>
<p style="font-size:16px;color:#666;line-height:1.6;margin:0 0 30px;">Hi {{name}},<br><br>Thank you for your recent purchase! Your opinion matters to us. How would you rate your experience?</p>
<div style="margin:25px 0;">
<a href="{{link}}&rating=5" style="font-size:40px;text-decoration:none;margin:0 5px;">⭐</a>
<a href="{{link}}&rating=4" style="font-size:40px;text-decoration:none;margin:0 5px;">⭐</a>
<a href="{{link}}&rating=3" style="font-size:40px;text-decoration:none;margin:0 5px;">⭐</a>
<a href="{{link}}&rating=2" style="font-size:40px;text-decoration:none;margin:0 5px;">⭐</a>
<a href="{{link}}&rating=1" style="font-size:40px;text-decoration:none;margin:0 5px;">⭐</a>
</div>
<p style="color:#999;font-size:13px;">Click a star to rate</p>
</td></tr>
<tr><td style="padding:20px;background:#f1f3f5;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/32/facebook.png"></a>
<a href="#" style="margin-left:10px;"><img src="https://img.icons8.com/color/32/twitter.png"></a>
<a href="#" style="margin-left:10px;"><img src="https://img.icons8.com/color/32/instagram-new.png"></a>
</div>
<p style="color:#6c757d;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    );
-- Continue in next message due to length...
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
-- TRANSACTIONAL, WELCOME & SEASONAL TEMPLATES (Remaining 15)
-- ========================================
-- TRANSACTIONAL TEMPLATES (5)
-- ========================================
INSERT INTO `cm_templates` (tenant_id, name, subject, content)
VALUES (
        NULL,
        'Order Confirmation - Premium',
        '✅ Order #{{order_id}} Confirmed - Thank You!',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9fa;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="background:#28a745;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;font-size:32px;">✅ Order Confirmed!</h1>
</td></tr>
<tr><td><img src="https://images.pexels.com/photos/4483610/pexels-photo-4483610.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="250" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:35px;">
<p style="font-size:18px;color:#333;margin:0 0 10px;">Hi {{name}},</p>
<p style="font-size:16px;color:#666;line-height:1.7;margin:0 0 25px;">Thank you for your order! We''ve received it and are getting it ready for shipment.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:6px;margin-bottom:25px;">
<h3 style="color:#333;margin:0 0 15px;">Order Summary</h3>
<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;">
<tr style="border-bottom:1px solid #dee2e6;">
<th style="text-align:left;color:#666;font-weight:normal;font-size:14px;">Item</th>
<th style="text-align:right;color:#666;font-weight:normal;font-size:14px;">Price</th>
</tr>
<tr style="border-bottom:1px solid #dee2e6;">
<td style="padding:12px 0;color:#333;">{{item_name}}</td>
<td style="text-align:right;color:#333;">{{price}}</td>
</tr>
<tr>
<td style="padding:12px 0;"><strong style="color:#333;">Total</strong></td>
<td style="text-align:right;"><strong style="color:#28a745;font-size:18px;">{{total}}</strong></td>
</tr>
</table>
</div>
<center><a href="{{link}}" style="display:inline-block;background:#28a745;color:#fff;padding:14px 35px;text-decoration:none;border-radius:6px;font-weight:bold;">View Full Order</a></center>
</td></tr>
<tr><td style="padding:25px;background:#212529;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#"><img src="https://img.icons8.com/color/32/facebook.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/32/instagram-new.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/32/twitter.png"></a>
</div>
<p style="color:#adb5bd;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Shipping Notification - Premium',
        '🚚 Your Order is On The Way!',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#e3f2fd;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;">
<tr><td style="padding:40px;text-align:center;">
<img src="https://img.icons8.com/color/96/delivery--v1.png" alt="Delivery" style="margin-bottom:20px;">
<h1 style="color:#0277bd;font-size:32px;margin:0 0 10px;">Package On The Way!</h1>
<p style="color:#64b5f6;font-size:16px;margin:0;">Estimated delivery: {{delivery_date}}</p>
</td></tr>
<tr><td><img src="https://images.pexels.com/photos/4391470/pexels-photo-4391470.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="280" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:35px;">
<p style="font-size:16px;color:#333;line-height:1.7;margin:0 0 25px;">Great news! Your package has been shipped and is on its way to you.</p>
<div style="background:#e3f2fd;padding:20px;border-radius:6px;text-align:center;margin-bottom:25px;">
<p style="color:#01579b;font-size:14px;margin:0 0 8px;">Tracking Number</p>
<p style="font-size:20px;font-weight:bold;color:#0277bd;margin:0;font-family:monospace;">{{tracking_number}}</p>
</div>
<center><a href="{{link}}" style="display:inline-block;background:#0277bd;color:#fff;padding:14px 35px;text-decoration:none;border-radius:30px;font-weight:bold;">Track Package</a></center>
</td></tr>
<tr><td style="padding:20px;background:#01579b;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/instagram-new.png"></a>
</div>
<p style="color:#90caf9;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Invoice Due - Premium',
        '💳 Invoice #{{invoice_id}} - Payment Due',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fff;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="border:2px solid #e0e0e0;border-radius:8px;">
<tr><td style="padding:30px;border-bottom:2px solid #e0e0e0;">
<h2 style="color:#333;margin:0;font-size:24px;">Invoice</h2>
<p style="color:#666;margin:5px 0 0;font-size:14px;">#{{invoice_id}}</p>
</td></tr>
<tr><td style="padding:35px;">
<div style="margin-bottom:25px;">
<p style="color:#666;font-size:14px;margin:0 0 5px;">Amount Due</p>
<p style="color:#d32f2f;font-size:36px;font-weight:bold;margin:0;">{{amount}}</p>
</div>
<div style="background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin-bottom:25px;">
<p style="color:#856404;margin:0;font-size:14px;"><strong>Due Date:</strong> {{due_date}}</p>
</div>
<p style="color:#333;line-height:1.7;margin:0 0 25px;">Please ensure payment is made by the due date to avoid any service interruptions. Click the button below to pay securely online.</p>
<center><a href="{{link}}" style="display:inline-block;background:#d32f2f;color:#fff;padding:15px 40px;text-decoration:none;border-radius:6px;font-weight:bold;">Pay Now</a></center>
</td></tr>
<tr><td style="padding:20px;background:#f5f5f5;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/30/facebook.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/linkedin.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/twitter.png"></a>
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
        'Password Reset - Premium',
        '🔒 Reset Your Password',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="padding:40px;text-align:center;">
<div style="width:80px;height:80px;background:#6366f1;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:25px;">
<img src="https://img.icons8.com/ios-filled/50/ffffff/lock.png" width="40" height="40">
</div>
<h1 style="color:#1f2937;font-size:28px;margin:0 0 15px;">Reset Your Password</h1>
<p style="color:#6b7280;font-size:16px;line-height:1.7;margin:0 0 30px;">We received a request to reset your password. Click the button below to create a new one.</p>
<a href="{{link}}" style="display:inline-block;background:#6366f1;color:#fff;padding:16px 45px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;box-shadow:0 4px 14px rgba(99,102,241,0.3);">Reset Password</a>
<p style="color:#9ca3af;font-size:13px;margin:30px 0 0;">This link will expire in 1 hour</p>
<p style="color:#9ca3af;font-size:13px;margin:10px 0 0;">If you didn''t request this, please ignore this email.</p>
</td></tr>
<tr><td style="padding:25px;background:#1f2937;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/26/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/26/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/26/ffffff/linkedin.png"></a>
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
        'Subscription Renewed - Premium',
        '✅ Your Subscription Has Been Renewed',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;border:1px solid#bbf7d0;">
<tr><td style="padding:35px;text-align:center;">
<div style="width:70px;height:70px;background:#22c55e;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
<span style="color:#fff;font-size:40px;">✓</span>
</div>
<h1 style="color:#166534;font-size:28px;margin:0 0 15px;">Subscription Renewed</h1>
<p style="color:#15803d;font-size:16px;margin:0 0 30px;">Thank you for staying with us!</p>
<div style="background:#f0fdf4;padding:20px;border-radius:6px;margin-bottom:25px;">
<table width="100%" cellpadding="8" cellspacing="0">
<tr>
<td style="color:#166534;font-size:14px;">Plan</td>
<td style="text-align:right;color:#166534;font-weight:bold;">{{plan_name}}</td>
</tr>
<tr>
<td style="color:#166534;font-size:14px;">Next Billing Date</td>
<td style="text-align:right;color:#166534;font-weight:bold;">{{next_billing_date}}</td>
</tr>
<tr>
<td style="color:#166534;font-size:14px;">Amount</td>
<td style="text-align:right;color:#166534;font-weight:bold;">{{amount}}</td>
</tr>
</table>
</div>
<a href="{{link}}" style="display:inline-block;background:#22c55e;color:#fff;padding:14px 32px;text-decoration:none;border-radius:6px;font-weight:bold;">View Billing Details</a>
</td></tr>
<tr><td style="padding:20px;background:#166534;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/instagram-new.png"></a>
</div>
<p style="color:#86efac;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    );
-- Continue in next insert...
