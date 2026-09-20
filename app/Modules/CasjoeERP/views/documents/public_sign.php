<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title>Review & Sign: <?= htmlspecialchars($doc['title']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px 10px;
        }
        .sign-container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .sign-header {
            background: #0f172a;
            color: #ffffff;
            padding: 24px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .sign-body {
            padding: 30px;
        }
        .doc-paper {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.7;
            white-space: pre-wrap;
            font-family: inherit;
        }
        .canvas-wrapper {
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            background: #ffffff;
            display: inline-block;
            touch-action: none;
            width: 100%;
            max-width: 500px;
        }
        #sigCanvas {
            width: 100%;
            height: 160px;
            cursor: crosshair;
            display: block;
        }
        .alert-completed {
            background: #ecfdf5;
            border: 1px solid #34d399;
            color: #065f46;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
<div class="sign-container">
    <div class="sign-header">
        <div>
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">Document for E-Signature</div>
            <h1 style="font-size: 20px; margin: 4px 0 0 0;"><?= htmlspecialchars($doc['title']) ?></h1>
        </div>
        <div style="text-align: right;">
            <span style="background: rgba(255,255,255,0.15); font-size: 12px; padding: 4px 10px; border-radius: 20px;">
                Secured by Casjoe
            </span>
        </div>
    </div>

    <div class="sign-body">
        <?php if (isset($_GET['completed']) || $doc['status'] === 'signed'): ?>
            <div class="alert-completed">
                <ion-icon name="checkmark-circle" style="font-size: 48px; color: #16a34a;"></ion-icon>
                <h2 style="margin: 10px 0 6px 0; color: #065f46;">Document Successfully Signed!</h2>
                <p style="margin: 0 0 16px 0; font-size: 14px;">
                    Thank you, <strong><?= htmlspecialchars($doc['signed_name'] ?: $doc['recipient_name']) ?></strong>. Your electronic signature has been legally timestamped and verified.
                </p>
                <div style="font-size: 13px; color: #047857; margin-bottom: 16px;">
                    Signed on <?= date('F d, Y - h:i A T', strtotime($doc['signed_at'] ?? date('Y-m-d H:i:s'))) ?>
                </div>
                <button onclick="window.print()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;">
                    <ion-icon name="print-outline"></ion-icon> Print / Download Copy
                </button>
            </div>
        <?php elseif (isset($_GET['declined']) || $doc['status'] === 'declined'): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 24px;">
                <ion-icon name="close-circle-outline" style="font-size: 48px; color: #dc2626;"></ion-icon>
                <h2 style="margin: 10px 0 6px 0;">Signing Declined</h2>
                <p style="margin: 0; font-size: 14px;">You have declined to sign this document. The sender has been notified.</p>
            </div>
        <?php endif; ?>

        <!-- Document Details Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 13px; color: #64748b; flex-wrap: wrap; gap: 10px;">
            <div>
                Sender: <strong><?= htmlspecialchars($companyName) ?></strong><br>
                Signer: <strong><?= htmlspecialchars($doc['recipient_name']) ?></strong> (<?= htmlspecialchars($doc['recipient_email']) ?>)
            </div>
            <div>
                <?php if (!empty($doc['signing_deadline'])): ?>
                    <span style="color: #dc2626; font-weight: 600;">
                        Signing Deadline: <?= date('M d, Y', strtotime($doc['signing_deadline'])) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Document Content Body -->
        <div class="doc-paper"><?= htmlspecialchars($doc['content']) ?></div>

        <?php if ($doc['status'] !== 'signed' && $doc['status'] !== 'declined'): ?>
            <!-- Interactive Signing Pad Form -->
            <form action="/sign/<?= $doc['uuid'] ?>/submit" method="POST" id="signForm" onsubmit="return handleFormSubmit()">
                <input type="hidden" name="uuid" value="<?= htmlspecialchars($doc['uuid']) ?>">
                <input type="hidden" name="signature" id="signatureInput">

                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                    <h3 style="margin: 0 0 14px 0; font-size: 16px; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                        <ion-icon name="pencil-outline"></ion-icon> Draw or Provide Your Signature
                    </h3>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Draw Your Signature with Finger or Mouse *</label>
                        <div class="canvas-wrapper">
                            <canvas id="sigCanvas"></canvas>
                        </div>
                        <div style="margin-top: 6px;">
                            <button type="button" onclick="clearSignature()" class="btn btn-sm btn-outline" style="font-size: 12px; padding: 4px 10px;">
                                Clear Signature
                            </button>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Your Legal Full Name *</label>
                            <input type="text" name="signed_name" id="signedName" class="form-control" value="<?= htmlspecialchars($doc['recipient_name']) ?>" required style="background: #ffffff;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px;">Signing Date</label>
                            <input type="text" class="form-control" value="<?= date('F d, Y') ?>" readonly style="background: #f1f5f9; color: #64748b;">
                        </div>
                    </div>

                    <div style="margin-top: 14px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 13px; cursor: pointer;">
                            <input type="checkbox" name="agreed" id="agreeCheck" required style="margin-top: 3px; cursor: pointer; width: 16px; height: 16px;">
                            <span>
                                I, <strong><?= htmlspecialchars($doc['recipient_name']) ?></strong>, confirm that I have read, understood, and agree to be legally bound by this document. I intend my signature above to be my legally-binding digital signature.
                            </span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <button type="button" onclick="toggleDeclineModal()" style="background: transparent; color: #64748b; border: none; cursor: pointer; font-size: 13px; text-decoration: underline;">
                        Decline to Sign
                    </button>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <ion-icon name="checkmark-circle-outline" style="font-size: 20px;"></ion-icon> Sign & Submit Agreement
                    </button>
                </div>
            </form>

            <!-- Decline Modal -->
            <div id="declineModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 15px;">
                <div style="background: #fff; border-radius: 8px; max-width: 450px; width: 100%; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                    <h3 style="margin-top: 0;">Decline to Sign Document</h3>
                    <p style="font-size: 13px; color: #64748b;">Please let the sender know why you are declining this document:</p>
                    <form action="/sign/<?= $doc['uuid'] ?>/decline" method="POST">
                        <input type="hidden" name="uuid" value="<?= htmlspecialchars($doc['uuid']) ?>">
                        <textarea name="declined_reason" class="form-control" rows="3" placeholder="Reason for declining..." required style="margin-bottom: 14px; width: 100%;"></textarea>
                        <div style="display: flex; justify-content: flex-end; gap: 10px;">
                            <button type="button" onclick="toggleDeclineModal()" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-sm" style="background: #dc2626; color: #fff; border: none; padding: 8px 14px;">Confirm Decline</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const canvas = document.getElementById('sigCanvas');
if (canvas) {
    const ctx = canvas.getContext('2d');
    let isDrawing = false;

    // Set high-res canvas scaling
    function resizeCanvas() {
        const rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = 160;
        ctx.lineWidth = 2.5;
        ctx.strokeStyle = '#0f172a';
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    function getCoords(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: e.touches[0].clientX - rect.left,
                y: e.touches[0].clientY - rect.top
            };
        }
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function startDraw(e) {
        isDrawing = true;
        const pos = getCoords(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    }

    function draw(e) {
        if (!isDrawing) return;
        if (e.cancelable) e.preventDefault();
        const pos = getCoords(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function stopDraw() {
        isDrawing = false;
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw, { passive: false });

    window.clearSignature = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    };

    function isCanvasBlank() {
        const pixelBuffer = new Uint32Array(
            ctx.getImageData(0, 0, canvas.width, canvas.height).data.buffer
        );
        return !pixelBuffer.some(color => color !== 0);
    }

    window.handleFormSubmit = function() {
        if (isCanvasBlank()) {
            alert('Please draw your signature on the signature pad before submitting.');
            return false;
        }
        if (!document.getElementById('agreeCheck').checked) {
            alert('Please check the agreement box to confirm your consent.');
            return false;
        }
        document.getElementById('signatureInput').value = canvas.toDataURL('image/png');
        return true;
    };
}

function toggleDeclineModal() {
    const m = document.getElementById('declineModal');
    if (m) {
        m.style.display = (m.style.display === 'flex') ? 'none' : 'flex';
    }
}
</script>
</body>
</html>
