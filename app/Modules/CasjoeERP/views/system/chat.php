<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Chat | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <style>
        .chat-container { display: flex; flex-direction: column; height: calc(100vh - 120px); background: #fff; border-radius: 10px; border: 1px solid #ddd; overflow: hidden; }
        .chat-messages { flex: 1; padding: 20px; overflow-y: auto; background: #f9f9f9; display: flex; flex-direction: column; gap: 10px; }
        .message { max-width: 70%; padding: 10px 15px; border-radius: 10px; position: relative; font-size: 0.95rem; }
        .message.sent { align-self: flex-end; background: #e3f2fd; color: #0d47a1; border-bottom-right-radius: 2px; }
        .message.received { align-self: flex-start; background: #fff; border: 1px solid #eee; border-bottom-left-radius: 2px; }
        .message .meta { font-size: 0.75rem; color: #999; margin-top: 5px; display: block; text-align: right; }
        .message.received .meta { text-align: left; }
        .chat-input-area { padding: 15px; background: #fff; border-top: 1px solid #eee; display: flex; gap: 10px; }
        .chat-input-area input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; transition: border 0.3s; }
        .chat-input-area input:focus { border-color: var(--secondary); }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="top-bar">
            <h2>Team Chat</h2>
            <button id="enable-push" class="btn" style="background: #666; font-size: 0.8rem;">Enable Notifications</button>
        </div>

        <div class="chat-container">
            <div class="chat-messages" id="chat-box">
                <!-- Messages will load here -->
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= $msg['user_id'] == $user['id'] ? 'sent' : 'received' ?>">
                        <strong style="font-size:0.8rem; color:#ec407a;"><?= htmlspecialchars($msg['sender_name']) ?></strong><br>
                        <?= htmlspecialchars($msg['message']) ?>
                        <span class="meta"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="chat-input-area">
                <input type="text" id="message-input" placeholder="Type a message..." autocomplete="off">
                <button class="btn" id="send-btn">Send</button>
            </div>
        </div>
    </main>
</div>

<script>
    const chatBox = document.getElementById('chat-box');
    const msgInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const pushBtn = document.getElementById('enable-push');
    let lastId = <?= end($messages)['id'] ?? 0 ?>;

    // Scroll to bottom
    chatBox.scrollTop = chatBox.scrollHeight;

    // Send Message
    async function sendMessage() {
        const text = msgInput.value.trim();
        if (!text) return;

        msgInput.value = '';
        
        // Optimistic UI Update
        const myName = "<?= htmlspecialchars($user['name'] ?? 'Me') ?>";
        appendMessage(text, myName, true);

        try {
            const formData = new FormData();
            formData.append('message', text);
            await fetch('/erp/chat/send', { method: 'POST', body: formData });
        } catch (e) {
            console.error('Send failed', e);
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    msgInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });

    function appendMessage(text, sender, isMe) {
        const div = document.createElement('div');
        div.className = `message ${isMe ? 'sent' : 'received'}`;
        let html = '';
        // Always show sender name as requested
        html += `<strong style="font-size:0.8rem; color:#ec407a;">${sender}</strong><br>`;
        html += `${text.replace(/</g, "&lt;")}<span class="meta">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
        div.innerHTML = html;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Pusher Real-time Logic
    const pusher = new Pusher('002937f554e53a1a6371', {
        cluster: 'mt1'
    });

    const channel = pusher.subscribe('chat-<?= \App\Core\TenantContext::getTenantId() ?>');
    channel.bind('new-message', function(data) {
        // Only append if it's NOT from me (or if I want to rely purely on server echo)
        // Since I blindly append my own message in sendMessage(), I should ignore my own ID here IF I strictly follow that pattern.
        // However, `data` from pusher includes `user_id`.
        if (data.user_id != <?= $user['id'] ?>) {
            appendMessage(data.message, data.sender_name, false);
            // Also notify via browser notification if hidden (handled by SW usually, but if tab is open but not focused?)
            // We can leave that to the separate Push Notification logic we built.
        }
    });

    // Push Notification Logic
    // Updated Public Key
    const VAPID_PUBLIC_KEY = 'BMjKk748g-1g2i3j4k5l6m7n8o9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d4e5f6g7';

    async function registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const register = await navigator.serviceWorker.register('/sw.js');
                console.log('SW Registered');
                return register;
            } catch (e) {
                console.error('SW Fail', e);
            }
        }
    }

    async function subscribeUser() {
        const swReg = await registerServiceWorker();
        if (!swReg) return;

        try {
            const subscription = await swReg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
            });

            // Send to server
            await fetch('/erp/chat/subscribe', {
                method: 'POST',
                body: JSON.stringify(subscription),
                headers: { 'Content-Type': 'application/json' }
            });

            pushBtn.textContent = "Notifications Enabled";
            pushBtn.disabled = true;
            alert("Notifications Enabled!");
            
        } catch (e) {
            console.error('Sub Fail', e);
            alert("Failed to enable notifications. Ensure you are on HTTPS or localhost.");
        }
    }

    pushBtn.addEventListener('click', subscribeUser);

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
    }
</script>
</body>
</html>
