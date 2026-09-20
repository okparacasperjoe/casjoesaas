<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta charset="UTF-8">
    <title>Create Document for Signing | Casjoe ERP</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        .template-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
        }
        html.dark-theme .template-card {
            background: #1f2937;
            border-color: rgba(255,255,255,0.1);
        }
        .template-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .template-card.active {
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.04);
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h2>Create Document for Signing</h2>
                <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Compose an agreement or select a pre-made legal template, then generate a secure signing link</p>
            </div>
            <a href="/erp/documents" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                <ion-icon name="arrow-back-outline"></ion-icon> Back to Documents
            </a>
        </div>

        <div style="max-width: 960px; margin: 20px auto;">
            <!-- Ready-Made Templates Picker -->
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="font-size: 15px; margin-top: 0; margin-bottom: 12px; color: #475569;">
                    <ion-icon name="flash-outline" style="color: #f59e0b;"></ion-icon> Quick Start Templates (Click to Auto-fill)
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                    <div class="template-card" onclick="loadTemplate('nda')">
                        <ion-icon name="shield-checkmark-outline" style="font-size: 24px; color: #2563eb;"></ion-icon>
                        <div>
                            <div style="font-weight: 600; font-size: 13px;">Non-Disclosure Agreement</div>
                            <div style="font-size: 11px; color: #64748b;">Standard Mutual NDA</div>
                        </div>
                    </div>
                    <div class="template-card" onclick="loadTemplate('service')">
                        <ion-icon name="briefcase-outline" style="font-size: 24px; color: #16a34a;"></ion-icon>
                        <div>
                            <div style="font-weight: 600; font-size: 13px;">Service Agreement</div>
                            <div style="font-size: 11px; color: #64748b;">Client Scope & Terms</div>
                        </div>
                    </div>
                    <div class="template-card" onclick="loadTemplate('contractor')">
                        <ion-icon name="hammer-outline" style="font-size: 24px; color: #d97706;"></ion-icon>
                        <div>
                            <div style="font-weight: 600; font-size: 13px;">Independent Contractor</div>
                            <div style="font-size: 11px; color: #64748b;">Freelance / Vendor Work</div>
                        </div>
                    </div>
                    <div class="template-card" onclick="loadTemplate('offer')">
                        <ion-icon name="person-add-outline" style="font-size: 24px; color: #9333ea;"></ion-icon>
                        <div>
                            <div style="font-weight: 600; font-size: 13px;">Offer Letter</div>
                            <div style="font-size: 11px; color: #64748b;">Staff Employment Offer</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Form -->
            <div class="card">
                <form action="/erp/documents/store" method="POST" id="docForm">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Document Title *</label>
                            <input type="text" name="title" id="docTitle" class="form-control" placeholder="e.g. Master Services Agreement - Acme Corp" required>
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Category</label>
                            <select name="category" id="docCategory" class="form-control">
                                <option value="contract">Client Contract / Agreement</option>
                                <option value="nda">Non-Disclosure Agreement (NDA)</option>
                                <option value="proposal">Sales Proposal</option>
                                <option value="offer_letter">Employment Offer Letter</option>
                                <option value="agreement">Vendor / Supplier Agreement</option>
                                <option value="other">Other Document</option>
                            </select>
                        </div>
                    </div>

                    <!-- Recipient Details Section -->
                    <div style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.06); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                        <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                            <ion-icon name="person-outline"></ion-icon> Recipient Details (Signer)
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Recipient Type</label>
                                <select name="recipient_type" class="form-control">
                                    <option value="client">Client / Customer</option>
                                    <option value="vendor">Vendor / Supplier</option>
                                    <option value="employee">Employee / Staff</option>
                                    <option value="other">External Partner</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Signer Full Name *</label>
                                <input type="text" name="recipient_name" id="recipientName" class="form-control" placeholder="e.g. John Doe" required>
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Signer Email Address *</label>
                                <input type="email" name="recipient_email" id="recipientEmail" class="form-control" placeholder="e.g. client@example.com" required>
                            </div>
                        </div>

                        <div style="margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Signing Deadline (Optional)</label>
                                <input type="date" name="signing_deadline" class="form-control" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Auto-fill from CRM / HR (Optional)</label>
                                <select class="form-control" onchange="autoFillRecipient(this)">
                                    <option value="">-- Select Contact from System --</option>
                                    <?php if (!empty($clients)): ?>
                                        <optgroup label="Clients">
                                            <?php foreach ($clients as $c): ?>
                                                <option value="<?= htmlspecialchars($c['email']) ?>" data-name="<?= htmlspecialchars($c['name']) ?>">Client: <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if (!empty($vendors)): ?>
                                        <optgroup label="Vendors">
                                            <?php foreach ($vendors as $v): ?>
                                                <option value="<?= htmlspecialchars($v['email']) ?>" data-name="<?= htmlspecialchars($v['name']) ?>">Vendor: <?= htmlspecialchars($v['name']) ?> (<?= htmlspecialchars($v['email']) ?>)</option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if (!empty($employees)): ?>
                                        <optgroup label="Employees">
                                            <?php foreach ($employees as $e): ?>
                                                <option value="<?= htmlspecialchars($e['email']) ?>" data-name="<?= htmlspecialchars($e['name']) ?>">Employee: <?= htmlspecialchars($e['name']) ?> (<?= htmlspecialchars($e['email']) ?>)</option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="form-group" style="margin-bottom: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label style="font-weight: 600;">Document Content & Terms *</label>
                            <span style="font-size: 12px; color: #64748b;">Supports plain text & legal clause formatting</span>
                        </div>
                        <textarea name="content" id="docContent" class="form-control" style="width: 100%; min-height: 380px; font-family: monospace, sans-serif; font-size: 14px; line-height: 1.6; padding: 14px;" required placeholder="Type or paste the full agreement terms here..."></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; align-items: center;">
                        <a href="/erp/documents" class="btn btn-outline">Cancel</a>
                        <button type="submit" name="submit_action" value="draft" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 6px;">
                            <ion-icon name="save-outline"></ion-icon> Save as Draft
                        </button>
                        <button type="submit" name="submit_action" value="send" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px;">
                            <ion-icon name="send-outline"></ion-icon> Create & Ready for Signing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
