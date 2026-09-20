<aside class="sidebar">
    <?php include __DIR__ . '/sidebar_mail_css.php'; ?>
    <div class="mail-brand">
        <ion-icon name="mail-outline" style="color: #FFA600; font-size: 1.8rem;"></ion-icon>
        <span>Casjoe Mail</span>
    </div>
    <ul class="mail-menu">
        <li class="mail-item">
            <a href="/mail" class="mail-link <?= $active == 'mail_dashboard' ? 'mail-active' : '' ?>">
                <ion-icon name="grid-outline"></ion-icon> Dashboard
            </a>
        </li>
        <li class="mail-item">
            <a href="/mail/campaigns" class="mail-link <?= $active == 'mail_campaigns' ? 'mail-active' : '' ?>">
                <ion-icon name="paper-plane-outline"></ion-icon> Campaigns
            </a>
        </li>
        <li class="mail-item">
            <a href="/mail/lists" class="mail-link <?= $active == 'mail_audiences' ? 'mail-active' : '' ?>">
                <ion-icon name="people-outline"></ion-icon> Audiences
            </a>
        </li>
        
        <li class="mail-section-label">Marketing</li>

        <li class="mail-item">
            <a href="/mail/sequences" class="mail-link <?= $active == 'mail_automations' ? 'mail-active' : '' ?>">
                <ion-icon name="git-network-outline"></ion-icon> Automations
            </a>
        </li>
        <li class="mail-item">
            <a href="/mail/templates" class="mail-link <?= $active == 'mail_templates' ? 'mail-active' : '' ?>">
                <ion-icon name="color-palette-outline"></ion-icon> Email Templates
            </a>
        </li>
        <li class="mail-item">
            <a href="/mail/forms" class="mail-link <?= $active == 'mail_forms' ? 'mail-active' : '' ?>">
                <ion-icon name="reader-outline"></ion-icon> Signup Forms
            </a>
        </li>

        <li class="mail-section-label">Configuration</li>

        <li class="mail-item">
            <a href="/mail/settings" class="mail-link <?= $active == 'mail_settings' ? 'mail-active' : '' ?>">
                <ion-icon name="settings-outline"></ion-icon> Settings
            </a>
        </li>

        <li class="mail-item" style="margin-top: auto;">
            <a href="/dashboard" class="mail-link">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Apps
            </a>
        </li>
    </ul>
</aside>
