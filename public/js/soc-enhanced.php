// Chart.js Configuration
Chart.defaults.color = '#6b7280';
Chart.defaults.font.family = "'Inter', sans-serif";

document.addEventListener('DOMContentLoaded', function() {
    // PHP Data
    const eventsTimeline = <?php echo json_encode($eventsTimeline ?? []); ?>;
    const eventTypes = <?php echo json_encode($eventTypes ?? []); ?>;
    const severityDist = <?php echo json_encode($severityDist ?? []); ?>;
    const logs = <?php echo json_encode($logs ?? []); ?>;

    // 1. Events Timeline Chart (Area)
    const timelineCtx = document.getElementById('eventsTimelineChart');
    if (timelineCtx) {
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: eventsTimeline.map(d => d.date),
                datasets: [{
                    label: 'Security Events',
                    data: eventsTimeline.map(d => d.count),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {y: {beginAtZero: true, ticks: {precision: 0}}}
            }
        });
    }

    // 2. Event Types Chart (Doughnut)
    const typesCtx = document.getElementById('eventTypesChart');
    if (typesCtx) {
        new Chart(typesCtx, {
            type: 'doughnut',
            data: {
                labels: eventTypes.map(e => e.event_type),
                datasets: [{
                    data: eventTypes.map(e => e.count),
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {position: 'bottom', labels: {padding: 10, boxWidth: 12}}
                }
            }
        });
    }

    // 3. Severity Distribution (Pie)
    const severityCtx = document.getElementById('severityChart');
    if (severityCtx) {
        const sevColors = {'info': '#3b82f6', 'warning': '#f59e0b', 'danger': '#ef4444'};
        new Chart(severityCtx, {
            type: 'pie',
            data: {
                labels: severityDist.map(s => s.severity.toUpperCase()),
                datasets: [{
                    data: severityDist.map(s => s.count),
                    backgroundColor: severityDist.map(s => sevColors[s.severity] || '#6b7280')
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {position: 'bottom', labels: {padding: 10, boxWidth: 12}}
                }
            }
        });
    }

    // 4. Threat Activity Chart (Bar - Last 7 Days)
    const threatCtx = document.getElementById('threatActivityChart');
    if (threatCtx) {
        new Chart(threatCtx, {
            type: 'bar',
            data: {
                labels: eventsTimeline.map(d => d.date),
                datasets: [{
                    label: 'Events',
                    data: eventsTimeline.map(d => d.count),
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {legend: {display: false}},
                scales: {y: {beginAtZero: true, ticks: {precision: 0}}}
            }
        });
    }

    // ENHANCED MAP with Clustering
    const mapEl = document.getElementById('userMap');
    if (mapEl) {
        const map = L.map('userMap').setView([20, 0], 2);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // Marker Cluster Group
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
                addMarker(51.505, -0.09, "Localhost System", log.severity || 'info', log.message);
                return;
            }

            // Fetch geo location
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
            const colors = {danger: '#ef4444', warning: '#f59e0b', info: '#3b82f6'};
            const color = colors[severity] || '#3b82f6';

            const icon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:${color}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 15px ${color}, 0 0 5px ${color};"></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            const marker = L.marker([lat, lon], {icon: icon});
            marker.bindPopup(`<div style="font-family: Inter, sans-serif;"><b>${title}</b><br><span style="color:${color};">Severity: ${severity.toUpperCase()}</span><br>${message || ''}</div>`);
            markers.addLayer(marker);
        }

        map.addLayer(markers);
    }
});
