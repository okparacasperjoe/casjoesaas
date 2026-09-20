<?php require __DIR__ . '/header.php'; ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    .soc-header {
        background: linear-gradient(135deg, #161828 0%, #0d0e17 100%);
        color: #ffffff;
        padding: 35px 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 166, 0, 0.25);
    }
    .soc-header::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(255, 166, 0, 0.12), transparent 45%);
        pointer-events: none;
    }
    .soc-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff !important;
    }
    .soc-header p {
        color: #94a3b8;
        margin: 0;
        font-size: 15px;
        max-width: 650px;
        line-height: 1.5;
    }
    .soc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    .soc-card {
        background: #13141f !important;
        padding: 24px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-top: 3px solid #FFA600;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .soc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(255, 166, 0, 0.15);
    }
    .soc-icon-wrapper {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin-right: 20px;
        flex-shrink: 0;
    }
    .icon-red { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    .icon-yellow { background: rgba(255, 166, 0, 0.15); color: #FFA600; border: 1px solid rgba(255, 166, 0, 0.3); }
    .icon-blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
    
    .stat-value { font-size: 34px; font-weight: 700; line-height: 1.2; color: #ffffff; }
    .stat-label { color: #94a3b8; font-size: 14px; font-weight: 500; margin-top: 4px; }
    
    .chart-box {
        background: #13141f !important;
        padding: 22px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    }
    .chart-box h3 {
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 16px 0;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel {
        background: #13141f !important;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 30px;
    }
    .panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #181a29;
    }
    .panel-title {
        font-size: 18px;
        font-weight: 600;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-refresh {
        background: rgba(255, 166, 0, 0.1);
        border: 1px solid rgba(255, 166, 0, 0.3);
        padding: 8px 16px;
        border-radius: 8px;
        color: #FFA600;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-refresh:hover {
        background: #FFA600;
        color: #000000;
    }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    table th {
        background: #191b2a !important;
        padding: 16px 24px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #FFA600 !important;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border-bottom: 1px solid rgba(255, 166, 0, 0.25) !important;
    }
    table td {
        padding: 16px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        font-size: 14px;
        color: #e2e8f0 !important;
    }
    table tr:last-child td { border-bottom: none; }
    table tr:hover td { background-color: #1d1f30 !important; }
    
    .badge-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4); }
    .badge-warning { background: rgba(255, 166, 0, 0.15); color: #FFA600; border: 1px solid rgba(255, 166, 0, 0.4); }
    .badge-info { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); }
    
    .code-snippet {
        font-family: 'JetBrains Mono', 'Consolas', monospace;
        font-size: 12px;
        background: rgba(255, 255, 255, 0.04);
        padding: 5px 10px;
        border-radius: 6px;
        color: #FFA600;
        border: 1px solid rgba(255, 166, 0, 0.2);
    }
    .user-link {
        color: #60a5fa;
        text-decoration: none;
        font-weight: 500;
    }
    .user-link:hover { color: #FFA600; text-decoration: underline; }
    
    .empty-state {
        padding: 50px;
        text-align: center;
        color: #94a3b8;
    }
    .live-indicator {
        width: 10px;
        height: 10px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
        70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .btn-block {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 6px 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-block:hover {
        background: #ef4444;
        color: white;
    }
    .row-warning td {
        background-color: rgba(255, 166, 0, 0.06) !important;
    }
    .map-container {
        background: #13141f !important;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        color: white;
    }
    #userMap {
        height: 420px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .custom-div-icon { background: transparent; border: none; }
</style>

<div class="top-bar" style="margin-bottom: 25px;">
    <h2 style="display: flex; align-items: center; gap: 10px; margin: 0;">
        <ion-icon name="shield-checkmark" style="color: #FFA600; font-size: 1.6rem;"></ion-icon>
        Security Operations Center (SOC)
    </h2>
    <span style="display: flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.12); color: #10b981; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.3);">
        <span class="live-indicator"></span> Real-time Protection Active
    </span>
</div>

<div class="soc-header">
    <h1><ion-icon name="pulse" style="color: #FFA600;"></ion-icon> Infrastructure & Threat Monitoring</h1>
    <p>Live telemetry, real-time intrusion detection, audit log tracking, and active firewall defense across all tenant ecosystems and API gateways.</p>
</div>

<div class="soc-grid">
    <div class="soc-card">
        <div class="soc-icon-wrapper icon-red">
            <ion-icon name="alert-circle"></ion-icon>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($stats['failed_logins_today'] ?? 0); ?></div>
            <div class="stat-label">Failed Logins (24h)</div>
        </div>
    </div>
    
    <div class="soc-card">
        <div class="soc-icon-wrapper icon-yellow">
            <ion-icon name="warning"></ion-icon>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($stats['critical_events'] ?? 0); ?></div>
            <div class="stat-label">Critical Events</div>
        </div>
    </div>

    <div class="soc-card">
        <div class="soc-icon-wrapper icon-blue">
            <ion-icon name="list"></ion-icon>
        </div>
        <div>
            <div class="stat-value"><?php echo number_format($stats['total_logs'] ?? 0); ?></div>
            <div class="stat-label">Total Security Logs</div>
        </div>
    </div>
</div>

<!-- Analytics Charts Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="chart-box">
        <h3><ion-icon name="trending-up-outline" style="color:#FFA600;"></ion-icon> Events Timeline (7 Days)</h3>
        <canvas id="eventsTimelineChart" style="max-height: 200px;"></canvas>
    </div>
    <div class="chart-box">
        <h3><ion-icon name="pie-chart-outline" style="color:#3b82f6;"></ion-icon> Event Types</h3>
        <canvas id="eventTypesChart" style="max-height: 200px;"></canvas>
    </div>
    <div class="chart-box">
        <h3><ion-icon name="shield-outline" style="color:#ef4444;"></ion-icon> Severity Distribution</h3>
        <canvas id="severityChart" style="max-height: 200px;"></canvas>
    </div>
    <div class="chart-box">
        <h3><ion-icon name="bar-chart-outline" style="color:#10b981;"></ion-icon> Threat Activity Bar</h3>
        <canvas id="threatActivityChart" style="max-height: 200px;"></canvas>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #10b981; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
        <span><ion-icon name="checkmark-circle" style="font-size: 1.2rem; vertical-align: middle; margin-right: 8px;"></ion-icon> 
        <?php 
            if ($_GET['msg'] === 'blocked') echo "IP Address successfully blocked and added to firewall deny list.";
            elseif ($_GET['msg'] === 'unblocked') echo "IP Address successfully removed from firewall deny list.";
            else echo htmlspecialchars($_GET['msg']);
        ?>
        </span>
        <a href="/<?php echo ADMIN_PATH; ?>/soc" style="color: #10b981; text-decoration: none; font-weight: bold;">✕</a>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <span class="live-indicator"></span>
            Live Security Feed & Audit Stream
        </div>
        <button onclick="location.reload()" class="btn-refresh">
            <ion-icon name="refresh"></ion-icon> Refresh Feed
        </button>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Block</th>
                    <th>Timestamp</th>
                    <th>Severity</th>
                    <th>Event Type</th>
                    <th>Message</th>
                    <th>User Context</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <ion-icon name="checkmark-circle-outline" style="font-size: 48px; color: #10b981; margin-bottom: 10px;"></ion-icon>
                            <p style="margin: 0;">No security events logged. All systems operating cleanly.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr class="<?php echo ($log['event_type'] === 'auth.login_failed') ? 'row-warning' : ''; ?>">
                        <td style="width: 50px; text-align: center;">
                            <?php if ($log['ip_address'] && $log['ip_address'] !== '::1' && $log['ip_address'] !== '127.0.0.1'): ?>
                                <form action="/<?php echo ADMIN_PATH; ?>/soc/block" method="POST" onsubmit="return confirm('Immediately block IP <?= htmlspecialchars($log['ip_address']) ?> across the platform?');">
                                    <input type="hidden" name="ip" value="<?php echo htmlspecialchars($log['ip_address']); ?>">
                                    <button type="submit" class="btn-block" title="Block IP">
                                        <ion-icon name="ban" style="font-size: 1rem;"></ion-icon>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #475569;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="white-space: nowrap; color: #94a3b8; font-size: 0.85rem;">
                            <?php echo date('M j, H:i:s', strtotime($log['created_at'])); ?>
                        </td>
                        <td>
                            <span class="badge-pill badge-<?php echo $log['severity']; ?>">
                                <?php echo strtoupper($log['severity']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="code-snippet"><?php echo htmlspecialchars($log['event_type']); ?></span>
                        </td>
                        <td style="color: #ffffff;"><?php echo htmlspecialchars($log['message'] ?? ''); ?></td>
                        <td>
                            <?php if (!empty($log['user_email'])): ?>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <ion-icon name="person-circle-outline" style="color: #64748b; font-size: 1.1rem;"></ion-icon>
                                    <?php if (!empty($log['user_id'])): ?>
                                        <a href="/<?php echo ADMIN_PATH; ?>/users/edit/<?php echo $log['user_id']; ?>" class="user-link">
                                            <?php echo htmlspecialchars($log['user_email']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #e2e8f0;"><?php echo htmlspecialchars($log['user_email']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #64748b; font-style: italic;">System / Anonymous</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; color: #FFA600;">
                            <?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Blocked IPs Section -->
<?php if (!empty($blockedIps)): ?>
<div class="panel" style="border: 1px solid rgba(239, 68, 68, 0.4);">
    <div class="panel-header" style="background: rgba(239, 68, 68, 0.12);">
        <div class="panel-title" style="color: #ef4444;">
            <ion-icon name="ban"></ion-icon> Active Firewall Deny List (Blocked IP Addresses)
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Reason / Trigger</th>
                    <th>Blocked Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blockedIps as $bIp): ?>
                <tr>
                    <td style="font-family: 'JetBrains Mono', monospace; font-weight: bold; color: #ef4444;"><?php echo htmlspecialchars($bIp['ip_address']); ?></td>
                    <td style="color: #ffffff;"><?php echo htmlspecialchars($bIp['reason'] ?? 'Manual SOC Block'); ?></td>
                    <td style="color: #94a3b8;"><?php echo date('M j, Y - H:i:s', strtotime($bIp['created_at'])); ?></td>
                    <td>
                        <form action="/<?php echo ADMIN_PATH; ?>/soc/unblock" method="POST" onsubmit="return confirm('Unblock IP <?= htmlspecialchars($bIp['ip_address']) ?>?');">
                            <input type="hidden" name="id" value="<?php echo $bIp['id']; ?>">
                            <button type="submit" style="color: #10b981; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s;">
                                Unblock IP
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Live Map Section -->
<div class="map-container">
    <h2 style="margin: 0 0 15px 0; font-size: 18px; display: flex; align-items: center; gap: 10px; color: #ffffff;">
        <ion-icon name="globe-outline" style="color: #60a5fa; font-size: 1.4rem;"></ion-icon> 
        Global Threat & Telemetry Geolocation Map
    </h2>
    <div id="userMap"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
Chart.defaults.color = '#94a3b8';
Chart.defaults.font.family = "'Inter', sans-serif";

document.addEventListener('DOMContentLoaded', function() {
    const eventsTimeline = <?php echo json_encode($eventsTimeline ?? []); ?>;
    const eventTypes = <?php echo json_encode($eventTypes ?? []); ?>;
    const severityDist = <?php echo json_encode($severityDist ?? []); ?>;
    const logs = <?php echo json_encode($logs ?? []); ?>;

    // 1. Events Timeline Chart
    const timelineCtx = document.getElementById('eventsTimelineChart');
    if (timelineCtx && eventsTimeline.length > 0) {
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: eventsTimeline.map(d => d.date),
                datasets: [{
                    label: 'Security Events',
                    data: eventsTimeline.map(d => d.count),
                    borderColor: '#FFA600',
                    backgroundColor: 'rgba(255, 166, 0, 0.12)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                    x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                }
            }
        });
    }

    // 2. Event Types Chart
    const typesCtx = document.getElementById('eventTypesChart');
    if (typesCtx && eventTypes.length > 0) {
        new Chart(typesCtx, {
            type: 'doughnut',
            data: {
                labels: eventTypes.map(e => e.event_type),
                datasets: [{
                    data: eventTypes.map(e => e.count),
                    backgroundColor: ['#FFA600', '#3b82f6', '#ef4444', '#10b981', '#a855f7', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12, color: '#e2e8f0' } } }
            }
        });
    }

    // 3. Severity Distribution Chart
    const severityCtx = document.getElementById('severityChart');
    if (severityCtx && severityDist.length > 0) {
        const sevColors = { 'info': '#3b82f6', 'warning': '#FFA600', 'danger': '#ef4444' };
        new Chart(severityCtx, {
            type: 'pie',
            data: {
                labels: severityDist.map(s => s.severity.toUpperCase()),
                datasets: [{
                    data: severityDist.map(s => s.count),
                    backgroundColor: severityDist.map(s => sevColors[s.severity] || '#64748b'),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12, color: '#e2e8f0' } } }
            }
        });
    }

    // 4. Threat Activity Bar Chart
    const threatCtx = document.getElementById('threatActivityChart');
    if (threatCtx && eventsTimeline.length > 0) {
        new Chart(threatCtx, {
            type: 'bar',
            data: {
                labels: eventsTimeline.map(d => d.date),
                datasets: [{
                    label: 'Daily Activity',
                    data: eventsTimeline.map(d => d.count),
                    backgroundColor: '#10b981',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                    x: { ticks: { color: '#94a3b8' }, grid: { display: false } }
                }
            }
        });
    }

    // 5. Leaflet Geolocation Map (Dark Theme)
    const mapEl = document.getElementById('userMap');
    if (mapEl && typeof L !== 'undefined') {
        const map = L.map('userMap').setView([20, 0], 2);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const markers = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true
        });

        const processedIps = new Set();

        logs.forEach(function(log) {
            const ip = log.ip_address;
            if (!ip || processedIps.has(ip)) return;
            processedIps.add(ip);

            if (ip === '127.0.0.1' || ip === '::1') {
                addMarker(51.505, -0.09, "Localhost Node / Core System", log.severity || 'info', log.message);
                return;
            }

            fetch(`https://ipwho.is/${ip}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        addMarker(data.latitude, data.longitude, 
                            `${ip} (${data.city}, ${data.country})`, 
                            log.severity || 'info', 
                            log.message || '');
                    }
                })
                .catch(err => console.log('Geo error', err));
        });

        function addMarker(lat, lon, title, severity, message) {
            const colors = { danger: '#ef4444', warning: '#FFA600', info: '#3b82f6' };
            const color = colors[severity] || '#3b82f6';

            const icon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:${color}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 15px ${color}, 0 0 5px ${color};"></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            const marker = L.marker([lat, lon], {icon: icon});
            marker.bindPopup(`<div style="font-family: Inter, sans-serif; background: #13141f; color: #fff; padding: 5px; border-radius: 6px;"><b>${title}</b><br><span style="color:${color}; font-weight: bold;">SEVERITY: ${severity.toUpperCase()}</span><br><small style="color: #cbd5e1;">${message || ''}</small></div>`);
            markers.addLayer(marker);
        }

        map.addLayer(markers);
    }
});
</script>
</main>
</div>
</body>
</html>
