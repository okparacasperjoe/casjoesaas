<?php
$title = 'The AI Office | Casjoe BOS';
require __DIR__ . '/../global_header.php';
?>

<style>
    body, .main-content {
        background: #030413 !important;
        background-image: 
            radial-gradient(circle at 15% 10%, rgba(0, 0, 102, 0.65) 0%, transparent 45%),
            radial-gradient(circle at 85% 20%, rgba(255, 166, 0, 0.15) 0%, transparent 40%),
            radial-gradient(circle at 50% 80%, rgba(10, 15, 60, 0.8) 0%, transparent 60%) !important;
        background-attachment: fixed !important;
        color: white !important;
    }
    /* Force ALL headings, labels, and titles on the AI Office dark background to be WHITE (#ffffff) instead of global blue (#000066) */
    .main-content h1, .main-content h2, .main-content h3, .main-content h4, .main-content h5, .main-content h6,
    .main-content label, .main-content strong,
    .ai-office-wrapper h1, .ai-office-wrapper h2, .ai-office-wrapper h3, .ai-office-wrapper h4, .ai-office-wrapper h5, .ai-office-wrapper h6,
    .ai-sidebar-header h3, .chat-header h4, .widget-header, .paywall-card h3,
    .ai-content h1, .ai-content h2, .ai-content h3, .ai-content h4, .ai-content h5, .ai-content h6 {
        color: #ffffff !important;
    }
    :root {
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --accent: #FFA600;
        --accent-glow: rgba(255, 166, 0, 0.2);
    }
    
    .ai-office-wrapper {
        display: flex;
        height: calc(100vh - 100px);
        background: var(--glass-bg);
        border-radius: 24px;
        border: 1px solid var(--glass-border);
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    /* Inner Sidebar */
    .ai-sidebar {
        width: 280px;
        background: rgba(0, 0, 0, 0.4);
        border-right: 1px solid var(--glass-border);
        display: flex;
        flex-direction: column;
    }
    
    .ai-sidebar-header {
        padding: 25px 20px;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .ai-sidebar-header h3 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ai-sidebar ul {
        list-style: none;
        padding: 15px;
        margin: 0;
        flex-grow: 1;
        overflow-y: auto;
    }

    .ai-sidebar ul::-webkit-scrollbar { width: 6px; }
    .ai-sidebar ul::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .ai-sidebar li { margin-bottom: 8px; }

    .ai-sidebar li a {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        color: rgba(255,255,255,0.6);
        text-decoration: none;
        gap: 12px;
        border-radius: 14px;
        transition: all 0.3s ease;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .ai-sidebar li a:hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
    }

    .ai-sidebar li.active a {
        background: linear-gradient(90deg, rgba(255, 166, 0, 0.15) 0%, rgba(0, 0, 102, 0.3) 100%);
        color: var(--accent);
        border: 1px solid rgba(255,166,0,0.3);
        box-shadow: 0 4px 15px var(--accent-glow);
    }
    
    .ai-sidebar li.active a ion-icon {
        color: var(--accent);
    }

    .ai-sidebar-footer {
        padding: 20px;
        border-top: 1px solid var(--glass-border);
        background: rgba(0,0,0,0.2);
    }

    .token-badge {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255,166,0,0.1);
        border: 1px solid rgba(255,166,0,0.3);
        padding: 12px 15px;
        border-radius: 12px;
        color: var(--accent);
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Main Content Area */
    .ai-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: radial-gradient(circle at top right, rgba(0,0,102,0.2), transparent 50%);
        position: relative;
    }

    .chat-header {
        padding: 20px 30px;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255,255,255,0.02);
        backdrop-filter: blur(10px);
    }

    .chat-header h4 { margin: 0; color: #fff; font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
    
    .model-badge {
        background: rgba(255,255,255,0.1);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        color: #ddd;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .chat-body {
        flex-grow: 1;
        padding: 30px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .chat-body::-webkit-scrollbar { width: 8px; }
    .chat-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .message {
        max-width: 75%;
        padding: 18px 24px;
        border-radius: 20px;
        line-height: 1.6;
        font-size: 1rem;
        position: relative;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message.user {
        background: linear-gradient(135deg, #000066 0%, #0000aa 100%);
        color: #fff;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
        box-shadow: 0 5px 15px rgba(0,0,102,0.3);
    }

    .message.ai {
        background: rgba(0,0,0,0.4);
        border: 1px solid var(--glass-border);
        color: #e2e8f0;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .chat-footer {
        padding: 20px 30px;
        border-top: 1px solid var(--glass-border);
        background: rgba(0,0,0,0.3);
    }

    .chat-input-wrapper {
        display: flex;
        gap: 15px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 8px 8px 8px 20px;
        border-radius: 30px;
        transition: all 0.3s;
    }

    .chat-input-wrapper:focus-within {
        border-color: var(--accent);
        box-shadow: 0 0 20px var(--accent-glow);
        background: rgba(255,255,255,0.08);
    }

    .chat-input {
        flex-grow: 1;
        background: transparent;
        border: none;
        color: white;
        font-size: 1rem;
        outline: none;
    }

    .btn-send {
        background: var(--accent);
        border: none;
        color: #000;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        transition: 0.3s;
    }

    .btn-send:hover {
        transform: scale(1.05);
        box-shadow: 0 0 15px var(--accent-glow);
    }

    .btn-send:disabled {
        background: rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.5);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Paywall UI */
    .paywall {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 50px;
    }
    
    .paywall-card {
        background: rgba(0,0,0,0.5);
        border: 1px solid var(--glass-border);
        padding: 40px;
        border-radius: 24px;
        max-width: 500px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        backdrop-filter: blur(10px);
    }

    .paywall ion-icon { font-size: 5rem; color: var(--accent); margin-bottom: 20px; filter: drop-shadow(0 0 20px var(--accent-glow)); }
    
    .paywall h3 { font-size: 1.8rem; margin-bottom: 15px; color: #fff; }
    .paywall p { color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 30px; font-size: 1.1rem; }

    .btn-subscribe {
        background: linear-gradient(135deg, var(--accent) 0%, #ff8c00 100%);
        border: none;
        padding: 15px 35px;
        color: #000;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-subscribe:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px var(--accent-glow);
    }

    /* Dashboard Widgets */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .widget {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 25px;
        transition: 0.3s;
    }
    
    .widget:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.15);
    }

    .widget-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--accent);
    }

    .event-item, .automation-item {
        padding: 15px;
        background: rgba(0,0,0,0.3);
        border-radius: 12px;
        margin-bottom: 12px;
        border-left: 3px solid var(--accent);
    }
    
    .event-item.low { border-left-color: #3b82f6; }
    .event-item.medium { border-left-color: #f59e0b; }
    .event-item.high { border-left-color: #ef4444; }

    .event-meta { font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-bottom: 5px; }
    .event-text { font-size: 0.95rem; color: #e2e8f0; }

    @media (max-width: 900px) {
        .ai-office-wrapper { flex-direction: column; height: auto; min-height: calc(100vh - 100px); }
        .ai-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--glass-border); flex-direction: row; overflow-x: auto; padding: 10px; }
        .ai-sidebar-header { display: none; }
        .ai-sidebar-footer { display: none; }
        .ai-sidebar ul { display: flex; gap: 10px; padding: 0; overflow-y: hidden; }
        .ai-sidebar li { margin: 0; white-space: nowrap; }
        .chat-body { min-height: 500px; }
    }
</style>

<div class="mb-4">
    <h2 class="text-white fw-bold mb-0" style="font-size: 1.8rem;">
        <ion-icon name="briefcase-outline" style="color: #FFA600; vertical-align: text-bottom;"></ion-icon> The AI Office
    </h2>
    <p style="color: rgba(255,255,255,0.6);">Your intelligent autonomous workforce.</p>
</div>

<div class="ai-office-wrapper">
    <!-- Inner Sidebar -->
    <div class="ai-sidebar">
        <div class="ai-sidebar-header">
            <ion-icon name="hardware-chip-outline" style="font-size: 1.5rem; color: #FFA600;"></ion-icon>
            <h3>Departments</h3>
        </div>
        <ul>
            <li class="<?= $activeTab === 'dashboard' ? 'active' : '' ?>">
                <a href="?agent=dashboard"><ion-icon name="grid-outline"></ion-icon> Office Dashboard</a>
            </li>
            <li class="<?= $activeTab === 'sales_manager' ? 'active' : '' ?>">
                <a href="?agent=sales_manager"><ion-icon name="trending-up-outline"></ion-icon> AI Sales Manager</a>
            </li>
            <li class="<?= $activeTab === 'accountant' ? 'active' : '' ?>">
                <a href="?agent=accountant"><ion-icon name="calculator-outline"></ion-icon> AI Accountant</a>
            </li>
            <li class="<?= $activeTab === 'support_agent' ? 'active' : '' ?>">
                <a href="?agent=support_agent"><ion-icon name="headset-outline"></ion-icon> AI Support Agent</a>
            </li>
            <li class="<?= $activeTab === 'queue' ? 'active' : '' ?>" style="margin-top: 15px;">
                <a href="?agent=queue" style="color: #FFA600;"><ion-icon name="checkmark-done-circle-outline"></ion-icon> Action Queue</a>
            </li>
            <li class="<?= $activeTab === 'automations' ? 'active' : '' ?>">
                <a href="?agent=automations" style="color: #10b981;"><ion-icon name="flash-outline"></ion-icon> Automation Studio</a>
            </li>
        </ul>
        <div class="ai-sidebar-footer">
            <div class="token-badge" title="Tokens remaining for AI tasks">
                <span><ion-icon name="flash"></ion-icon> Tokens</span>
                <span><?= number_format($tokensAvailable) ?></span>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="ai-content">
        <?php if (!$isSubscribed): ?>
            <div class="paywall">
                <div class="paywall-card">
                    <ion-icon name="lock-closed"></ion-icon>
                    <h3>AI Workforce Locked</h3>
                    <p>Upgrade to the AI Employee Subscription (10,000 NGN/mo) to unlock your autonomous Sales Manager, Accountant, and Automation Studio.</p>
                    <form method="POST" action="/ai-office/subscribe">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::getToken() ?>">
                        <button type="submit" class="btn-subscribe">
                            <ion-icon name="key-outline"></ion-icon> Subscribe Now
                        </button>
                    </form>
                </div>
            </div>
        <?php elseif ($activeTab === 'dashboard'): ?>
            <div class="chat-header">
                <h4><ion-icon name="pulse-outline" style="color:#FFA600;"></ion-icon> Office Activity Stream</h4>
                <div class="model-badge">Real-time Global Context</div>
            </div>
            <div class="chat-body" style="padding: 30px;">
                <div class="dashboard-grid">
                    <div class="widget">
                        <div class="widget-header"><ion-icon name="radio-outline"></ion-icon> Recent Events</div>
                        <?php if (empty($recentEvents)): ?>
                            <p style="color: rgba(255,255,255,0.4); font-style: italic;">No recent system events.</p>
                        <?php else: ?>
                            <?php foreach ($recentEvents as $ev): ?>
                                <div class="event-item <?= htmlspecialchars($ev['priority'] ?? 'low') ?>">
                                    <div class="event-meta"><?= htmlspecialchars($ev['module']) ?> &bull; <?= date('M j, Y H:i', strtotime($ev['created_at'])) ?></div>
                                    <div class="event-text"><strong><?= htmlspecialchars($ev['event_type']) ?></strong><br>
                                    <small style="color:rgba(255,255,255,0.6);"><?= htmlspecialchars(substr($ev['payload'], 0, 100)) ?>...</small></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="widget">
                        <div class="widget-header"><ion-icon name="flash-outline"></ion-icon> Active Automations</div>
                        <?php if (empty($automations)): ?>
                            <p style="color: rgba(255,255,255,0.4); font-style: italic;">No automations configured.</p>
                        <?php else: ?>
                            <?php foreach ($automations as $aut): ?>
                                <div class="automation-item">
                                    <div class="event-text">
                                        <strong><?= htmlspecialchars($aut['name'] ?: ($aut['trigger_event'] . ' Automation')) ?></strong>
                                        <span style="float:right; font-size:0.8rem; padding: 2px 8px; border-radius: 10px; background: <?= $aut['is_active'] ? 'rgba(16,185,129,0.2)' : 'rgba(255,255,255,0.1)' ?>; color: <?= $aut['is_active'] ? '#10b981' : '#fff' ?>;">
                                            <?= $aut['is_active'] ? 'Active' : 'Paused' ?>
                                        </span>
                                    </div>
                                    <div class="event-meta" style="margin-top: 5px;">Trigger: <?= htmlspecialchars($aut['trigger_event']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($activeTab === 'automations'): ?>
            <div class="chat-header">
                <h4><ion-icon name="flash-outline" style="color:#10b981;"></ion-icon> Automation Studio</h4>
                <div class="model-badge">Event-Driven AI Rules</div>
            </div>
            <div class="chat-body" style="padding: 30px;">
                <p style="color: rgba(255,255,255,0.7); margin-bottom: 25px;">Define rules for AI agents to automatically trigger actions when specific events occur in your ecosystem.</p>
                
                <div class="widget" style="margin-bottom: 30px;">
                    <div class="widget-header"><ion-icon name="add-circle-outline"></ion-icon> Create New Automation</div>
                    <form method="POST" action="/ai-office/automations/create">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::getToken() ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <input type="text" name="name" class="chat-input" placeholder="Automation Name (e.g. Welcome Lead)" required style="border-radius: 12px;">
                            <select name="trigger_event" class="chat-input" required style="border-radius: 12px; appearance: none;">
                                <option value="" disabled selected>Select Trigger Event...</option>
                                <option value="lead_created">CRM: Lead Created</option>
                                <option value="invoice_created">Finance: Invoice Created</option>
                                <option value="order_placed">Shop: Order Placed</option>
                                <option value="course_completed">Academy: Course Completed</option>
                            </select>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <select name="agent_role" class="chat-input" required style="border-radius: 12px; appearance: none;">
                                <option value="" disabled selected>Select AI Agent...</option>
                                <option value="sales_manager">Sales Manager</option>
                                <option value="accountant">Accountant</option>
                                <option value="hr">HR Generalist</option>
                            </select>
                            <input type="text" name="prompt" class="chat-input" placeholder="Action Prompt (e.g. Draft welcome email)" required style="border-radius: 12px;">
                        </div>
                        <button type="submit" class="btn-subscribe" style="padding: 10px 25px; font-size: 1rem; border-radius: 12px;"><ion-icon name="save-outline"></ion-icon> Save Automation</button>
                    </form>
                </div>
                
                <h5 style="color: #fff; margin-bottom: 15px;">Existing Automations</h5>
                <?php foreach ($automations as $aut): ?>
                    <div class="automation-item" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="event-text"><strong><?= htmlspecialchars($aut['name'] ?: ($aut['trigger_event'] . ' Automation')) ?></strong></div>
                            <div class="event-meta">When <strong><?= htmlspecialchars($aut['trigger_event']) ?></strong> occurs, <strong><?= htmlspecialchars($aut['agent_role'] ?: 'AI Agent') ?></strong> will "<?= htmlspecialchars($aut['agent_prompt']) ?>"</div>
                        </div>
                        <form method="POST" action="/ai-office/automations/delete" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::getToken() ?>">
                            <input type="hidden" name="automation_id" value="<?= $aut['id'] ?>">
                            <button type="submit" class="btn-send" style="width: 35px; height: 35px; background: rgba(239, 68, 68, 0.2); color: #ef4444;"><ion-icon name="trash-outline"></ion-icon></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($activeTab === 'queue'): ?>
            <div class="chat-header">
                <h4><ion-icon name="checkmark-done-circle-outline" style="color:#FFA600;"></ion-icon> Action Queue</h4>
                <div class="model-badge">Pending Approvals</div>
            </div>
            <div class="chat-body" style="padding: 30px;">
                <p style="color: rgba(255,255,255,0.7); margin-bottom: 25px;">Review and approve actions drafted by your AI employees before they are executed.</p>
                <?php if (empty($actionQueue)): ?>
                    <p style="color: rgba(255,255,255,0.4); font-style: italic;">No pending actions in the queue.</p>
                <?php else: ?>
                    <?php foreach ($actionQueue as $action): ?>
                        <div class="widget" style="margin-bottom: 15px;">
                            <div class="event-text" style="display: flex; justify-content: space-between;">
                                <strong><?= htmlspecialchars($action['agent_role']) ?></strong>
                                <span style="font-size: 0.8rem; color: #FFA600;"><ion-icon name="time-outline"></ion-icon> Pending</span>
                            </div>
                            <div style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px; margin: 10px 0; font-family: monospace; color: #a5b4fc;">
                                <?= nl2br(htmlspecialchars($action['action_payload'])) ?>
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <form method="POST" action="/ai-office/queue/approve" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::getToken() ?>">
                                    <input type="hidden" name="action_id" value="<?= $action['id'] ?>">
                                    <button type="submit" style="background: #10b981; color: #fff; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;"><ion-icon name="checkmark-outline"></ion-icon> Approve & Execute</button>
                                </form>
                                <form method="POST" action="/ai-office/queue/reject" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Services\CsrfService::getToken() ?>">
                                    <input type="hidden" name="action_id" value="<?= $action['id'] ?>">
                                    <button type="submit" style="background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 8px 15px; border-radius: 8px; cursor: pointer;"><ion-icon name="close-outline"></ion-icon> Reject</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="chat-header">
                <h4><ion-icon name="<?= $agent['icon'] ?>" style="color:#FFA600;"></ion-icon> <?= htmlspecialchars($agent['name']) ?></h4>
                <div class="model-badge"><ion-icon name="server-outline"></ion-icon> <?= htmlspecialchars($agent['model']) ?></div>
            </div>
            
            <div class="chat-body" id="chatBox">
                <div class="message ai">
                    <strong><?= htmlspecialchars($agent['name']) ?></strong><br><br>
                    <?= htmlspecialchars($agent['greeting']) ?>
                </div>
                <!-- History -->
            </div>

            <div class="chat-footer">
                <form id="chatForm" onsubmit="sendChatMessage(event)">
                    <div class="chat-input-wrapper">
                        <input type="hidden" id="agentRole" value="<?= htmlspecialchars($activeTab) ?>">
                        <input type="text" id="chatInput" class="chat-input" placeholder="Give <?= htmlspecialchars($agent['name']) ?> a command or ask a question..." autocomplete="off" required>
                        <button type="submit" class="btn-send" id="sendBtn"><ion-icon name="paper-plane"></ion-icon></button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    async function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const role = document.getElementById('agentRole').value;
        const box = document.getElementById('chatBox');
        const btn = document.getElementById('sendBtn');
        const msg = input.value.trim();
        
        if (!msg) return;

        // User message
        box.innerHTML += `<div class="message user">${msg.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</div>`;
        input.value = '';
        box.scrollTop = box.scrollHeight;

        // Loading
        btn.disabled = true;
        const loadId = 'load-' + Date.now();
        box.innerHTML += `<div id="${loadId}" class="message ai" style="opacity:0.6;"><ion-icon name="sync-outline" class="spin"></ion-icon> Analyzing...</div>`;
        box.scrollTop = box.scrollHeight;

        try {
            const fd = new FormData();
            fd.append('message', msg);
            fd.append('agent', role);

            const res = await fetch('/ai-office/chat', { method: 'POST', body: fd });
            const data = await res.json();
            
            document.getElementById(loadId).remove();
            
            if (data.error) {
                box.innerHTML += `<div class="message ai" style="border-left: 3px solid #ef4444;">Error: ${data.error}</div>`;
            } else {
                box.innerHTML += `<div class="message ai">${data.response.replace(/\n/g, '<br>')}</div>`;
            }
        } catch (err) {
            document.getElementById(loadId).remove();
            box.innerHTML += `<div class="message ai" style="border-left: 3px solid #ef4444;">Network Error.</div>`;
        }
        
        btn.disabled = false;
        box.scrollTop = box.scrollHeight;
    }
</script>

<style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    .spin { animation: spin 1s linear infinite; }
</style>

<?php require __DIR__ . '/../global_footer.php'; ?>
