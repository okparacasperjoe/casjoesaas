<?php if (!isset($_SESSION['user_id'])): ?>
<?php 
    $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
    if (!empty($clientId)):
?>
<!-- Google Identity Services Library -->
<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
     data-client_id="<?= htmlspecialchars($clientId) ?>"
     data-login_uri="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/auth/google/onetap" ?>"
     data-auto_prompt="true"
     data-use_fedcm_for_prompt="true"
     data-itp_support="true"
     data-prompt_parent_id="g_id_onload"
     style="position: fixed; top: 80px; right: 20px; z-index: 10000;">
</div>
<?php endif; ?>
<?php endif; ?>
