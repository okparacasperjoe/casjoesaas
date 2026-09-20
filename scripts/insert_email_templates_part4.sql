-- FINAL 10 TEMPLATES: WELCOME/ONBOARDING (5) + SEASONAL/EVENT (5)
-- ========================================
-- WELCOME/ONBOARDING TEMPLATES (5)
-- ========================================
INSERT INTO `cm_templates` (tenant_id, name, subject, content)
VALUES (
        NULL,
        'Welcome Email - Premium',
        '🎉 Welcome to {{company_name}}!',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#667eea,#764ba2);padding:50px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;">
<tr><td><img src="https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="300" style="display:block;width:100%;border-radius:12px 12px 0 0;"></td></tr>
<tr><td style="padding:45px 40px;text-align:center;">
<h1 style="color:#1a202c;font-size:36px;margin:0 0 15px;">Welcome Aboard! 🎉</h1>
<p style="font-size:18px;color:#4a5568;margin:0 0 30px;">We''re thrilled to have you, {{name}}!</p>
<p style="font-size:16px;line-height:1.8;color:#2d3748;margin:0 0 30px;text-align:left;">Your journey with {{company_name}} starts now. We''ve built tools to help you succeed, and we''re here to support you every step of the way.</p>
<div style="background:#f7fafc;padding:25px;border-radius:8px;margin-bottom:30px;">
<h3 style="color:#2d3748;margin:0 0 15px;">Get Started in 3 Easy Steps:</h3>
<ol style="text-align:left;color:#4a5568;line-height:1.9;margin:0;">
<li>Complete your profile setup</li>
<li>Explore the dashboard</li>
<li>Invite your team</li>
</ol>
</div>
<a href="{{link}}" style="display:inline-block;background:#667eea;color:#fff;padding:16px 40px;text-decoration:none;border-radius:50px;font-weight:bold;font-size:16px;">Get Started</a>
</td></tr>
<tr><td style="padding:25px;background:#1a202c;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/linkedin.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/instagram-new.png"></a>
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
        'Getting Started Guide - Premium',
        '🚀 Your Quick Start Guide',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;">
<tr><td style="padding:40px 35px;">
<h1 style="color:#1f2937;font-size:30px;margin:0 0 10px;">Getting Started is Easy</h1>
<p style="color:#6b7280;font-size:16px;margin:0 0 30px;">Follow these steps to set up your account</p>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="33%" style="padding:15px;background:#dbeafe;border-radius:6px 0 0 6px;text-align:center;">
<div style="background:#3b82f6;width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;"><span style="color:#fff;font-weight:bold;">1</span></div>
<p style="color:#1e40af;margin:0;font-size:14px;font-weight:bold;">Create Profile</p>
</td>
<td width="33%" style="padding:15px;background:#dbeafe;text-align:center;">
<div style="background:#3b82f6;width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;"><span style="color:#fff;font-weight:bold;">2</span></div>
<p style="color:#1e40af;margin:0;font-size:14px;font-weight:bold;">Upload Data</p>
</td>
<td width="33%" style="padding:15px;background:#dbeafe;border-radius:0 6px 6px 0;text-align:center;">
<div style="background:#3b82f6;width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;"><span style="color:#fff;font-weight:bold;">3</span></div>
<p style="color:#1e40af;margin:0;font-size:14px;font-weight:bold;">Launch!</p>
</td>
</tr>
</table>
<div style="margin-top:30px;text-align:center;">
<a href="{{link}}" style="display:inline-block;background:#3b82f6;color:#fff;padding:15px 35px;text-decoration:none;border-radius:6px;font-weight:bold;">Start Tutorial</a>
</div>
</td></tr>
<tr><td style="padding:20px;background:#1f2937;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/30/facebook.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/twitter.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/linkedin.png"></a>
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
        'Community Invite - Premium',
        '💬 Join Our Community',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#5865F2;padding:50px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;">
<tr><td><img src="https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="280" style="display:block;width:100%;border-radius:10px 10px 0 0;"></td></tr>
<tr><td style="padding:45px 40px;text-align:center;">
<h1 style="color:#1f2937;font-size:34px;margin:0 0 15px;">Join the Conversation 💬</h1>
<p style="font-size:17px;color:#4b5563;line-height:1.7;margin:0 0 30px;">Connect with thousands of like-minded individuals, share ideas, and grow together in our exclusive community.</p>
<div style="background:#eef2ff;padding:25px;border-radius:8px;margin-bottom:30px;">
<p style="color:#1e40af;margin:0;font-size:15px;line-height:1.7;"><strong>Benefits of Joining:</strong><br>✓ Expert advice & support<br>✓ Exclusive resources<br>✓ Networking opportunities</p>
</div>
<a href="{{link}}" style="display:inline-block;background:#5865F2;color:#fff;padding:16px 45px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:16px;box-shadow:0 4px 14px rgba(88,101,242,0.4);">Join Discord Community</a>
</td></tr>
<tr><td style="padding:25px;background:#23272a;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/discord-logo.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/github.png"></a>
</div>
<p style="color:#b9bbbe;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Founder Note - Premium',
        '✉️ A Personal Note from Our Founder',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fdfaf6;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0">
<tr><td style="padding:40px;background:#fff;border-radius:8px;border:1px solid #e7e5e4;">
<img src="https://images.pexels.com/photos/1181690/pexels-photo-1181690.jpeg?auto=compress&cs=tinysrgb&w=150&h=150" width="80" height="80" style="border-radius:50%;display:block;margin:0 auto 20px;">
<h2 style="color:#1c1917;font-size:26px;text-align:center;margin:0 0 25px;font-family:Georgia,serif;">A Personal Welcome</h2>
<p style="font-size:17px;line-height:1.9;color:#44403c;margin:0 0 20px;font-family:Georgia,serif;">Hi {{name}},</p>
<p style="font-size:17px;line-height:1.9;color:#44403c;margin:0 0 20px;font-family:Georgia,serif;">I wanted to personally welcome you to our community. When I started this journey, my goal was simple: create something that genuinely helps people succeed.</p>
<p style="font-size:17px;line-height:1.9;color:#44403c;margin:0 0 20px;font-family:Georgia,serif;">Your feedback and ideas matter to us. Feel free to reply to this email anytime—I read every message.</p>
<p style="font-size:17px;line-height:1.9;color:#44403c;margin:0 0 5px;font-family:Georgia,serif;">Cheers,</p>
<p style="font-size:17px;color:#44403c;margin:0;font-family:Georgia,serif;"><strong>Founder Name</strong><br><small style="color:#78716c;">Founder & CEO</small></p>
</td></tr>
<tr><td style="padding:20px 0;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/32/linkedin.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/32/twitter.png"></a>
</div>
<p style="color:#78716c;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Setup Checklist - Premium',
        '✅ Your Setup Checklist',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;border:2px solid #86efac;">
<tr><td style="padding:40px 35px;">
<h1 style="color:#166534;font-size:30px;margin:0 0 10px;">Setup Checklist ✅</h1>
<p style="color:#16a34a;font-size:16px;margin:0 0 30px;">Complete these steps to get the most out of your account</p>
<div style="border-bottom:2px solid #dcfce7;padding-bottom:18px;margin-bottom:18px;">
<div style="display:flex;align-items:center;margin-bottom:15px;">
<div style="width:32px;height:32px;background:#22c55e;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-right:15px;"><span style="color:#fff;font-size:18px;">✓</span></div>
<div><p style="color:#166534;margin:0;font-size:16px;"><strong>Account Created</strong></p></div>
</div>
</div>
<div style="border-bottom:2px solid #dcfce7;padding-bottom:18px;margin-bottom:18px;">
<div style="display:flex;align-items:center;">
<div style="width:32px;height:32px;background:#e5e7eb;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-right:15px;"><span style="color:#6b7280;">⬜</span></div>
<div><p style="color:#4b5563;margin:0;font-size:16px;">Upload Profile Photo</p></div>
</div>
</div>
<div style="border-bottom:2px solid #dcfce7;padding-bottom:18px;margin-bottom:18px;">
<div style="display:flex;align-items:center;">
<div style="width:32px;height:32px;background:#e5e7eb;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-right:15px;"><span style="color:#6b7280;">⬜</span></div>
<div><p style="color:#4b5563;margin:0;font-size:16px;">Invite Team Members</p></div>
</div>
</div>
<div style="padding-bottom:18px;">
<div style="display:flex;align-items:center;">
<div style="width:32px;height:32px;background:#e5e7eb;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-right:15px;"><span style="color:#6b7280;">⬜</span></div>
<div><p style="color:#4b5563;margin:0;font-size:16px;">Connect Integrations</p></div>
</div>
</div>
<div style="margin-top:30px;text-align:center;">
<a href="{{link}}" style="display:inline-block;background:#22c55e;color:#fff;padding:15px 35px;text-decoration:none;border-radius:6px;font-weight:bold;">Continue Setup</a>
</div>
</td></tr>
<tr><td style="padding:20px;background:#166534;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/26/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/26/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/26/ffffff/linkedin.png"></a>
</div>
<p style="color:#86efac;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    );
-- ========================================
-- SEASONAL/EVENT TEMPLATES (5)
-- ========================================
INSERT INTO `cm_templates` (tenant_id, name, subject, content)
VALUES (
        NULL,
        'Happy New Year - Premium',
        '🎉 Happy New Year 2026!',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#1e3a8a,#312e81);padding:60px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0">
<tr><td><img src="https://images.pexels.com/photos/1679618/pexels-photo-1679618.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="320" style="display:block;width:100%;border-radius:12px;"></td></tr>
<tr><td style="padding:50px 40px;text-align:center;background:#fff;border-radius:12px;margin-top:-10px;">
<h1 style="color:#1e3a8a;font-size:56px;margin:0 0 10px;font-weight:800;">2026</h1>
<h2 style="color:#312e81;font-size:32px;margin:0 0 20px;">Happy New Year!</h2>
<p style="font-size:18px;line-height:1.8;color:#475569;margin:0;">Wishing you a year filled with success, growth, and endless possibilities. Thank you for being part of our journey.</p>
<p style="margin:30px 0 0;color:#64748b;font-size:16px;">— The {{company_name}} Team</p>
</td></tr>
<tr><td style="padding:25px 0;text-align:center;">
<div style="margin-bottom:12px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/linkedin.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/instagram-new.png"></a>
</div>
<p style="color:#e0e7ff;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Holiday Greetings - Premium',
        '🎄 Season''s Greetings',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#7f1d1d;padding:45px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:10px;">
<tr><td><img src="https://images.pexels.com/photos/1661021/pexels-photo-1661021.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="300" style="display:block;width:100%;border-radius:10px 10px 0 0;"></td></tr>
<tr><td style="padding:45px 40px;text-align:center;">
<h1 style="color:#7f1d1d;font-size:38px;margin:0 0 15px;font-family:Georgia,serif;">Season''s Greetings 🎄</h1>
<p style="font-size:17px;line-height:1.8;color:#44403c;margin:0 0 25px;">Warmest thoughts and best wishes for a wonderful holiday season and a very happy New Year. May peace, love, and prosperity follow you always.</p>
<p style="color:#78716c;font-size:15px;margin:0;">With gratitude,<br><strong>{{company_name}} Team</strong></p>
</td></tr>
<tr><td style="padding:25px;background:#7f1d1d;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/instagram-new.png"></a>
</div>
<p style="color:#fca5a5;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Webinar Invite - Premium',
        '📅 You''re Invited: Live Webinar Tomorrow',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;border-top:5px solid #3b82f6;">
<tr><td><img src="https://images.pexels.com/photos/3184292/pexels-photo-3184292.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="280" style="display:block;width:100%;"></td></tr>
<tr><td style="padding:35px;">
<div style="background:#dbeafe;color:#1e40af;padding:6px 15px;border-radius:20px;display:inline-block;font-size:12px;font-weight:bold;margin-bottom:20px;">LIVE WEBINAR</div>
<h1 style="color:#1f2937;font-size:28px;margin:0 0 15px;">Mastering Productivity in 2026</h1>
<div style="border-left:4px solid #3b82f6;padding-left:20px;margin:20px 0;">
<p style="color:#4b5563;margin:0 0 8px;font-size:15px;"><strong>📅 When:</strong> Tomorrow at 2:00 PM PST</p>
<p style="color:#4b5563;margin:0 0 8px;font-size:15px;"><strong>⏱️ Duration:</strong> 60 minutes</p>
<p style="color:#4b5563;margin:0;font-size:15px;"><strong>👥 Speakers:</strong> Industry Expert Panel</p>
</div>
<p style="color:#6b7280;font-size:16px;line-height:1.7;margin:20px 0;">Learn proven strategies to double your output, eliminate distractions, and achieve work-life balance from our expert panel.</p>
<div style="text-align:center;margin-top:30px;">
<a href="{{link}}" style="display:inline-block;background:#3b82f6;color:#fff;padding:16px 40px;text-decoration:none;border-radius:6px;font-weight:bold;font-size:16px;box-shadow:0 4px 12px rgba(59,130,246,0.3);">Save My Spot</a>
</div>
</td></tr>
<tr><td style="padding:20px;background:#1f2937;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/color/30/linkedin.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/twitter.png"></a>
<a href="#" style="margin-left:8px;"><img src="https://img.icons8.com/color/30/youtube.png"></a>
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
        'Birthday - Premium',
        '🎂 Happy Birthday, {{name}}!',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#ec4899,#a855f7);padding:50px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;">
<tr><td><img src="https://images.pexels.com/photos/1729797/pexels-photo-1729797.jpeg?auto=compress&cs=tinysrgb&w=600" width="600" height="300" style="display:block;width:100%;border-radius:12px 12px 0 0;"></td></tr>
<tr><td style="padding:45px 40px;text-align:center;">
<h1 style="color:#ec4899;font-size:48px;margin:0 0 10px;">Happy Birthday! 🎉</h1>
<p style="font-size:18px;color:#4b5563;margin:0 0 30px;">Wishing you a fantastic day filled with joy and celebration!</p>
<p style="font-size:16px;color:#6b7280;line-height:1.7;margin:0 0 30px;">To make your day extra special, here''s a little gift from us:</p>
<div style="background:#fdf2f8;border:3px dashed #ec4899;padding:25px;border-radius:8px;display:inline-block;margin-bottom:30px;">
<p style="color:#ec4899;font-size:32px;font-weight:bold;margin:0 0 5px;font-family:monospace;">BDAY20</p>
<p style="color:#be185d;margin:0;font-size:14px;">20% Off Your Next Order</p>
</div>
<a href="{{link}}" style="display:inline-block;background:#ec4899;color:#fff;padding:15px 35px;text-decoration:none;border-radius:30px;font-weight:bold;">Claim Gift</a>
</td></tr>
<tr><td style="padding:25px;background:#831843;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/28/ffffff/instagram-new.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/28/ffffff/twitter.png"></a>
</div>
<p style="color:#fbcfe8;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    ),
    (
        NULL,
        'Event Reminder - Premium',
        '⏰ Reminder: Event Starts in 1 Hour',
        '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#fef3c7;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;border:2px solid #f59e0b;">
<tr><td style="padding:35px;text-align:center;">
<div style="width:80px;height:80px;background:#f59e0b;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:20px;">
<span style="color:#fff;font-size:40px;">⏰</span>
</div>
<h1 style="color:#78350f;font-size:30px;margin:0 0 10px;">Don''t Forget!</h1>
<p style="color:#92400e;font-size:18px;margin:0 0 25px;">Your event is starting soon</p>
<div style="background:#fef3c7;padding:20px;border-radius:6px;margin-bottom:25px;">
<p style="color:#78350f;margin:0 0 10px;font-size:16px;"><strong>{{event_name}}</strong></p>
<p style="color:#92400e;margin:0;font-size:14px;">Starts in approximately 1 hour</p>
</div>
<a href="{{link}}" style="display:inline-block;background:#f59e0b;color:#fff;padding:16px 40px;text-decoration:none;border-radius:6px;font-weight:bold;font-size:17px;">Join Now →</a>
<p style="color:#a16207;font-size:13px;margin:25px 0 0;">See you there!</p>
</td></tr>
<tr><td style="padding:20px;background:#78350f;text-align:center;">
<div style="margin-bottom:10px;">
<a href="#"><img src="https://img.icons8.com/ios-filled/26/ffffff/facebook-new.png"></a>
<a href="#" style="margin:0 8px;"><img src="https://img.icons8.com/ios-filled/26/ffffff/twitter.png"></a>
<a href="#"><img src="https://img.icons8.com/ios-filled/26/ffffff/calendar.png"></a>
</div>
<p style="color:#fde68a;font-size:12px;margin:0;">&copy; {{year}} {{company_name}}</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>'
    );