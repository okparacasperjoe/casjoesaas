<script>
    // VAPID Public Key - Should match backend config
    const VAPID_PUBLIC_KEY = 'BJ4ydI3nL2Qj-Gfk6YsAJgcwDpyBGlX_zrnZKKtG9NXhfPWRONs3oWBXuIwdIQefDyb8ZIeURrJ31mxjfabf5xo';

    // Register Service Worker System-Wide
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register('/sw.js');
                console.log('SW registered:', reg.scope);
                
                // Check if already subscribed
                const subscription = await reg.pushManager.getSubscription();
                if (!subscription) {
                    showEnableNotificationUI();
                    // Show "Force" Modal after a short delay
                    setTimeout(showNotificationModal, 2000);
                } else {
                    console.log('User is already subscribed to push notifications');
                    hideEnableNotificationUI();
                }
            } catch (err) {
                console.error('SW registration failed:', err);
            }
        });
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        const rawData = window.atob(base64);
        return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
    }

    async function subscribeToPush() {
        if (!('serviceWorker' in navigator)) {
            alert('Your browser does not support push notifications.');
            return;
        }

        // 1. Explicitly Request Permission
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            alert('Notifications permission was denied. Please enable it in your browser settings to continue.');
            return;
        }

        const reg = await navigator.serviceWorker.ready;
        try {
            // 2. Subscribe
            const subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
            });

            console.log('Subscribed to push:', subscription);

            // 3. Send subscription to backend
            const response = await fetch('/erp/chat/subscribe', {
                method: 'POST',
                body: JSON.stringify(subscription),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            if (result.status === 'success') {
                alert('Notifications enabled successfully!');
                hideEnableNotificationUI();
                closeNotificationModal();
            } else {
                throw new Error(result.message || 'Server error during subscription');
            }
        } catch (err) {
            console.error('Failed to subscribe:', err);
            if (err.name === 'InvalidCharacterError' || err.name === 'NotAllowedError') {
                 alert('Could not enable notifications. Please check your browser settings or try a different browser.');
            } else {
                 alert('An error occurred while setting up notifications. Please contact support.');
            }
        }
    }

    // UI Helper Functions
    function showEnableNotificationUI() {
        const btn = document.getElementById('enable-push-btn');
        if (btn) btn.style.display = 'inline-flex';
    }

    function hideEnableNotificationUI() {
        const btn = document.getElementById('enable-push-btn');
        if (btn) btn.style.display = 'none';
    }

    function showNotificationModal() {
        // Only show if permission is 'default' (not denied)
        if (Notification.permission === 'default') {
            document.getElementById('push-modal').style.display = 'flex';
        }
    }

    function closeNotificationModal() {
        document.getElementById('push-modal').style.display = 'none';
    }
</script>

<!-- Notification Modal -->
<div id="push-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <ion-icon name="notifications" style="font-size: 4rem; color: #000066; margin-bottom: 20px;"></ion-icon>
        <h3 style="color: #333; margin: 0 0 10px 0;">Don't Miss Out!</h3>
        <p style="color: #666; margin-bottom: 25px;">Enable push notifications to stay updated on new messages and alerts instantly.</p>
        
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="closeNotificationModal()" style="padding: 10px 20px; border: 1px solid #ccc; background: white; color: #666; border-radius: 6px; cursor: pointer;">Later</button>
            <button onclick="subscribeToPush()" style="padding: 10px 20px; border: none; background: #000066; color: white; border-radius: 6px; cursor: pointer;">Enable Now</button>
        </div>
    </div>
</div>
