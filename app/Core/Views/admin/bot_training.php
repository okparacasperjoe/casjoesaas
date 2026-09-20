<?php
$pageTitle = 'Chatbot Training';
require __DIR__ . '/header.php';
?>
<style>
        .app-container { min-height: 100vh; display: flex; }
        .app-container { min-height: 100vh; display: flex; }

        /* Sidebar Link Reset */
        a { text-decoration: none; }
        
        .nav-menu { list-style: none; padding: 0; margin: 20px 0; }

        @media (max-width: 768px) {
            .app-container { flex-direction: column; }
        }
    </style>
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h3 mb-0" style="color: #ffffff; font-weight: 700;">🤖 Chatbot Training</h1>
                <p style="color: #94a3b8; margin-bottom: 0;">Teach Cori AI how to respond to specific keywords.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="/<?= ADMIN_PATH ?>" class="btn btn-outline-secondary">
                    <ion-icon name="arrow-back-outline" class="me-1"></ion-icon> Dashboard
                </a>
            </div>
        </div>

        <!-- Add New Rule -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Add New Rule</h5>
            </div>
            <div class="card-body">
                <form action="/casper-joe/bot-training/save" method="POST">
                    <?= \App\Core\Services\CsrfService::getTokenField() ?>
                    <input type="hidden" name="id" id="rule-id" value="0">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Keywords</label>
                            <input type="text" name="keywords" id="rule-keywords" class="form-control" placeholder="e.g. pricing, cost" required>
                            <div class="form-text">Comma separated. Matches ANY word.</div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Bot Response</label>
                            <textarea name="response" id="rule-response" class="form-control" rows="2" placeholder="The answer..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" id="save-btn" class="btn text-white" style="background-color: #000066;">
                        <ion-icon name="save-outline" class="me-1"></ion-icon> Save Rule
                    </button>
                    <button type="button" id="cancel-btn" class="btn btn-secondary ms-2" style="display:none;" onclick="cancelEdit()">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Existing Rules -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Existing Rules</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Keywords</th>
                            <th>Response</th>
                            <th class="text-center">Matches</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rules)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No rules yet. Add one above! 🧠</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rules as $rule): ?>
                            <tr>
                                <td style="width: 25%;">
                                    <?php foreach(explode(',', $rule['keywords']) as $k): ?>
                                        <span class="badge bg-light text-dark border me-1"><?= trim($k) ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($rule['response'])) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill"><?= $rule['matches_count'] ?></span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                            onclick='editRule(<?= $rule["id"] ?>, <?= htmlspecialchars(json_encode($rule["keywords"]), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($rule["response"]), ENT_QUOTES) ?>)'>
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    <form action="/casper-joe/bot-training/delete" method="POST" onsubmit="return confirm('Delete this rule?');" style="display:inline;">
                                        <?= \App\Core\Services\CsrfService::getTokenField() ?>
                                        <input type="hidden" name="id" value="<?= $rule['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<script>
    function editRule(id, keywords, response) {
        document.getElementById('rule-id').value = id;
        document.getElementById('rule-keywords').value = keywords;
        document.getElementById('rule-response').value = response;
        
        document.getElementById('save-btn').innerHTML = '<ion-icon name="save-outline" class="me-1"></ion-icon> Update Rule';
        document.getElementById('cancel-btn').style.display = 'inline-block';
        window.scrollTo(0, 0);
    }

    function cancelEdit() {
        document.getElementById('rule-id').value = 0;
        document.getElementById('rule-keywords').value = '';
        document.getElementById('rule-response').value = '';
        
        document.getElementById('save-btn').innerHTML = '<ion-icon name="save-outline" class="me-1"></ion-icon> Save Rule';
        document.getElementById('cancel-btn').style.display = 'none';
    }
</script>
<?php require __DIR__ . '/footer.php'; ?>
