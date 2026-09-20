(function () {
    // 0. Configuration
    const CONFIG = {
        chatEndpoint: '/erp/chat/send',
        pollEndpoint: '/erp/chat/poll',
        registerEndpoint: '/erp/chat/register-lead',
        color: '#ff9900', // Brand Amber
        brandColor: '#000066', // Brand Blue
        title: 'Cori AI',
        subtitle: 'We typically reply instantly',
        // Check if user is logged in (Global Variable injected in Footer)
        isLoggedIn: window.casjoeUser ? true : false,
        userId: window.casjoeUser ? window.casjoeUser.id : null,
        tenantId: window.casjoeTenantId || (window.casjoeUser ? window.casjoeUser.tenant_id : 13),
        isRegistered: localStorage.getItem('casjoe_chat_registered') === 'true'
    };

    // 1. Session Management (Guest Support)
    // If not logged in, generate/retrieve a random Guest ID from LocalStorage
    if (!CONFIG.userId) {
        let guestId = localStorage.getItem('casjoe_guest_id');
        if (!guestId) {
            guestId = 'guest_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('casjoe_guest_id', guestId);
        }
        CONFIG.userId = guestId; // Use string for guests
        CONFIG.isGuest = true;
    }

    // 2. Inject CSS
    const style = document.createElement('style');
    style.innerHTML = `
        :root { --cj-chat-primary: ${CONFIG.color}; --cj-chat-brand: ${CONFIG.brandColor}; }
        
        /* Floating Button */
        #cj-chat-btn {
            position: fixed; bottom: 80px; right: 20px;
            width: 60px; height: 60px;
            background: var(--cj-chat-primary);
            border-radius: 50%;
            box-shadow: 0 4px 20px rgba(255, 153, 0, 0.4);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 2147483647; 
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: auto !important;
        }
        #cj-chat-btn:hover { transform: scale(1.1); }
        #cj-chat-btn svg { width: 32px; height: 32px; fill: #fff; }
        
        /* Chat Window */
        #cj-chat-window {
            position: fixed; bottom: 150px; right: 20px;
            width: 350px; height: 500px; max-height: 70vh;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: none; flex-direction: column; overflow: hidden;
            z-index: 99999;
            font-family: 'Segoe UI', sans-serif;
            opacity: 0; transform: translateY(20px);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        #cj-chat-window.active { display: flex; opacity: 1; transform: translateY(0); }

        /* Header */
        .cj-chat-header {
            background: linear-gradient(135deg, var(--cj-chat-brand) 0%, #000044 100%);
            padding: 20px; color: #fff;
            display: flex; align-items: center; justify-content: space-between;
        }
        .cj-chat-header h4 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .cj-chat-header p { margin: 4px 0 0; font-size: 0.8rem; opacity: 0.8; }
        .cj-chat-close { cursor: pointer; font-size: 1.2rem; background: rgba(255,255,255,0.1); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

        /* Registration Form */
        .cj-chat-reg-container { padding: 30px; box-sizing: border-box; flex: 1; display: flex; flex-direction: column; justify-content: center; background: #fff; }
        .cj-chat-reg-container h3 { margin: 0 0 10px; color: var(--cj-chat-brand); font-size: 1.2rem; }
        .cj-chat-reg-container p { margin: 0 0 20px; font-size: 0.9rem; color: #666; }
        .cj-chat-reg-field { margin-bottom: 15px; }
        .cj-chat-reg-field label { display: block; margin-bottom: 5px; font-size: 0.8rem; color: #444; font-weight: 600; }
        .cj-chat-reg-field input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; outline: none; }
        .cj-chat-reg-field input:focus { border-color: var(--cj-chat-primary); }
        .cj-chat-reg-btn { background: var(--cj-chat-brand); color: #fff; border: none; width: 100%; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.2s; margin-top: 10px; }
        .cj-chat-reg-btn:hover { background: var(--cj-chat-primary); }

        /* Messages Area */
        .cj-chat-body { flex: 1; overflow-y: auto; padding: 20px; background: #f8f9fa; display: flex; flex-direction: column; gap: 12px; }
        
        /* Message Bubbles */
        .cj-msg { max-width: 80%; padding: 10px 15px; border-radius: 12px; font-size: 0.9rem; line-height: 1.4; position: relative; word-wrap: break-word; }
        .cj-msg.bot { background: #fff; color: #333; border-bottom-left-radius: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); align-self: flex-start; }
        .cj-msg.user { background: var(--cj-chat-brand); color: #fff; border-bottom-right-radius: 2px; align-self: flex-end; }
        
        /* Input Area */
        .cj-chat-footer { padding: 15px; background: #fff; border-top: 1px solid #eee; display: flex; gap: 10px; }
        #cj-chat-input { flex: 1; border: 1px solid #ddd; padding: 10px 15px; border-radius: 25px; outline: none; font-size: 0.9rem; transition: 0.2s; }
        #cj-chat-input:focus { border-color: var(--cj-chat-primary); }
        #cj-chat-send { background: var(--cj-chat-brand); color: #fff; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        #cj-chat-send:hover { background: var(--cj-chat-primary); }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            #cj-chat-window { bottom: 0; right: 0; width: 100%; height: 100%; border-radius: 0; max-height: 100%; }
            #cj-chat-btn { bottom: 80px; right: 20px; }
        }
    `;
    document.head.appendChild(style);

    // 3. Inject HTML
    const showReg = !CONFIG.isLoggedIn && !CONFIG.isRegistered;
    const widgetHTML = `
        <div id="cj-chat-btn" title="Chat with Support">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
        </div>
        <div id="cj-chat-window">
            <div class="cj-chat-header">
                <div>
                    <h4>${CONFIG.title}</h4>
                    <p>${CONFIG.subtitle}</p>
                </div>
                <div class="cj-chat-close">&times;</div>
            </div>
            
            <div id="cj-chat-reg-view" class="cj-chat-reg-container" style="display: ${showReg ? 'flex' : 'none'};">
                <h3>Welcome! 👋</h3>
                <p>Please introduce yourself to start chatting with our team.</p>
                <div class="cj-chat-reg-field">
                    <label>Full Name</label>
                    <input type="text" id="cj-reg-name" placeholder="Enter your name">
                </div>
                <div class="cj-chat-reg-field">
                    <label>Email Address</label>
                    <input type="email" id="cj-reg-email" placeholder="Enter your email">
                </div>
                <div class="cj-chat-reg-field">
                    <label>Phone Number</label>
                    <input type="text" id="cj-reg-phone" placeholder="Enter your phone number">
                </div>
                <button id="cj-reg-submit" class="cj-chat-reg-btn">Start Chatting</button>
            </div>

            <div class="cj-chat-body" id="cj-chat-body" style="display: ${showReg ? 'none' : 'flex'};">
                <div class="cj-msg bot">Hello! 👋 I'm Cori AI. How can I help you? Try asking about <b>Pricing</b> or <b>Support</b>.</div>
            </div>
            <div class="cj-chat-footer" id="cj-chat-footer" style="display: ${showReg ? 'none' : 'flex'};">
                <input type="text" id="cj-chat-input" placeholder="Type a message..." autocomplete="off">
                <button id="cj-chat-send">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#fff"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                </button>
            </div>
        </div>
    `;
    const container = document.createElement('div');
    container.innerHTML = widgetHTML;
    document.body.appendChild(container);

    // 4. Logic
    const btn = document.getElementById('cj-chat-btn');
    const win = document.getElementById('cj-chat-window');
    const close = document.querySelector('.cj-chat-close');
    const input = document.getElementById('cj-chat-input');
    const sendBtn = document.getElementById('cj-chat-send');
    const body = document.getElementById('cj-chat-body');
    const footer = document.getElementById('cj-chat-footer');
    const regView = document.getElementById('cj-chat-reg-view');
    const regSubmit = document.getElementById('cj-reg-submit');

    // Toggle
    btn.addEventListener('click', () => {
        if (win.classList.contains('active')) {
            win.classList.remove('active');
        } else {
            win.classList.add('active');
            if (regView.style.display !== 'none') {
                document.getElementById('cj-reg-name').focus();
            } else {
                input.focus();
                scrollToBottom();
                if (!pollingInterval) startPolling();
            }
        }
    });

    close.addEventListener('click', () => win.classList.remove('active'));

    // Lead Registration
    regSubmit.addEventListener('click', () => {
        const name = document.getElementById('cj-reg-name').value.trim();
        const email = document.getElementById('cj-reg-email').value.trim();
        const phone = document.getElementById('cj-reg-phone').value.trim();

        if (!name || !email) {
            alert('Name and Email are required to start the chat.');
            return;
        }

        regSubmit.disabled = true;
        regSubmit.innerText = 'Connecting...';

        const payload = `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`;

        fetch(CONFIG.registerEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: payload
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                localStorage.setItem('casjoe_chat_registered', 'true');
                localStorage.setItem('casjoe_chat_name', name);
                
                // Show Chat UI
                regView.style.display = 'none';
                body.style.display = 'flex';
                footer.style.display = 'flex';
                
                input.focus();
                scrollToBottom();
                startPolling();
                
                // Initial welcome
                appendMessage(`Welcome <b>${name}</b>! How can we help you today?`, 'bot');
            } else {
                alert('Connection failed. Please try again.');
                regSubmit.disabled = false;
                regSubmit.innerText = 'Start Chatting';
            }
        })
        .catch(err => {
            console.error('Registration Error:', err);
            alert('Something went wrong. Please try again.');
            regSubmit.disabled = false;
            regSubmit.innerText = 'Start Chatting';
        });
    });

    // Send Message
    function sendMessage() {
        const msg = input.value.trim();
        if (!msg) return;

        // Add User Message Locally
        appendMessage(msg, 'user');
        input.value = '';
        scrollToBottom();

        // AJAX to Backend
        let payload = 'message=' + encodeURIComponent(msg);
        if (CONFIG.isGuest) {
            payload += '&guest_id=' + encodeURIComponent(CONFIG.userId);
        }

        fetch(CONFIG.chatEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: payload
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Poll immediately to catch bot response
                    setTimeout(pollMessages, 500);
                }
            })
            .catch(err => {
                console.error('Chat Error:', err);
                appendMessage('Error sending message. Please try again.', 'bot');
            });
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });

    function appendMessage(text, type) {
        const div = document.createElement('div');
        div.className = `cj-msg ${type}`;
        div.innerHTML = parseMarkdown(text);
        body.appendChild(div);
    }

    function parseMarkdown(text) {
        if (!text) return '';
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Bold (**text**)
        html = html.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Italic (*text*)
        html = html.replace(/\*(.*?)\*/g, '<i>$1</i>');

        // Links ([text](url))
        html = html.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank">$1</a>');

        return html;
    }

    function scrollToBottom() {
        body.scrollTop = body.scrollHeight;
    }

    // Polling Logic
    let lastId = 0;
    let pollingInterval = null;

    function pollMessages() {
        if (!win.classList.contains('active')) return;

        const url = `${CONFIG.pollEndpoint}?last_id=${lastId}` + (CONFIG.isGuest ? `&guest_id=${CONFIG.userId}` : '');

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.messages && data.messages.length > 0) {
                    data.messages.forEach(m => {
                        if (m.id > lastId) {
                            lastId = m.id;

                            // Only append if it's NOT a 'sent' message that we already appended locally
                            // OR if it's from a Bot
                            if (m.type === 'received') {
                                appendMessage(m.message, 'bot');
                                scrollToBottom();
                            }
                        }
                    });
                }
            })
            .catch(err => console.debug('Poll failed:', err));
    }

    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(pollMessages, 4000);
        pollMessages(); // Initial poll
    }

    // Auto-start if it's already open for some reason
    if (win.classList.contains('active') && regView.style.display === 'none') startPolling();

})();
