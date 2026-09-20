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