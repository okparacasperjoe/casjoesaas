<h1>Verify Your Email ✉️</h1>

<p>Hi <strong><?= htmlspecialchars($name ?? 'there') ?></strong>,</p>

<p><?= $subtitle ?? 'Thanks for signing up! Please verify your email address to get started managing your business with Casjoe.' ?></p>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?= htmlspecialchars($verifyLink ?? '#') ?>" class="btn">
        Verify My Account →
    </a>
</div>

<div class="highlight-box">
    <p style="margin: 0;"><strong>Can't click the button?</strong><br>
    Copy and paste this link into your browser:</p>
    <p style="margin: 5px 0 0; word-break: break-all;"><a href="<?= htmlspecialchars($verifyLink ?? '#') ?>" style="color: #000066;"><?= htmlspecialchars($verifyLink ?? '#') ?></a></p>
</div>

<p style="color: #999; font-size: 14px;">If you didn't create an account with Casjoe, you can safely ignore this email.</p>
