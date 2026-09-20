<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Team Chat | Casjoe ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #05051a, #00000a) !important;
            color: #fff !important;
            min-height: 100vh;
        }

        /* Unified Executive Sidebar overrides to match /erp dashboard */
        .sidebar { width: 270px !important; min-width: 270px !important; background: rgba(3, 4, 20, 0.85) !important; backdrop-filter: blur(30px) !important; -webkit-backdrop-filter: blur(30px) !important; border-right: 1px solid rgba(255, 166, 0, 0.25) !important; box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important; position: fixed !important; top: 0 !important; bottom: 0 !important; left: 0 !important; height: 100vh !important; z-index: 1000 !important; padding: 0 !important; display: flex !important; flex-direction: column !important; }
        .sidebar .brand { padding: 24px 20px !important; margin: 0 0 12px 0 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; display: flex !important; align-items: center !important; font-size: 1.3rem !important; font-weight: 700 !important; color: #ffffff !important; }
        .sidebar .nav-link { color: rgba(255, 255, 255, 0.72) !important; border-radius: 14px !important; margin: 6px 14px !important; padding: 13px 18px !important; font-weight: 600 !important; font-size: 0.95rem !important; transition: all 0.25s ease !important; display: flex !important; align-items: center !important; gap: 12px !important; border: 1px solid transparent !important; border-left: 1px solid transparent !important; background: transparent !important; text-decoration: none !important; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #ffffff !important; background: linear-gradient(90deg, rgba(255, 166, 0, 0.18) 0%, rgba(0, 0, 102, 0.35) 100%) !important; border: 1px solid rgba(255, 166, 0, 0.3) !important; box-shadow: 0 4px 15px rgba(255, 166, 0, 0.12) !important; }
        .sidebar .nav-link ion-icon, .sidebar .nav-link i { color: #FFA600 !important; font-size: 1.35rem !important; margin-right: 0 !important; }

        .chat-wrapper {
            display: flex;
            height: calc(100vh - 180px);
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 166, 0, 0.2);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        /* Left Sidebar: Team Directory */
        .chat-sidebar {
            width: 280px;
            border-right: 1px solid rgba(255, 166, 0, 0.2);
            background: rgba(3, 4, 20, 0.4);
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 768px) {
            .chat-sidebar { display: none; }
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #FFA600;
            letter-spacing: 0.5px;
        }

        .staff-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .staff-list::-webkit-scrollbar { width: 4px; }
        .staff-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }

        .staff-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s ease;
        }
        .staff-item:hover {
            background: rgba(255, 166, 0, 0.06);
            border-color: rgba(255, 166, 0, 0.2);
            transform: translateX(3px);
        }

        .staff-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFA600, #ff8c00);
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .staff-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .staff-info .name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #e2e8f0;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .staff-info .status {
            font-size: 0.72rem;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
        }

        /* Right Side: Message Board */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(3, 4, 20, 0.2);
        }

        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(3, 4, 20, 0.2);
        }
        .chat-header h3 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
        }
        .chat-header p {
            margin: 2px 0 0;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .chat-messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: transparent;
        }
        .chat-messages::-webkit-scrollbar { width: 4px; }
        .chat-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }

        .msg-row {
            display: flex;
            gap: 12px;
            max-width: 75%;
            animation: msgFadeIn 0.3s ease;
        }
        @keyframes msgFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .msg-row.sent {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        .msg-row.received {
            align-self: flex-start;
        }

        .msg-bubble {
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 0.92rem;
            line-height: 1.5;
            word-wrap: break-word;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .msg-row.sent .msg-bubble {
            background: linear-gradient(135deg, #0b53fc, #0220a6);
            border-top-right-radius: 4px;
            color: #fff;
        }
        .msg-row.received .msg-bubble {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-top-left-radius: 4px;
            color: #e2e8f0;
        }

        .msg-bubble .sender {
            font-size: 0.78rem;
            font-weight: 700;
            color: #FFA600;
            margin-bottom: 4px;
            display: block;
        }

        .msg-bubble .meta {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 6px;
            display: block;
            text-align: right;
        }

        .chat-input-area {
            padding: 20px;
            background: rgba(3, 4, 20, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        .chat-input-area input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: 12px 20px;
            color: #fff;
            font-family: inherit;
            outline: none;
            transition: all 0.3s;
        }
        .chat-input-area input:focus {
            border-color: #FFA600;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 15px rgba(255, 166, 0, 0.2);
        }
        .chat-input-area button {
            padding: 12px 24px;
            border-radius: 30px;
            background: #FFA600;
            color: #000;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.25s;
        }
        .chat-input-area button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 166, 0, 0.4);
        }

        /* Autocomplete Dropdown */
        .mention-dropdown {
            position: absolute;
            bottom: 75px;
            left: 20px;
            background: #0a0a2e;
            border: 1px solid rgba(255, 166, 0, 0.3);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            width: 250px;
        }
        .mention-item {
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: #fff;
            font-size: 0.88rem;
            transition: background 0.2s;
        }
        .mention-item:hover, .mention-item.active {
            background: rgba(255, 166, 0, 0.15);
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="m-0" style="font-weight:700; color:#fff;">Team Chatroom</h2>
            <button id="enable-push" class="btn" style="background: rgba(255, 166, 0, 0.1); border: 1px solid rgba(255, 166, 0, 0.25); color: #FFA600; font-size: 0.82rem; font-weight:700; border-radius:30px; padding: 8px 18px;">
                <i class="bi bi-bell-fill me-1"></i> Enable Notifications
            </button>
        </div>

        <div class="chat-wrapper">
            <!-- Left Sidebar: Directory -->
            <div class="chat-sidebar">
                <div class="sidebar-header">
                    <h3>Members</h3>
                </div>
                <div class="staff-list">
                    <?php foreach ($staff as $member): ?>
                        <div class="staff-item">
                            <div class="staff-avatar">
                                <?= strtoupper(substr($member['name'], 0, 1)) ?>
                            </div>
                            <div class="staff-info">
                                <span class="name"><?= htmlspecialchars($member['name']) ?></span>
                                <span class="status"><span class="status-dot"></span> Active</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Sidebar: Feed -->
            <div class="chat-main">
                <div class="chat-header">
                    <div>
                        <h3>#general</h3>
                        <p>Discuss anything with the team</p>
                    </div>
                </div>
                
                <div class="chat-messages" id="chat-box">
                    <?php foreach ($messages as $msg): ?>
                        <?php 
                        $isMe = ($msg['user_id'] == $user['id']);
                        $senderInitials = strtoupper(substr($msg['sender_name'], 0, 1));
                        ?>
                        <div class="msg-row <?= $isMe ? 'sent' : 'received' ?>">
                            <?php if (!$isMe): ?>
                                <div class="staff-avatar"><?= $senderInitials ?></div>
                            <?php endif; ?>
                            <div class="msg-bubble">
                                <?php if (!$isMe): ?>
                                    <span class="sender"><?= htmlspecialchars($msg['sender_name']) ?></span>
                                <?php endif; ?>
                                <?= htmlspecialchars($msg['message']) ?>
                                <span class="meta"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="chat-input-area">
                    <input type="text" id="message-input" placeholder="Type a message... (Use @ to mention)" autocomplete="off">
                    <button id="send-btn">Send</button>
                </div>
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
        appendMessage(text, "<?= htmlspecialchars($user['name'] ?? 'Me') ?>", true);

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
        div.className = `msg-row ${isMe ? 'sent' : 'received'}`;
        const initials = sender.charAt(0).toUpperCase();
        
        let html = '';
        if (!isMe) {
            html += `<div class="staff-avatar">${initials}</div>`;
        }
        
        html += `<div class="msg-bubble">`;
        if (!isMe) {
            html += `<span class="sender">${sender}</span>`;
        }
        html += `${text.replace(/</g, "&lt;").replace(/&lt;br&gt;/g, "<br>")}`;
        html += `<span class="meta">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
        html += `</div>`;
        
        div.innerHTML = html;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // @Mentions Autocomplete Setup
    const staff = <?= json_encode($staff ?? []) ?>;
    const mentionDropdown = document.createElement('div');
    mentionDropdown.className = 'mention-dropdown';
    mentionDropdown.id = 'mentionDropdown';
    document.querySelector('.chat-input-area').appendChild(mentionDropdown);
    
    let activeMentionIndex = 0;
    let showingMention = false;
    let mentionQueryStart = -1;

    msgInput.addEventListener('input', (e) => {
        const val = msgInput.value;
        const selectionStart = msgInput.selectionStart;
        const textBeforeCursor = val.slice(0, selectionStart);
        
        const lastAt = textBeforeCursor.lastIndexOf('@');
        if (lastAt !== -1 && (lastAt === 0 || textBeforeCursor[lastAt - 1] === ' ')) {
            const query = textBeforeCursor.slice(lastAt + 1);
            if (!query.includes(' ')) {
                showMentions(query, lastAt);
                return;
            }
        }
        hideMentions();
    });

    function showMentions(query, atIndex) {
        mentionQueryStart = atIndex;
        const matches = staff.filter(s => s.name.toLowerCase().includes(query.toLowerCase()));
        
        if (matches.length === 0) {
            hideMentions();
            return;
        }

        showingMention = true;
        mentionDropdown.innerHTML = '';
        matches.forEach((s, idx) => {
            const item = document.createElement('div');
            item.className = 'mention-item' + (idx === activeMentionIndex ? ' active' : '');
            const init = s.name.charAt(0).toUpperCase();
            
            item.innerHTML = `
                <div class="staff-avatar" style="width:24px; height:24px; font-size:0.7rem; background:#FFA600;">${init}</div>
                <span class="name">${s.name}</span>
            `;
            
            item.addEventListener('click', () => selectMention(s.name));
            mentionDropdown.appendChild(item);
        });
        
        mentionDropdown.style.display = 'block';
    }

    function selectMention(name) {
        const val = msgInput.value;
        const selectionStart = msgInput.selectionStart;
        const before = val.slice(0, mentionQueryStart);
        const after = val.slice(selectionStart);
        
        msgInput.value = before + '@' + name.replace(/\s+/g, '') + ' ' + after;
        hideMentions();
        msgInput.focus();
    }

    function hideMentions() {
        showingMention = false;
        mentionDropdown.style.display = 'none';
        activeMentionIndex = 0;
    }

    msgInput.addEventListener('keydown', (e) => {
        if (showingMention) {
            const items = mentionDropdown.querySelectorAll('.mention-item');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeMentionIndex = (activeMentionIndex + 1) % items.length;
                updateMentionSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeMentionIndex = (activeMentionIndex - 1 + items.length) % items.length;
                updateMentionSelection(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const activeItem = items[activeMentionIndex];
                if (activeItem) {
                    const name = activeItem.querySelector('.name').textContent;
                    selectMention(name);
                }
            } else if (e.key === 'Escape') {
                hideMentions();
            }
        }
    });

    function updateMentionSelection(items) {
        items.forEach((item, idx) => {
            if (idx === activeMentionIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Pusher Real-time Logic
    const pusher = new Pusher('002937f554e53a1a6371', {
        cluster: 'mt1'
    });

    const channel = pusher.subscribe('chat-<?= \App\Core\TenantContext::getTenantId() ?>');
    channel.bind('new-message', function(data) {
        if (data.user_id != <?= $user['id'] ?>) {
            appendMessage(data.message, data.sender_name, false);
        }
    });

    // VAPID Push Notification Subscriptions
    const VAPID_PUBLIC_KEY = 'BMjKk748g-1g2i3j4k5l6m7n8o9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d4e5f6g7';

    async function registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const register = await navigator.serviceWorker.register('/sw.js');
                return register;
            } catch (e) {
                console.error('SW Registration Failed', e);
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

            await fetch('/erp/chat/subscribe', {
                method: 'POST',
                body: JSON.stringify(subscription),
                headers: { 'Content-Type': 'application/json' }
            });

            pushBtn.innerHTML = "<i class='bi bi-bell-fill me-1'></i> Notifications Enabled";
            pushBtn.disabled = true;
            alert("Notifications Enabled!");
            
        } catch (e) {
            console.error('Subscription Failed', e);
            alert("Failed to enable notifications. Check HTTPS connection.");
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
