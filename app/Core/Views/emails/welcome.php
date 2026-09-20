<h1>Welcome to Casjoe! 🎉</h1>

<p>Hi <strong><?= htmlspecialchars($name ?? 'there') ?></strong>,</p>

<p>I'm <strong>Casper Joe</strong>, and I'm personally thrilled to welcome you to <strong>Casjoe</strong>.</p>

<p>You’re now inside a complete business ecosystem designed to help you <strong>run your business, sell products, get paid, and scale</strong> — all from one platform.</p>

<div class="highlight-box">
    <h2 style="border-bottom: 2px solid #FFA600; display: inline-block; padding-bottom: 5px; margin-top: 10px;">Start with Casjoe Business School</h2>
    <p>Your first smart move is to go through the <strong>Casjoe Mastery Course</strong> inside Casjoe Business School. This course shows you how to:</p>
    <ul style="line-height: 2.2;">
        <li>Use Casjoe the right way (not trial-and-error)</li>
        <li>Set up your business systems properly</li>
        <li>Activate the modules that actually matter for your goals</li>
        <li>Avoid common mistakes that slow businesses down</li>
    </ul>
    <?php 
    $appUrl = defined('APP_URL') ? APP_URL : 'https://app.casjoe.com';
    $academyLink = $appUrl . "/academy";
    ?>
    <p>👉 <strong>Access Casjoe Business School:</strong> <a href="<?= $academyLink ?>" style="color: #000066; font-weight: bold;">Click Here</a></p>
</div>

<div class="highlight-box" style="margin-top: 25px;">
    <h2 style="border-bottom: 2px solid #FFA600; display: inline-block; padding-bottom: 5px; margin-top: 10px;">Earn by Referring Others</h2>
    <p>Casjoe rewards growth. You have a personal referral link. When people sign up using your link and start using Casjoe, <strong>you earn rewards</strong>.</p>
    <ul style="line-height: 2.2;">
        <li>Business owners</li>
        <li>Vendors</li>
        <li>Teams</li>
        <li>Anyone who wants to build or grow a business</li>
    </ul>
</div>

<div style="text-align: center; margin-top: 25px;">
    <a href="<?= $dashboardUrl ?? 'https://app.casjoe.com/dashboard' ?>" class="btn">
        Go to Dashboard →
    </a>
</div>

<p>If you ever feel stuck, reach out through our support channels inside the app.</p>

<p>Build smart. Learn fast. Grow with systems.</p>

<p style="margin-top: 30px;">
    <strong>Best Regards,</strong><br>
    <strong>Casper Joe Okpara</strong><br>
    CEO, Casjoe LLC
</p>