const templates = {
    nda: {
        title: "Mutual Non-Disclosure Agreement (NDA)",
        category: "nda",
        content: `MUTUAL NON-DISCLOSURE AGREEMENT (NDA)

This Mutual Non-Disclosure Agreement (the "Agreement") is entered into on [DATE], by and between:
Party A: [COMPANY_NAME] ("Discloser")
Party B: [RECIPIENT_NAME] ("Recipient")

1. PURPOSE OF DISCLOSURE
The parties wish to explore business opportunities and in connection with this purpose, each party may disclose confidential and proprietary information to the other.

2. CONFIDENTIAL INFORMATION
"Confidential Information" refers to any non-public technical, business, financial, or commercial information disclosed by either party, whether orally, in writing, or electronically.

3. OBLIGATIONS OF RECEIVING PARTY
The receiving party agrees to:
(a) Protect and safeguard the Confidential Information with at least the same degree of care used for its own confidential information.
(b) Use the Confidential Information solely for the Purpose described above.
(c) Not disclose any Confidential Information to third parties without prior written consent.

4. TERM & TERMINATION
This Agreement shall remain in effect for a period of two (2) years from the date of execution.

5. GOVERNING LAW
This Agreement shall be governed by and construed in accordance with applicable laws.

IN WITNESS WHEREOF, the parties have executed this Agreement as of the date first written above.

Signed on behalf of Discloser:
[COMPANY_NAME]

Signed on behalf of Recipient:
[RECIPIENT_NAME]`
    },
    service: {
        title: "Professional Services Agreement",
        category: "contract",
        content: `PROFESSIONAL SERVICES AGREEMENT

This Services Agreement (the "Agreement") is made effective as of [DATE], by and between:
Service Provider: [COMPANY_NAME]
Client: [RECIPIENT_NAME]

1. SCOPE OF SERVICES
The Service Provider agrees to perform professional services, deliverables, and consultations as mutually agreed upon in work orders or project specifications.

2. COMPENSATION & PAYMENT TERMS
Client agrees to compensate the Service Provider according to the agreed fees. Invoices are payable within fourteen (14) calendar days of issuance unless otherwise stipulated.

3. TERM AND TERMINATION
Either party may terminate this Agreement by giving thirty (30) days prior written notice. Upon termination, Client shall pay for all services rendered up to the termination date.

4. INTELLECTUAL PROPERTY
Upon receipt of full payment, all deliverables created specifically for the Client shall become the property of the Client, excluding pre-existing tools and materials.

5. WARRANTIES & LIABILITY
The Service Provider warrants that all services will be executed in a professional, workmanlike manner in accordance with industry standards.

ACCEPTED AND AGREED:
Service Provider: [COMPANY_NAME]
Client: [RECIPIENT_NAME]`
    },
    contractor: {
        title: "Independent Contractor Agreement",
        category: "agreement",
        content: `INDEPENDENT CONTRACTOR AGREEMENT

This Independent Contractor Agreement is entered into on [DATE] between:
Company: [COMPANY_NAME]
Contractor: [RECIPIENT_NAME]

1. ENGAGEMENT
The Company engages the Contractor, and the Contractor agrees to perform specialized contracting services in accordance with project scopes and deadlines.

2. INDEPENDENT CONTRACTOR STATUS
The Contractor is an independent contractor, not an employee or agent of the Company. The Contractor is responsible for all applicable taxes and statutory benefits.

3. CONFIDENTIALITY
Contractor agrees not to disclose or use any proprietary information, client lists, or internal data of the Company at any time during or after this engagement.

4. OWNERSHIP OF WORK PRODUCT
All copyrights, inventions, work products, and code created for the Company under this Agreement shall belong solely and exclusively to the Company.

AGREED AND ACCEPTED:
For the Company: [COMPANY_NAME]
Contractor: [RECIPIENT_NAME]`
    },
    offer: {
        title: "Employment Offer Letter",
        category: "offer_letter",
        content: `EMPLOYMENT OFFER LETTER

Date: [DATE]
To: [RECIPIENT_NAME]

Dear [RECIPIENT_NAME],

On behalf of [COMPANY_NAME], we are pleased to offer you employment for the position of [JOB_TITLE].

1. COMMENCEMENT & DUTIES
Your start date will be [START_DATE]. You will report directly to management and perform duties customary to this role.

2. COMPENSATION & BENEFITS
Your base salary will be [SALARY_AMOUNT], paid on a monthly basis in accordance with standard payroll cycles. You will also be eligible for company statutory benefits and leave allowances.

3. AT-WILL EMPLOYMENT & POLICIES
Your employment is subject to company policies, handbooks, and standard code of conduct.

To accept this offer, please electronically sign and date this document below.

Warm regards,
Management
[COMPANY_NAME]

ACCEPTANCE OF OFFER:
I accept the offer of employment as outlined above.

Candidate Signature: [RECIPIENT_NAME]
Date: [DATE]`
    }
};

function loadTemplate(key) {
    if (templates[key]) {
        const t = templates[key];
        document.getElementById('docTitle').value = t.title;
        document.getElementById('docCategory').value = t.category;
        document.getElementById('docContent').value = t.content;
    }
}

function autoFillRecipient(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value) {
        document.getElementById('recipientEmail').value = selectedOption.value;
        const name = selectedOption.getAttribute('data-name');
        if (name) {
            document.getElementById('recipientName').value = name;
        }
    }
}
</script>
</body>
</html>
