<?php
if (isset($GLOBALS['cori_widget_loaded'])) {
    return;
}
$GLOBALS['cori_widget_loaded'] = true;
?>
    <!-- Cori AI Floating Widget -->
    <div id="cori-widget-container">
        <!-- Chat Window -->
        <div id="cori-widget-window">
            <div class="cori-w-header">
                <div class="cori-w-avatar">
                    <ion-icon name="sparkles"></ion-icon>
                </div>
                <div class="cori-w-info">
                    <h3>Cori AI</h3>
                    <p>Your Business Assistant</p>
                </div>
                <button class="cori-w-close" onclick="toggleCoriWidget()">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            
            <div class="cori-w-messages" id="coriWMessages">
                <div class="msg-row ai">
                    <div class="msg-bubble ai-bubble">
                        👋 Hi! I'm <strong>Cori</strong>, your AI business assistant.<br><br>
                        I can create invoices, send emails, manage tasks, and answer questions about your business. What can I do for you?
                    </div>
                </div>
            </div>
            
            <div class="cori-w-typing" id="coriWTyping">
                <div class="typing-dots"><span></span><span></span><span></span></div>
            </div>

            <!-- Quick Action Chips -->
            <div class="cori-w-chips" id="coriWChips">
                <button onclick="sendCoriChipMessage('📊 Business summary')">📊 Business summary</button>
                <button onclick="sendCoriChipMessage('📄 Create an invoice')">📄 Create an invoice</button>
                <button onclick="sendCoriChipMessage('⏳ Show unpaid invoices')">⏳ Show unpaid invoices</button>
                <button onclick="sendCoriChipMessage('➕ Add a lead')">➕ Add a lead</button>
                <button onclick="sendCoriChipMessage('💸 Record expense')">💸 Record expense</button>
                <button onclick="sendCoriChipMessage('🛍️ Add product')">🛍️ Add product</button>
                <button onclick="sendCoriChipMessage('💳 Wallet balance')">💳 Wallet balance</button>
                <button onclick="sendCoriChipMessage('🎟️ Create ticket')">🎟️ Create ticket</button>
                <button onclick="sendCoriChipMessage('📧 Campaign stats')">📧 Campaign stats</button>
                <button onclick="sendCoriChipMessage('🎓 List courses')">🎓 List courses</button>
                <button onclick="sendCoriChipMessage('🛒 Recent orders')">🛒 Recent orders</button>
            </div>
            
            <!-- Image Attachment Preview Bar -->
            <div id="coriWImagePreview" style="display:none; padding:8px 14px; background:rgba(255,166,0,0.12); border-top:1px solid rgba(255,166,0,0.25); align-items:center; gap:10px;">
                <img id="coriWPreviewImg" src="" style="width:34px; height:34px; border-radius:6px; object-fit:cover; border:1px solid rgba(255,166,0,0.4);" alt="Preview">
                <div style="flex:1; overflow:hidden;">
                    <div id="coriWFileName" style="font-size:0.75rem; color:#fff; font-weight:600; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;">Image attached</div>
                    <div style="font-size:0.68rem; color:rgba(255,255,255,0.6);">Ready to scan with AI Vision</div>
                </div>
                <button type="button" onclick="clearCoriWImage()" style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:1.1rem; display:flex; align-items:center;">
                    <ion-icon name="close-circle"></ion-icon>
                </button>
            </div>

            <div class="cori-w-input">
                <input type="file" id="coriWFileInput" accept="image/*" capture="environment" style="display:none;" onchange="handleCoriWFileSelect(event)">
                <button type="button" class="cori-w-cam-btn" id="coriWCamBtn" onclick="document.getElementById('coriWFileInput').click()" title="Snap or upload image to scan">
                    <ion-icon name="camera-outline"></ion-icon>
                </button>
                <input type="text" id="coriWInput" placeholder="Ask or snap a photo..." autocomplete="off">
                <button id="coriWSend" onclick="sendCoriWMessage()">
                    <ion-icon name="send"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Floating Button -->
        <button id="cori-widget-btn" onclick="toggleCoriWidget()">
            <ion-icon name="sparkles"></ion-icon>
        </button>
    </div>

    <!-- Markdown Parser for Widget -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <style>
        #cori-widget-container {
            position: fixed !important;
            bottom: 90px !important;
            right: 24px !important;
            z-index: 999999 !important;
            font-family: 'Outfit', 'Inter', sans-serif !important;
            display: block !important;
        }

        #cori-widget-btn {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, #FFA600, #ff8c00) !important;
            border: 1px solid rgba(255, 166, 0, 0.4) !important;
            color: #ffffff !important;
            font-size: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            box-shadow: 0 6px 20px rgba(255, 166, 0, 0.3) !important;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }
        #cori-widget-btn:hover {
            transform: scale(1.08) !important;
            box-shadow: 0 8px 25px rgba(255, 166, 0, 0.5) !important;
        }

        #cori-widget-window {
            position: absolute !important;
            bottom: 70px !important;
            right: 0 !important;
            width: 330px !important;
            height: 480px !important;
            background: rgba(3, 4, 20, 0.85) !important;
            backdrop-filter: blur(25px) !important;
            -webkit-backdrop-filter: blur(25px) !important;
            border-radius: 20px !important;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5) !important;
            border: 1px solid rgba(255, 166, 0, 0.25) !important;
            display: none;
            flex-direction: column !important;
            overflow: hidden !important;
            transform-origin: bottom right !important;
            animation: widgetPop 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        #cori-widget-window.open {
            display: flex !important;
        }
        @keyframes widgetPop {
            from { opacity: 0; transform: scale(0.8) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .cori-w-header {
            background: rgba(255, 166, 0, 0.1) !important;
            padding: 12px 16px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            border-bottom: 1px solid rgba(255, 166, 0, 0.15) !important;
        }
        .cori-w-avatar {
            width: 34px !important; height: 34px !important;
            background: rgba(255, 166, 0, 0.2) !important;
            border-radius: 50% !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            font-size: 1.1rem !important;
            color: #FFA600 !important;
            border: 1px solid rgba(255, 166, 0, 0.3) !important;
        }
        .cori-w-info h3 { margin: 0 !important; font-size: 0.95rem !important; color: #fff !important; font-weight: 700 !important; }
        .cori-w-info p { margin: 1px 0 0 !important; font-size: 0.72rem !important; color: rgba(255, 255, 255, 0.6) !important; }
        .cori-w-close {
            margin-left: auto !important;
            background: rgba(255, 255, 255, 0.05) !important; border: none !important; color: rgba(255, 255, 255, 0.7) !important;
            width: 26px !important; height: 26px !important; border-radius: 50% !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            font-size: 1.1rem !important; cursor: pointer !important;
            transition: all 0.2s !important;
        }
        .cori-w-close:hover { background: rgba(255, 166, 0, 0.2) !important; color: #FFA600 !important; }

        .cori-w-messages {
            flex: 1 !important;
            padding: 14px !important;
            overflow-y: auto !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            background: transparent !important;
        }
        .cori-w-messages::-webkit-scrollbar { width: 4px !important; }
        .cori-w-messages::-webkit-scrollbar-thumb { background: rgba(255, 166, 0, 0.2) !important; border-radius: 4px !important; }

        .msg-row { display: flex !important; max-width: 90% !important; animation: wMsgFadeIn 0.3s ease !important; }
        @keyframes wMsgFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .msg-row.ai { align-self: flex-start !important; }
        .msg-row.user { align-self: flex-end !important; }
        
        .msg-bubble {
            padding: 12px 16px !important;
            border-radius: 16px !important;
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
            word-wrap: break-word !important;
        }
        .msg-bubble.ai-bubble {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-top-left-radius: 4px !important;
            color: #f1f5f9 !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15) !important;
        }
        .msg-bubble.user-bubble {
            background: rgba(255, 166, 0, 0.2) !important;
            border: 1px solid rgba(255, 166, 0, 0.35) !important;
            border-top-right-radius: 4px !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(255, 166, 0, 0.15) !important;
        }
        
        /* Markdown in widget */
        .msg-bubble h2, .msg-bubble h3 { font-size: 0.95rem !important; margin: 0 0 8px !important; color: #FFA600 !important; font-weight: 700 !important; }
        .msg-bubble p { margin: 0 0 6px !important; }
        .msg-bubble p:last-child { margin-bottom: 0 !important; }
        .msg-bubble strong { color: #FFA600 !important; }
        .msg-bubble a { color: #FFA600 !important; text-decoration: none !important; font-weight: 600 !important; }
        .msg-bubble a:hover { text-decoration: underline !important; }

        .cori-w-typing {
            display: none;
            padding: 0 16px 10px !important;
        }
        .cori-w-typing.show { display: flex !important; }
        .typing-dots {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 14px !important;
            border-bottom-left-radius: 4px !important;
            padding: 10px 14px !important;
            display: flex !important;
            gap: 4px !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03) !important;
        }
        .typing-dots span {
            width: 6px !important; height: 6px !important; background: #94a3b8 !important; border-radius: 50% !important;
            animation: wTypingBounce 1.4s ease infinite !important;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s !important; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s !important; }
        @keyframes wTypingBounce { 0%, 60%, 100% { transform: translateY(0); opacity: 0.4; } 30% { transform: translateY(-4px); opacity: 1; } }

        .cori-w-chips {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            white-space: nowrap !important;
            gap: 6px !important;
            padding: 10px 14px !important;
            background: transparent !important;
            border-top: 1px solid rgba(255, 166, 0, 0.1) !important;
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .cori-w-chips::-webkit-scrollbar { display: none; }
        .cori-w-chips button {
            background: rgba(255, 166, 0, 0.1) !important;
            border: 1px solid rgba(255, 166, 0, 0.25) !important;
            color: #FFA600 !important;
            border-radius: 20px !important;
            padding: 5px 12px !important;
            font-size: 0.74rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
        }
        .cori-w-chips button:hover {
            background: rgba(255, 166, 0, 0.2) !important;
            transform: translateY(-1px) !important;
        }

        .cori-w-input {
            padding: 10px 14px !important;
            background: rgba(3, 4, 20, 0.95) !important;
            border-top: 1px solid rgba(255, 166, 0, 0.15) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        .cori-w-input input {
            flex: 1 !important;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 24px !important;
            padding: 8px 14px !important;
            color: #ffffff !important;
            font-family: inherit !important;
            font-size: 0.85rem !important;
            outline: none !important;
        }
        .cori-w-input input:focus { border-color: #FFA600 !important; background: rgba(255,255,255,0.08) !important; }
        .cori-w-cam-btn {
            width: 36px !important; height: 36px !important;
            border-radius: 50% !important;
            background: rgba(255, 166, 0, 0.15) !important;
            border: 1px solid rgba(255, 166, 0, 0.35) !important;
            color: #FFA600 !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            font-size: 1.25rem !important;
            flex-shrink: 0 !important;
        }
        .cori-w-cam-btn:hover { background: rgba(255, 166, 0, 0.3) !important; transform: scale(1.08) !important; color: #fff !important; }
        .cori-w-input button {
            width: 36px !important; height: 36px !important;
            border-radius: 50% !important;
            background: #FFA600 !important;
            border: none !important;
            color: #030014 !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            cursor: pointer !important;
            transition: transform 0.2s, background-color 0.2s !important;
            font-size: 1.1rem !important;
        }
        .cori-w-input button:hover { transform: scale(1.05) !important; background: #ff8c00 !important; }
        .cori-w-input button:disabled { opacity: 0.5 !important; cursor: not-allowed !important; transform: none !important; }
    </style>

    <script>
        function toggleCoriWidget() {
            const win = document.getElementById('cori-widget-window');
            const btn = document.getElementById('cori-widget-btn');
            if (!win || !btn) return;
            win.classList.toggle('open');
            if (win.classList.contains('open')) {
                btn.innerHTML = '<ion-icon name="close-outline"></ion-icon>';
                document.getElementById('coriWInput').focus();
            } else {
                btn.innerHTML = '<ion-icon name="sparkles"></ion-icon>';
            }
        }

        function sendCoriChipMessage(text) {
            const input = document.getElementById('coriWInput');
            if (input) {
                input.value = text;
                sendCoriWMessage();
            }
        }

        const coriWInput = document.getElementById('coriWInput');
        const coriWSend = document.getElementById('coriWSend');
        const coriWMessages = document.getElementById('coriWMessages');
        const coriWTyping = document.getElementById('coriWTyping');
        let coriWProcessing = false;
        let coriWSelectedImage = null;

        function handleCoriWFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                coriWSelectedImage = {
                    base64: e.target.result,
                    name: file.name
                };
                const preview = document.getElementById('coriWImagePreview');
                const previewImg = document.getElementById('coriWPreviewImg');
                const fileName = document.getElementById('coriWFileName');
                if (preview && previewImg && fileName) {
                    previewImg.src = e.target.result;
                    fileName.textContent = file.name || 'Photo captured';
                    preview.style.display = 'flex';
                }
                const input = document.getElementById('coriWInput');
                if (input && !input.value.trim()) {
                    input.placeholder = "Optional note (e.g. 'Record as expense')...";
                }
            };
            reader.readAsDataURL(file);
        }

        function clearCoriWImage() {
            coriWSelectedImage = null;
            const fileInput = document.getElementById('coriWFileInput');
            if (fileInput) fileInput.value = '';
            const preview = document.getElementById('coriWImagePreview');
            if (preview) preview.style.display = 'none';
            const input = document.getElementById('coriWInput');
            if (input) input.placeholder = "Ask or snap a photo...";
        }

        if (coriWInput) {
            coriWInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendCoriWMessage();
                }
            });
        }

        function sendCoriWMessage() {
            const text = coriWInput.value.trim();
            const hasImage = !!coriWSelectedImage;
            if (!text && !hasImage) return;
            if (coriWProcessing) return;

            const chips = document.getElementById('coriWChips');
            if (chips) chips.style.display = 'none';

            coriWProcessing = true;
            coriWSend.disabled = true;
            coriWInput.value = '';

            const sendingImage = coriWSelectedImage ? coriWSelectedImage.base64 : null;
            const sendingFileName = coriWSelectedImage ? coriWSelectedImage.name : null;
            clearCoriWImage();

            // Display user message bubble (with thumbnail if image attached)
            addCoriWUserMessage(text, sendingImage);

            coriWTyping.classList.add('show');
            scrollCoriWBottom();

            const payload = { message: text };
            if (sendingImage) {
                payload.image = sendingImage;
                payload.filename = sendingFileName;
            }

            fetch('/erp/ai-manager/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                coriWTyping.classList.remove('show');
                addCoriWMessage(data.response || 'Sorry, I couldn\'t process that.', 'ai');
                coriWProcessing = false;
                coriWSend.disabled = false;
                coriWInput.focus();
            })
            .catch(err => {
                coriWTyping.classList.remove('show');
                addCoriWMessage('⚠️ Something went wrong.', 'ai');
                coriWProcessing = false;
                coriWSend.disabled = false;
            });
        }

        function addCoriWUserMessage(text, imageBase64) {
            const row = document.createElement('div');
            row.className = 'msg-row user';
            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble user-bubble';

            if (imageBase64) {
                const imgWrap = document.createElement('div');
                imgWrap.style.marginBottom = text ? '6px' : '0';
                const img = document.createElement('img');
                img.src = imageBase64;
                img.style.maxWidth = '180px';
                img.style.maxHeight = '140px';
                img.style.borderRadius = '8px';
                img.style.display = 'block';
                img.style.objectFit = 'cover';
                imgWrap.appendChild(img);
                bubble.appendChild(imgWrap);
            }

            if (text) {
                const span = document.createElement('span');
                span.textContent = text;
                bubble.appendChild(span);
            }

            row.appendChild(bubble);
            coriWMessages.appendChild(row);
            scrollCoriWBottom();
        }

        function addCoriWMessage(text, sender) {
            const row = document.createElement('div');
            row.className = 'msg-row ' + sender;
            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble ' + (sender === 'ai' ? 'ai-bubble' : 'user-bubble');
            
            if (sender === 'ai') {
                if (text.includes('<div') || text.includes('cori-scan-card')) {
                    bubble.innerHTML = text;
                } else if (window.marked) {
                    try { bubble.innerHTML = marked.parse(text); } 
                    catch(e) { bubble.textContent = text; }
                } else {
                    bubble.textContent = text;
                }
            } else {
                bubble.textContent = text;
            }
            
            row.appendChild(bubble);
            coriWMessages.appendChild(row);
            scrollCoriWBottom();
        }

        // Handler for 1-click execution on scanned cards in widget
        window.executeCoriScanAction = function(btn) {
            try {
                const payloadStr = btn.getAttribute('data-action-payload');
                if (!payloadStr) return;
                const payload = JSON.parse(payloadStr);

                btn.disabled = true;
                btn.innerHTML = '⏳ Saving...';

                fetch('/erp/ai-manager/execute-action', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        btn.parentElement.innerHTML = '<span style="color:#10b981; font-weight:700; font-size:0.85rem;">' + (data.message || '✅ Saved to ERP!') + '</span> <a href="' + (data.view_url || '/erp') + '" class="btn-ai" style="margin-left:auto; text-decoration:none; display:inline-block; font-size:0.78rem; padding:4px 10px;">View →</a>';
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '⚠️ Retry';
                        alert(data.message || 'Could not save record.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = '⚠️ Retry';
                });
            } catch(e) {
                console.error(e);
            }
        };

        function scrollCoriWBottom() {
            requestAnimationFrame(() => {
                coriWMessages.scrollTop = coriWMessages.scrollHeight;
            });
        }
    </script>
