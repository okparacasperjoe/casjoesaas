<?php
$title = "Support Center | Casjoe Apps";
require __DIR__ . '/../global_header.php';

// If tickets variable is not passed or empty, initialize as array
if (!isset($tickets) || !is_array($tickets)) {
    $tickets = [];
}
?>
<style>
    .support-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }
    .support-title {
        color: #000066;
        font-weight: 800;
        margin: 0;
        font-size: 2rem;
        letter-spacing: -0.5px;
    }
    .btn-new-ticket {
        background: #FFA600;
        color: #ffffff;
        font-weight: 700;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(255, 166, 0, 0.3);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-new-ticket:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 166, 0, 0.4);
        color: #ffffff;
    }
    .contact-info-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 102, 0.08);
        border-radius: 14px;
        padding: 20px 25px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }
    .contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .contact-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(0, 0, 102, 0.06);
        color: #000066;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .contact-label {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .contact-value {
        font-size: 1.05rem;
        color: #0f172a;
        font-weight: 700;
        text-decoration: none;
    }
    .contact-value:hover {
        color: #FFA600;
    }
    .ai-chat-embedded {
        background: linear-gradient(135deg, #000066 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 102, 0.2);
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .ai-chat-embedded::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 166, 0, 0.18) 0%, transparent 70%);
        pointer-events: none;
    }
    .ai-chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 16px;
    }
    .ai-chat-title-box {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .ai-chat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #FFA600;
        color: #000066;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(255, 166, 0, 0.3);
    }
    .ai-token-badge {
        background: rgba(255, 166, 0, 0.15);
        color: #FFA600;
        border: 1px solid rgba(255, 166, 0, 0.35);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .ai-input-wrapper {
        display: flex;
        gap: 10px;
        position: relative;
        z-index: 2;
    }
    .ai-chat-input {
        flex: 1;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 1rem;
        transition: border-color 0.2s, background 0.2s;
    }
    .ai-chat-input:focus {
        background: rgba(255, 255, 255, 0.12);
        border-color: #FFA600;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 166, 0, 0.25);
    }
    .ai-chat-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    .btn-ai-send {
        background: #FFA600;
        color: #000066;
        font-weight: 700;
        border: none;
        padding: 0 28px;
        border-radius: 12px;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s, background 0.2s;
    }
    .btn-ai-send:hover {
        background: #ffb72b;
        transform: translateY(-1px);
    }
    .ai-chat-output-box {
        background: rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 22px;
        margin-top: 20px;
        display: none;
        animation: slideDown 0.3s ease;
        position: relative;
        z-index: 2;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tickets-table-card {
        background: #64748b;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .tickets-table-card table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    .tickets-table-card th {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0 16px 8px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }
    .tickets-table-card td {
        padding: 16px;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.98rem;
    }
    .ticket-status-badge {
        background: rgba(46, 213, 115, 0.25);
        color: #2ed573;
        border: 1px solid rgba(46, 213, 115, 0.4);
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        display: inline-block;
    }
    .ticket-status-badge.closed {
        background: rgba(255, 71, 87, 0.25);
        color: #ff4757;
        border-color: rgba(255, 71, 87, 0.4);
    }
    .btn-view-ticket {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        padding: 6px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.88rem;
        transition: background 0.2s;
        display: inline-block;
    }
    .btn-view-ticket:hover {
        background: rgba(255, 255, 255, 0.35);
        color: #ffffff;
    }
</style>

<div class="container-fluid py-4" style="max-width: 1200px;">
    <!-- Top Header Bar -->
    <div class="support-header-bar">
        <h1 class="support-title">Support Center</h1>
        <a href="/support/create" class="btn-new-ticket">
            <ion-icon name="add-outline" style="font-size: 1.3rem;"></ion-icon> New Ticket
        </a>
    </div>

    <!-- Contact Info Bar -->
    <div class="contact-info-card">
        <div class="contact-item">
            <div class="contact-icon">
                <ion-icon name="call-outline"></ion-icon>
            </div>
            <div>
                <div class="contact-label">Direct Support Phone</div>
                <a href="tel:07050409050" class="contact-value">07050409050</a>
            </div>
        </div>
        <div class="contact-item">
            <div class="contact-icon">
                <ion-icon name="mail-outline"></ion-icon>
            </div>
            <div>
                <div class="contact-label">Official Support Email</div>
                <a href="mailto:support@casjoe.com" class="contact-value">support@casjoe.com</a>
            </div>
        </div>
        <div class="contact-item">
            <div class="contact-icon" style="background: rgba(255, 166, 0, 0.12); color: #FFA600;">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
            </div>
            <div>
                <div class="contact-label">Support Access</div>
                <div class="contact-value" style="color: #000066;">Cori AI Tokens & Human Helpdesk</div>
            </div>
        </div>
    </div>

    <!-- Embedded AI Support Chat Section -->
    <div class="ai-chat-embedded">
        <div class="ai-chat-header">
            <div class="ai-chat-title-box">
                <div class="ai-chat-icon">
                    <ion-icon name="sparkles"></ion-icon>
                </div>
                <div>
                    <h3 class="text-white fw-bold mb-1" style="font-size: 1.45rem;">Cori AI Support Specialist</h3>
                    <p class="text-light text-opacity-75 mb-0 small">Get instant technical troubleshooting right here inside the Support Center before opening a human ticket.</p>
                </div>
            </div>
            <span class="ai-token-badge">
                <ion-icon name="flash"></ion-icon> POWERED BY CORI AI TOKENS (NO SUBSCRIPTION REQUIRED)
            </span>
        </div>

        <div class="ai-input-wrapper">
            <input type="text" id="cori-chat-input" class="ai-chat-input" placeholder="Type your support issue or question here (e.g., How do I set up virtual cards? How do I verify my KYC?)..." onkeypress="if(event.key==='Enter') sendCoriSupportChat()">
            <button type="button" class="btn-ai-send" id="cori-chat-btn" onclick="sendCoriSupportChat()">
                <ion-icon name="send"></ion-icon> Ask Cori AI
            </button>
        </div>

        <div id="cori-chat-output" class="ai-chat-output-box"></div>
    </div>

    <!-- Tickets Table Card (Matching Screenshot Layout) -->
    <div class="tickets-table-card">
        <?php if (empty($tickets)): ?>
            <div class="text-center py-5 text-white">
                <ion-icon name="document-text-outline" style="font-size: 3rem; opacity: 0.6; margin-bottom: 10px;"></ion-icon>
                <h4 class="fw-bold mb-1">No Support Tickets Found</h4>
                <p class="mb-4 text-light text-opacity-75">You haven't submitted any human support tickets yet. You can ask Cori AI above or create a new ticket.</p>
                <a href="/support/create" class="btn-new-ticket">
                    <ion-icon name="add-outline"></ion-icon> New Ticket
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45%;">Subject</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 25%;">Created</th>
                            <th style="width: 15%; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['subject'] ?? 'Untitled Issue') ?></td>
                                <td>
                                    <?php if (($ticket['status'] ?? 'open') === 'closed'): ?>
                                        <span class="ticket-status-badge closed">Closed</span>
                                    <?php else: ?>
                                        <span class="ticket-status-badge">Open</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: rgba(255, 255, 255, 0.85); font-weight: 500;">
                                    <?= isset($ticket['created_at']) ? date('M j, Y', strtotime($ticket['created_at'])) : 'Recent' ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/support/view?id=<?= $ticket['id'] ?? 0 ?>" class="btn-view-ticket">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function sendCoriSupportChat() {
    const input = document.getElementById('cori-chat-input');
    const btn = document.getElementById('cori-chat-btn');
    const output = document.getElementById('cori-chat-output');
    const message = input.value.trim();

    if (!message) {
        input.focus();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Analyzing...';
    output.style.display = 'block';
    output.innerHTML = '<div class="d-flex align-items-center gap-2 text-warning"><span class="spinner-border spinner-border-sm"></span> <strong>Cori AI Support Specialist is troubleshooting your question...</strong></div>';

    const formData = new FormData();
    formData.append('message', message);

    fetch('/support/ai-chat', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="send"></ion-icon> Ask Cori AI';

        if (data.success) {
            output.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light border-opacity-10 pb-2">
                    <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                        <ion-icon name="sparkles" style="font-size: 1.3rem;"></ion-icon>
                        <span>CORI AI SUPPORT SPECIALIST RESPONSE</span>
                    </div>
                    <span class="badge bg-dark border border-warning text-warning small">Token Applied</span>
                </div>
                <div style="white-space: pre-wrap; font-size: 0.98rem; line-height: 1.65; color: #f8fafc;">${data.reply}</div>
                <div class="mt-4 pt-3 border-top border-light border-opacity-10 d-flex justify-content-between align-items-center flex-wrap gap-2 text-light text-opacity-75 small">
                    <span>⚡ Deducted from your Cori AI token balance. Need further help? Click 'New Ticket' above to reach our engineers.</span>
                    <button type="button" class="btn btn-sm btn-outline-warning fw-bold" onclick="navigator.clipboard.writeText(${JSON.stringify(data.reply)}); alert('Copied solution to clipboard!');">Copy Answer</button>
                </div>
            `;
            input.value = '';
        } else {
            output.innerHTML = `
                <div class="text-danger fw-bold d-flex align-items-center gap-2">
                    <ion-icon name="alert-circle" style="font-size: 1.4rem;"></ion-icon>
                    <span>${data.error || 'Failed to get AI response.'}</span>
                </div>
                ${data.exhausted ? '<div class="mt-3"><a href="/billing" class="btn btn-warning fw-bold px-4 py-2">Top Up Cori AI Credits</a></div>' : ''}
            `;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="send"></ion-icon> Ask Cori AI';
        output.innerHTML = `<div class="text-danger fw-bold"><ion-icon name="alert-circle"></ion-icon> Network error connecting to Cori AI Support service.</div>`;
    });
}
</script>

<?php require __DIR__ . '/../global_footer.php'; ?>
