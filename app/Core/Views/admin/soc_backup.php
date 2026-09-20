<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <title>Security Operations Center (SOC) - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --success: #10b981;
            --success-light: #ecfdf5;
            --surface: #ffffff;
            --background: #f3f4f6;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            margin: 0; 
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .soc-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .soc-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.3), transparent 40%);
            pointer-events: none;
        }

        .soc-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .soc-header p {
            color: #94a3b8;
            margin: 0;
            font-size: 16px;
            max-width: 600px;
        }

        .soc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .soc-card {
            background: var(--surface);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .soc-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .soc-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .icon-red { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: var(--danger); }
        .icon-yellow { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: var(--warning); }
        .icon-blue { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); color: var(--primary); }

        .stat-value { font-size: 32px; font-weight: 700; line-height: 1.2; color: var(--text-main); }
        .stat-label { color: var(--text-muted); font-size: 14px; font-weight: 500; }

        .panel {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .panel-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-refresh {
            background: white;
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: 8px;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-refresh:hover {
            background: var(--background);
            color: var(--text-main);
        }

        .table-responsive { overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; }
        
        th {
            background: #f9fafb;
            padding: 16px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-main);
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9fafb; }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
        }

        .badge-danger { background: var(--danger-light); color: var(--danger); }
        .badge-warning { background: var(--warning-light); color: var(--warning); }
        .badge-info { background: var(--primary-light); color: var(--primary); }

        .code-snippet {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 12px;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .user-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .user-link:hover { text-decoration: underline; }

        .empty-state {
            padding: 40px;
            text-align: center;
            color: var(--text-muted);
        }

        /* Pulse animation for live feel */
        .live-indicator {
            width: 8px;
            height: 8px;
            background-color: var(--success);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .btn-block {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 6px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-block:hover {
            background: #ef4444;
            color: white;
        }
        .row-warning {
            background-color: #fffbeb !important;
        }
        .row-warning td {
            border-bottom-color: #fef3c7;
        }
        .map-container {
            background: #1e293b; /* Dark slate background for map area */
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            color: white;
        }
        #userMap {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }
        .user-popup .leaflet-popup-content-wrapper {
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
        }
        .user-popup .leaflet-popup-tip { background: white; }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <main class="main-content">
            <div class="soc-header">
                <h1><ion-icon name="shield-checkmark"></ion-icon> Security Operations Center</h1>
                <p>Real-time monitoring of system security, user authentication, and critical events across your infrastructure.</p>
            </div>

            <div class="content-wrapper">
                
                <!-- ... stats cards ... (unchanged) -->
                 <div class="soc-grid">
                    <div class="soc-card">
                        <div class="soc-icon-wrapper icon-red">
                            <ion-icon name="alert-circle"></ion-icon>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo number_format($stats['failed_logins_today']); ?></div>
                            <div class="stat-label">Failed Logins (24h)</div>
                        </div>
                    </div>
                    
                    <div class="soc-card">
                        <div class="soc-icon-wrapper icon-yellow">
                            <ion-icon name="warning"></ion-icon>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo number_format($stats['critical_events']); ?></div>
                            <div class="stat-label">Critical Events</div>
                        </div>
                    </div>

                    <div class="soc-card">
                        <div class="soc-icon-wrapper icon-blue">
                            <ion-icon name="list"></ion-icon>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo number_format($stats['total_logs']); ?></div>
                            <div class="stat-label">Total Audit Logs</div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <!-- ... table header ... -->
                     <div class="panel-header">
                        <div class="panel-title">
                            <span class="live-indicator"></span>
                            Live Security Feed
                        </div>
                        <button onclick="location.reload()" class="btn-refresh">
                            <ion-icon name="refresh"></ion-icon> Refresh Data
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Action</th>
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
                                            <p>No security events found. System is secure.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                    <tr class="<?php echo ($log['event_type'] === 'auth.login_failed') ? 'row-warning' : ''; ?>">
                                        <td>
                                            <?php if ($log['ip_address'] && $log['ip_address'] !== '::1' && $log['ip_address'] !== '127.0.0.1'): ?>
                                                <form action="/<?php echo ADMIN_PATH; ?>/soc/block" method="POST" onsubmit="return confirm('Block this IP?');">
                                                    <input type="hidden" name="ip" value="<?php echo $log['ip_address']; ?>">
                                                    <button type="submit" class="btn-block" title="Block IP">
                                                        <ion-icon name="ban"></ion-icon>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap; color: var(--text-muted);">
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
                                        <td><?php echo htmlspecialchars($log['message'] ?? ''); ?></td>
                                        <td>
                                            <?php if ($log['user_email']): ?>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <ion-icon name="person-circle-outline" style="color: #64748b;"></ion-icon>
                                                    <a href="/<?php echo ADMIN_PATH; ?>/users/edit/<?php echo $log['user_id']; ?>" class="user-link">
                                                        <?php echo htmlspecialchars($log['user_email']); ?>
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #94a3b8; font-style: italic;">System / Guest</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-family: monospace; color: var(--text-muted);">
                                            <?php echo htmlspecialchars($log['ip_address'] ?? ''); ?>
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
                <div class="panel" style="border-color: var(--danger-light);">
                    <div class="panel-header" style="background: var(--danger-light);">
                        <div class="panel-title" style="color: var(--danger);">
                            <ion-icon name="ban"></ion-icon> Blocked IP Addresses
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Reason</th>
                                    <th>Blocked On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blockedIps as $bIp): ?>
                                <tr>
                                    <td style="font-family: monospace; font-weight: bold;"><?php echo htmlspecialchars($bIp['ip_address']); ?></td>
                                    <td><?php echo htmlspecialchars($bIp['reason']); ?></td>
                                    <td><?php echo date('M j, H:i', strtotime($bIp['created_at'])); ?></td>
                                    <td>
                                        <form action="/<?php echo ADMIN_PATH; ?>/soc/unblock" method="POST" onsubmit="return confirm('Unblock this IP?');">
                                            <input type="hidden" name="id" value="<?php echo $bIp['id']; ?>">
                                            <button type="submit" style="color: #10b981; background: none; border: none; cursor: pointer; font-weight: bold;">
                                                Unblock
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
                    <h2 style="margin: 0 0 15px 0; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                        <ion-icon name="globe-outline" style="color: #60a5fa;"></ion-icon> 
                        Live Threat Map
                    </h2>
                    <div id="userMap"></div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Map
            var map = L.map('userMap').setView([20, 0], 2); // World view

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            // Log Data from PHP
            var logs = <?php echo json_encode($logs); ?>;
            var processedIps = new Set();

            // Mock Data for Demo (if local)
            // In production, this would use the real IP API
            
            logs.forEach(function(log) {
                var ip = log.ip_address;
                if (!ip || processedIps.has(ip)) return;
                
                processedIps.add(ip);

                // Fetch Geo Data (Client-side for simplicity)
                // Using ip-api.com (free non-commercial)
                // Note: This won't work for localhost/private IPs.
                
                if (ip === '127.0.0.1' || ip === '::1') {
                     // Add a mock marker for localhost (e.g., in London or NY for demo)
                     addMarker(51.505, -0.09, "Localhost System", log.severity);
                     return;
                }

                // Switch to ipwho.is for HTTPS support (Free tier)
                fetch(`https://ipwho.is/${ip}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            addMarker(data.latitude, data.longitude, `${ip} (${data.city}, ${data.country})`, log.severity);
                        }
                    })
                    .catch(err => console.log('Geo fetch error', err));
            });

            function addMarker(lat, lon, title, severity) {
                var color = severity === 'danger' ? '#ef4444' : (severity === 'warning' ? '#f59e0b' : '#3b82f6');
                
                // Custom dot marker
                var icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color:${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 10px ${color};"></div>`,
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                });

                L.marker([lat, lon], {icon: icon}).addTo(map)
                    .bindPopup(`<b>${title}</b><br>Severity: ${severity.toUpperCase()}`, {className: 'user-popup'});
            }
        });
    </script>
</body>
</html>
