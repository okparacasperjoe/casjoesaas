<aside class="sidebar">
    <?php include __DIR__ . '/sidebar_mail_css.php'; ?>
    <div class="mail-brand">
        <ion-icon name="mail-outline" style="color: #FFA600; font-size: 1.8rem;"></ion-icon>
        <span>Casjoe Mail</span>
    </div>
    <ul class="mail-menu">
        <li class="mail-item"><a href="/mail" class="mail-link <?= $active == 'dashboard' ? 'mail-active' : '' ?>"><ion-icon name="speedometer-outline"></ion-icon> Dashboard</a></li>
        <li class="mail-item"><a href="/mail/campaigns" class="mail-link <?= $active == 'campaigns' ? 'mail-active' : '' ?>"><ion-icon name="paper-plane-outline"></ion-icon> Campaigns</a></li>
        <li class="mail-item"><a href="/mail/sequences" class="mail-link <?= $active == 'sequences' ? 'mail-active' : '' ?>"><ion-icon name="git-network-outline"></ion-icon> Sequences</a></li>
        <li class="mail-item"><a href="/mail/lists" class="mail-link <?= $active == 'lists' ? 'mail-active' : '' ?>"><ion-icon name="people-outline"></ion-icon> Subscribers</a></li>
        <li class="mail-item"><a href="/mail/forms" class="mail-link <?= $active == 'forms' ? 'mail-active' : '' ?>"><ion-icon name="clipboard-outline"></ion-icon> Signup Forms</a></li>
        <li class="mail-item"><a href="/mail/templates" class="mail-link <?= $active == 'templates' ? 'mail-active' : '' ?>"><ion-icon name="duplicate-outline"></ion-icon> Templates</a></li>
        <li class="mail-item"><a href="/mail/settings" class="mail-link <?= $active == 'settings' ? 'mail-active' : '' ?>"><ion-icon name="settings-outline"></ion-icon> Settings</a></li>
        
        <li class="mail-section-label">AI Agent</li>
        <li class="mail-item"><a href="/ai-office" class="mail-link" style="border-left: 3px solid #FFA600; background: rgba(255,166,0,0.05);"><ion-icon name="briefcase"></ion-icon> AI Office</a></li>
        
        <li class="mail-item" style="margin-top: auto;"><a href="/dashboard" class="mail-link"><ion-icon name="arrow-back-outline"></ion-icon> Back to Apps</a></li>
    </ul>
</aside>
