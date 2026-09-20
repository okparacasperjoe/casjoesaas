<?php
$title = "Ticket #" . ($ticket['id'] ?? '') . " | Casjoe Priority Support";
require __DIR__ . '/../../../Core/Views/global_header.php';
?>
<style>
    .support-ticket-header {
        background: linear-gradient(135deg, rgba(0, 0, 102, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .ticket-chat-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 30px;
    }
    .ticket-msg-bubble {
        max-width: 82%;
        padding: 20px;
        border-radius: 16px;
        line-height: 1.6;
        position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    }
    .ticket-msg-bubble.user {
        align-self: flex-end;
        background: linear-gradient(135deg, #000066 0%, #0c1a40 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-bottom-right-radius: 4px;
    }
    .ticket-msg-bubble.staff {
        align-self: flex-start;
        background: linear-gradient(135deg, rgba(255, 166, 0, 0.15) 0%, rgba(15, 23, 42, 0.9) 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 166, 0, 0.35);
        border-bottom-left-radius: 4px;
    }
    .ticket-msg-meta {
        font-size: 0.78rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.9;
    }
    .ticket-reply-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 25px;
    }
    .ai-ticket-assistant {
        background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(0, 0, 102, 0.65));
        border: 1px solid rgba(255, 166, 0, 0.35);
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 25px;
    }
</style>

<div class="container-fluid py-4" style="max-width: 1050px;">
    <!-- Ticket Header Bar -->
    <div class="support-ticket-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-dark border border-secondary text-light px-3 py-1 rounded-pill">TICKET #<?= $ticket['id'] ?></span>
                
                <?php
                    $priorityColor = match($ticket['priority'] ?? 'medium') {
                        'high', 'urgent' => '#ff4757',
                        'medium' => '#FFA600',
                        default => '#70a1ff'
                    };
                ?>
                <span class="badge px-3 py-1 rounded-pill" style="background: rgba(0,0,0,0.3); color: <?= $priorityColor ?>; border: 1px solid <?= $priorityColor ?>;">
                    Priority: <?= strtoupper($ticket['priority'] ?? 'medium') ?>
                </span>

                <span class="badge px-3 py-1 rounded-pill" style="background: <?= $ticket['status'] === 'closed' ? 'rgba(255, 71, 87, 0.2)' : 'rgba(46, 213, 115, 0.2)' ?>; color: <?= $ticket['status'] === 'closed' ? '#ff4757' : '#2ed573' ?>; border: 1px solid <?= $ticket['status'] === 'closed' ? 'rgba(255, 71, 87, 0.4)' : 'rgba(46, 213, 115, 0.4)' ?>;">
                    Status: <?= strtoupper($ticket['status']) ?>
                </span>
            </div>
            <h2 class="text-white fw-bold mb-1" style="font-size: 1.8rem;"><?= htmlspecialchars($ticket['subject']) ?></h2>
            <p class="text-muted small mb-0">Opened on <?= date('F j, Y \a\t g:i a', strtotime($ticket['created_at'])) ?></p>
        </div>
        <a href="/support" class="btn btn-outline-light fw-bold px-4 py-2 d-flex align-items-center gap-2" style="border-radius: 10px;">
            <ion-icon name="arrow-back-outline"></ion-icon> Back to Tickets
        </a>
    </div>

    <!-- AI Instant Ticket Assistant -->
    <div class="ai-ticket-assistant shadow-lg">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2 text-warning fw-bold">
                <ion-icon name="sparkles" style="font-size: 1.3rem;"></ion-icon>
                <span style="font-size: 1.05rem;">Cori AI Ticket Assistant</span>
            </div>
            <span class="badge bg-dark border border-warning text-warning" style="font-size: 0.75rem;">Uses Cori AI Tokens (No Subscription Required)</span>
        </div>
        <p class="text-muted small mb-3">Don't want to wait for an engineer reply? Ask Cori AI to inspect this conversation and give you an instant diagnostic fix right now.</p>
        <button type="button" class="btn btn-warning fw-bold px-4 py-2 d-flex align-items-center gap-2" onclick="analyzeTicketWithCori(<?= $ticket['id'] ?>)" id="cori-analyze-btn" style="background: #FFA600; color: #000066; border: none; border-radius: 8px;">
            <ion-icon name="flash"></ion-icon> Ask Cori AI for Instant Solution
        </button>
        <div id="cori-analyze-result" class="mt-3 p-3 rounded text-white" style="background: rgba(0,0,0,0.45); border: 1px solid rgba(255,255,255,0.1); display: none;"></div>
    </div>

    <!-- Chat Messages Flow -->
    <div class="ticket-chat-container">
        <?php foreach ($messages as $msg): ?>
            <div class="ticket-msg-bubble <?= $msg['is_staff'] ? 'staff' : 'user' ?>">
                <div class="ticket-msg-meta <?= $msg['is_staff'] ? 'text-warning' : 'text-info' ?>">
                    <ion-icon name="<?= $msg['is_staff'] ? 'shield-checkmark' : 'person' ?>"></ion-icon>
                    <span class="fw-bold"><?= $msg['is_staff'] ? 'Casjoe Support Specialist' : 'You' ?></span>
                    <span class="text-muted">&bull; <?= date('M j, Y, g:i a', strtotime($msg['created_at'])) ?></span>
                </div>
                <div style="font-size: 0.98rem; white-space: pre-wrap; word-break: break-word;">
                    <?= htmlspecialchars($msg['message']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Reply Card -->
    <div class="ticket-reply-card shadow-lg">
        <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
            <ion-icon name="chatbox-ellipses-outline" class="text-warning"></ion-icon> Add Reply to Ticket
        </h5>
        <?php if ($ticket['status'] === 'closed'): ?>
            <div class="alert alert-warning py-2 small mb-3 border border-warning border-opacity-25 bg-warning bg-opacity-10 text-warning d-flex align-items-center gap-2">
                <ion-icon name="information-circle"></ion-icon>
                <span>This ticket is currently closed. Replying below will automatically reopen it.</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/support/view?id=<?= $ticket['id'] ?>">
            <div class="mb-3">
                <textarea name="message" class="form-control" rows="5" required placeholder="Type your response or additional information for our engineering team..." style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 10px; padding: 15px;"></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-warning fw-bold px-5 py-2 d-flex align-items-center gap-2 shadow-lg" style="background: #FFA600; color: #000066; border: none; border-radius: 10px;">
                    <ion-icon name="send"></ion-icon> Send Response
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function analyzeTicketWithCori(ticketId) {
    const btn = document.getElementById('cori-analyze-btn');
    const resultBox = document.getElementById('cori-analyze-result');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inspecting Ticket History...';
    resultBox.style.display = 'block';
    resultBox.innerHTML = '<div class="text-warning d-flex align-items-center gap-2"><span class="spinner-border spinner-border-sm"></span> <strong>Cori AI is reviewing all messages and system state...</strong></div>';

    const formData = new FormData();
    formData.append('ticket_id', ticketId);
    formData.append('message', 'Please review all details and history of this ticket and provide a comprehensive diagnostic fix and troubleshooting steps.');

    fetch('/support/ai-chat', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="flash"></ion-icon> Ask Cori AI for Instant Solution';

        if (data.success) {
            resultBox.innerHTML = `
                <div class="d-flex align-items-center gap-2 text-warning mb-2">
                    <ion-icon name="sparkles" style="font-size: 1.2rem;"></ion-icon>
                    <strong style="letter-spacing: 0.5px;">CORI AI INSTANT RESOLUTION PLAN</strong>
                </div>
                <div style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">${data.reply}</div>
                <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 text-muted small d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span><ion-icon name="flash"></ion-icon> Token cost deducted from your Cori AI balance</span>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="navigator.clipboard.writeText(${JSON.stringify(data.reply)}); alert('Copied to clipboard!');">Copy Solution</button>
                </div>
            `;
        } else {
            resultBox.innerHTML = `
                <div class="text-danger fw-bold"><ion-icon name="alert-circle"></ion-icon> ${data.error || 'Unable to analyze ticket.'}</div>
                ${data.exhausted ? '<div class="mt-2"><a href="/billing" class="btn btn-sm btn-warning fw-bold">Top Up AI Credits</a></div>' : ''}
            `;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="flash"></ion-icon> Ask Cori AI for Instant Solution';
        resultBox.innerHTML = `<div class="text-danger fw-bold"><ion-icon name="alert-circle"></ion-icon> Network or server error while connecting to Cori AI.</div>`;
    });
}
</script>

<?php require __DIR__ . '/../../../Core/Views/global_footer.php'; ?>
