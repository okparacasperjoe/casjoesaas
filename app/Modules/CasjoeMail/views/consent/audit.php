<?php
/**
 * Consent Audit Trail View
 * Displays all consent records for compliance
 */
$active = 'consent';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Audit Trail | Casjoe Mail</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
<?php require_once dirname(__DIR__, 4) . '/Core/Views/partials/mobile_nav.php'; ?>
<div class="app-container">
    <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

    <main class="main-content">
        <div class="top-bar">
            <h2>🛡️ Consent Audit Trail</h2>
            <a href="/mail" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

    <!-- Consent Statistics -->
    <div class="grid-4" style="margin-bottom: 30px;">
        <?php
        $consentController = new \App\Modules\CasjoeMail\Controllers\ConsentController();
        $stats = $consentController->getConsentStats();
        $consentRate = $stats['total_subscribers'] > 0 
            ? round(($stats['with_consent'] / $stats['total_subscribers']) * 100, 1) 
            : 0;
        ?>
        
        <div class="card fintech-card" style="background: #FFA600; color: white; border-left: none;">
            <h3 style="color: white;"><?php echo number_format($stats['with_consent'] ?? 0); ?></h3>
            <div style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Verified Consent</div>
        </div>
        
        <div class="card fintech-card" style="background: <?php echo $consentRate >= 80 ? '#28a745' : '#ffc107'; ?>; color: white; border-left: none;">
            <h3 style="color: white;"><?php echo $consentRate; ?>%</h3>
            <div style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Consent Health</div>
        </div>
        
        <div class="card fintech-card" style="background: #17a2b8; color: white; border-left: none;">
            <h3 style="color: white;"><?php echo number_format($stats['from_forms'] ?? 0); ?></h3>
            <div style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Form Opt-Ins</div>
        </div>
        
        <div class="card fintech-card" style="background: #6c757d; color: white; border-left: none;">
            <h3 style="color: white;"><?php echo number_format($stats['legacy'] ?? 0); ?></h3>
            <div style="font-size: 0.9rem; color: rgba(255,255,255,0.9);">Legacy Subscribers</div>
        </div>
    </div>

    <!-- Audit Records Table -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 15px 20px;">
            <h3 style="margin: 0; color: #1a1a1a; font-weight: 600;">Consent Records</h3>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Subscriber</th>
                    <th>Email</th>
                    <th>Consent Method</th>
                    <th>Consent Date</th>
                    <th>IP Address</th>
                    <th>Proof Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px;">
                            <div style="color: #999; font-size: 1.2rem;">No consent records found</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '')); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($record['email'] ?? ''); ?></td>
                            <td>
                                <?php
                                $badges = [
                                    'form' => 'success',
                                    'whatsapp' => 'info',
                                    'import' => 'warning',
                                    'manual' => 'primary',
                                    'legacy' => 'secondary'
                                ];
                                $badgeClass = $badges[$record['consent_method']] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?php echo $badgeClass; ?>">
                                    <?php echo strtoupper($record['consent_method'] ?? 'UNKNOWN'); ?>
                                </span>
                            </td>
                            <td><?php echo $record['consent_date'] ? date('M d, Y H:i', strtotime($record['consent_date'])) : '-'; ?></td>
                            <td><code><?php echo htmlspecialchars($record['consent_ip'] ?? 'N/A'); ?></code></td>
                            <td>
                                <?php if (!empty($record['proof_details'])): ?>
                                    <button class="btn btn-sm btn-outline" 
                                            onclick="showProof(<?php echo htmlspecialchars(json_encode($record['proof_details'])); ?>)">
                                        👁️ View
                                    </button>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


<!-- Proof Details Modal -->
<div class="modal fade" id="proofModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Consent Proof Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="proofContent" style="background: #f5f5f5; padding: 15px; border-radius: 5px;"></pre>
            </div>
        </div>
    </div>
</div>

    </main>
</div>

<style>
    .fintech-card { 
        border-left: 4px solid #000066; 
        padding: 20px;
        text-align: center;
    }
    .fintech-card h3 {
        margin: 0 0 5px 0;
        font-size: 2.5rem;
        font-weight: bold;
    }
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .badge-success { background: #28a745; color: white; }
    .badge-info { background: #17a2b8; color: white; }
    .badge-warning { background: #ffc107; color: #000; }
    .badge-primary { background: #007bff; color: white; }
    .badge-secondary { background: #6c757d; color: white; }
    
    table { width: 100%; border-collapse: collapse; }
    th, td { 
        padding: 12px; 
        text-align: left; 
        border-bottom: 1px solid #eee; 
        color: #1a1a1a; /* Dark black text for readability */
    }
    th { background: #f8f9fa; font-weight: 600; }
    
    /* Ensure all table text is dark */
    table td, table td strong, table td code {
        color: #1a1a1a !important;
    }
    table td code {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
    }
    
    /* Sidebar Link Styling */
    a { text-decoration: none; }
    
    /* Fix Sidebar Contrast - White text on dark sidebar */
    .sidebar .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { 
        color: #ffffff !important; 
        background: rgba(255, 255, 255, 0.1); 
    }
    .sidebar .nav-link ion-icon { color: inherit !important; }
    
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    @media (max-width: 992px) {
        /* Sidebar Blue Theme on Mobile */
        .sidebar {
            background-color: #000066 !important;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8) !important;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1) !important;
            color: white !important;
        }
        
        .grid-4 { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 15px; 
        }
        .top-bar { flex-direction: column; align-items: flex-start; gap: 10px; }
    }
    
    @media (min-width: 769px) {
        .grid-4 { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 20px; 
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showProof(proofData) {
    document.getElementById('proofContent').textContent = JSON.stringify(proofData, null, 2);
    new bootstrap.Modal(document.getElementById('proofModal')).show();
}
</script>
</body>
</html>
