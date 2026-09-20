<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Meeting — <?= htmlspecialchars($profile['title']) ?></title>
    <link rel="icon" type="image/png" href="/assets/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-page: #0a0a1a;
            --text-main: #ffffff;
            --text-muted: rgba(255,255,255,0.6);
            --card-bg: rgba(255,255,255,0.04);
            --card-border: rgba(255,255,255,0.08);
            --slot-bg: rgba(255,255,255,0.06);
            --slot-border: rgba(255,255,255,0.12);
            --input-bg: rgba(255,255,255,0.06);
            --input-border: rgba(255,255,255,0.12);
            --nav-btn-bg: rgba(255,255,255,0.1);
        }
        html.light-theme {
            --bg-page: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --slot-bg: #f8fafc;
            --slot-border: #e2e8f0;
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --nav-btn-bg: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-page); color: var(--text-main); min-height: 100vh; transition: background 0.3s ease, color 0.3s ease; }

        .booking-container { max-width: 960px; margin: 0 auto; padding: 40px 20px; }

        .booking-header { text-align: center; margin-bottom: 40px; }
        .booking-header h1 { font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, #FFA600, #ff6b6b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .booking-header .host-name { color: var(--text-muted); font-size: 1rem; margin-top: 5px; }
        .booking-header .duration-badge { display: inline-block; background: rgba(255,166,0,0.15); color: #FFA600; padding: 4px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-top: 10px; }
        .booking-header .description { color: var(--text-muted); margin-top: 12px; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6; }

        .booking-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

        .calendar-section, .slots-section { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }

        /* Calendar */
        .calendar-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .calendar-nav h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }
        .calendar-nav button { background: var(--nav-btn-bg); border: none; color: var(--text-main); width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; }
        .calendar-nav button:hover { background: rgba(255,166,0,0.2); }

        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
        .cal-day-name { font-size: 0.75rem; color: var(--text-muted); padding: 8px 0; font-weight: 600; }
        .cal-day { padding: 10px 0; border-radius: 10px; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; border: 2px solid transparent; color: var(--text-main); }
        .cal-day:hover:not(.disabled):not(.empty) { background: rgba(255,166,0,0.15); border-color: rgba(255,166,0,0.4); }
        .cal-day.selected { background: #FFA600 !important; color: #000 !important; font-weight: 700; border-color: #FFA600 !important; }
        .cal-day.disabled { opacity: 0.25; cursor: not-allowed; }
        .cal-day.today { border-color: rgba(255,166,0,0.6); font-weight: 700; }
        .cal-day.empty { cursor: default; }

        /* Slots */
        .slots-section h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: 15px; color: var(--text-main); }
        .slots-list { display: flex; flex-direction: column; gap: 8px; max-height: 380px; overflow-y: auto; padding-right: 4px; }
        .slot-btn { background: var(--slot-bg); border: 1px solid var(--slot-border); color: var(--text-main); padding: 12px 18px; border-radius: 10px; cursor: pointer; font-size: 0.95rem; text-align: center; transition: all 0.2s; font-family: inherit; font-weight: 500; }
        .slot-btn:hover { background: rgba(255,166,0,0.12); border-color: #FFA600; transform: translateY(-1px); }
        .slot-btn.selected { background: #FFA600 !important; color: #000 !important; font-weight: 700; border-color: #FFA600 !important; }
        .slots-loading { text-align: center; padding: 40px; color: var(--text-muted); }
        .slots-empty { text-align: center; padding: 40px; color: var(--text-muted); }

        /* Guest Form */
        .guest-form { display: none; margin-top: 30px; background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .guest-form.visible { display: block; animation: slideUp 0.3s ease; }
        .guest-form h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: var(--text-main); }
        .form-row { margin-bottom: 16px; }
        .form-row label { display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 6px; }
        .form-row input, .form-row textarea { width: 100%; padding: 12px 16px; background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 10px; color: var(--text-main); font-size: 0.95rem; font-family: inherit; transition: border 0.2s; }
        .form-row input:focus, .form-row textarea:focus { outline: none; border-color: #FFA600; box-shadow: 0 0 0 3px rgba(255,166,0,0.15); }
        .form-row textarea { resize: vertical; min-height: 80px; }
        .submit-btn { display: block; width: 100%; padding: 14px; background: linear-gradient(135deg, #FFA600, #ff8c00); color: #000; border: none; border-radius: 12px; font-size: 1.05rem; font-weight: 700; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; font-family: inherit; }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,166,0,0.3); }
        .submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

        .selected-summary { background: rgba(255,166,0,0.08); border: 1px solid rgba(255,166,0,0.25); border-radius: 10px; padding: 15px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .selected-summary .date { font-weight: 700; color: #FFA600; font-size: 0.95rem; }
        .selected-summary .time { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }

        .error-banner { background: rgba(231,74,59,0.15); border: 1px solid rgba(231,74,59,0.3); color: #e74a3b; padding: 12px 20px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 500; }

        .powered-by { text-align: center; margin-top: 40px; color: var(--text-muted); font-size: 0.85rem; opacity: 0.7; }
        .powered-by a { color: #FFA600; text-decoration: none; font-weight: 600; }
        .powered-by a:hover { text-decoration: underline; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .booking-grid { grid-template-columns: 1fr; }
            .booking-header h1 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-header">
            <h1><?= htmlspecialchars($profile['title']) ?></h1>
            <div class="host-name">with <?= htmlspecialchars($profile['host_name']) ?></div>
            <div class="duration-badge">⏱ <?= $profile['duration'] ?> min</div>
            <?php if (!empty($profile['description'])): ?>
                <div class="description"><?= nl2br(htmlspecialchars($profile['description'])) ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-banner">
                <?php if ($_GET['error'] === 'slot_taken'): ?>
                    ⚠️ Oops! That time slot was just booked. Please pick another.
                <?php elseif ($_GET['error'] === 'missing_fields'): ?>
                    ⚠️ Please fill in all required fields.
                <?php else: ?>
                    ⚠️ Something went wrong. Please try again.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="booking-grid">
            <!-- Calendar -->
            <div class="calendar-section">
                <div class="calendar-nav">
                    <button onclick="prevMonth()">‹</button>
                    <h3 id="calMonthYear"></h3>
                    <button onclick="nextMonth()">›</button>
                </div>
                <div class="calendar-grid" id="calendarGrid"></div>
            </div>

            <!-- Time Slots -->
            <div class="slots-section">
                <h3 id="slotsTitle">Select a date first</h3>
                <div class="slots-list" id="slotsList">
                    <div class="slots-empty">
                        <p>👈 Pick a date on the calendar to see available times.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Form -->
        <form class="guest-form" id="guestForm" method="POST" action="/book/<?= htmlspecialchars($profile['slug']) ?>/submit">
            <h3>📝 Your Details</h3>
            <div class="selected-summary" id="selectedSummary">
                <span class="date" id="summaryDate"></span>
                <span class="time" id="summaryTime"></span>
            </div>
            <input type="hidden" name="booking_date" id="bookingDate">
            <input type="hidden" name="start_time" id="startTime">

            <div class="form-row">
                <label>Full Name *</label>
                <input type="text" name="guest_name" required placeholder="John Doe">
            </div>
            <div class="form-row">
                <label>Email Address *</label>
                <input type="email" name="guest_email" required placeholder="john@example.com">
            </div>
            <div class="form-row">
                <label>Phone Number</label>
                <input type="tel" name="guest_phone" placeholder="+234...">
            </div>
            <div class="form-row">
                <label>Notes (optional)</label>
                <textarea name="guest_notes" placeholder="Anything you'd like to share before the meeting..."></textarea>
            </div>
            <button type="submit" class="submit-btn" id="submitBtn">
                Confirm Booking →
            </button>
        </form>

        <div class="powered-by">
            Powered by <a href="/">Casjoe LLC</a>
        </div>
    </div>

<script>
    const slug = '<?= htmlspecialchars($profile['slug']) ?>';
    const availability = <?= $profile['availability'] ?>;
    const enabledDays = {};

    // Map day names to JS day numbers (0=Sun, 1=Mon, ...)
    const dayMap = { sunday: 0, monday: 1, tuesday: 2, wednesday: 3, thursday: 4, friday: 5, saturday: 6 };
    for (let [day, config] of Object.entries(availability)) {
        if (config.enabled) enabledDays[dayMap[day]] = true;
    }

    let currentYear, currentMonth, selectedDate = null, selectedSlot = null;

    function init() {
        const today = new Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();
        renderCalendar();
    }

    function renderCalendar() {
        const grid = document.getElementById('calendarGrid');
        const header = document.getElementById('calMonthYear');
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        header.textContent = months[currentMonth] + ' ' + currentYear;

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();
        today.setHours(0,0,0,0);

        let html = '';
        ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
            html += `<div class="cal-day-name">${d}</div>`;
        });

        // Empty cells
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="cal-day empty"></div>';
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const date = new Date(currentYear, currentMonth, d);
            const dayNum = date.getDay();
            const isPast = date < today;
            const isToday = date.getTime() === today.getTime();
            const isAvailable = enabledDays[dayNum] && !isPast;
            const dateStr = formatDate(date);

            let classes = 'cal-day';
            if (!isAvailable) classes += ' disabled';
            if (isToday) classes += ' today';
            if (selectedDate === dateStr) classes += ' selected';

            if (isAvailable) {
                html += `<div class="${classes}" onclick="selectDate('${dateStr}', this)">${d}</div>`;
            } else {
                html += `<div class="${classes}">${d}</div>`;
            }
        }

        grid.innerHTML = html;
    }

    function prevMonth() {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar();
    }

    function nextMonth() {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        renderCalendar();
    }

    function formatDate(date) {
        return date.getFullYear() + '-' +
               String(date.getMonth()+1).padStart(2,'0') + '-' +
               String(date.getDate()).padStart(2,'0');
    }

    function selectDate(dateStr, el) {
        selectedDate = dateStr;
        selectedSlot = null;
        document.getElementById('guestForm').classList.remove('visible');

        // Re-render calendar to update selection
        renderCalendar();

        // Show loading
        document.getElementById('slotsTitle').textContent = 'Loading slots...';
        document.getElementById('slotsList').innerHTML = '<div class="slots-loading">Loading available times...</div>';

        // Parse date for title display
        const [year, month, day] = dateStr.split('-').map(Number);
        const dateObj = new Date(year, month - 1, day);
        const options = { weekday: 'long', month: 'long', day: 'numeric' };
        document.getElementById('slotsTitle').textContent = dateObj.toLocaleDateString('en-US', options);

        // Fetch slots from /book/{slug}/slots
        fetch(`/book/${encodeURIComponent(slug)}/slots?date=${encodeURIComponent(dateStr)}`)
            .then(r => {
                if (!r.ok) {
                    throw new Error('Server returned ' + r.status);
                }
                return r.json();
            })
            .then(data => {
                const slots = data.slots || [];

                if (slots.length === 0) {
                    document.getElementById('slotsList').innerHTML = '<div class="slots-empty"><p>No available slots for this date.</p></div>';
                    return;
                }

                let html = '';
                slots.forEach(s => {
                    const startDisplay = formatTime12(s.start);
                    const endDisplay = formatTime12(s.end);
                    html += `<button type="button" class="slot-btn" onclick="selectSlot('${s.start}', '${s.end}', this)">${startDisplay} — ${endDisplay}</button>`;
                });
                document.getElementById('slotsList').innerHTML = html;
            })
            .catch(err => {
                console.error('Failed to load slots:', err);
                document.getElementById('slotsList').innerHTML = '<div class="slots-empty"><p>Failed to load slots. Please try again.</p></div>';
            });
    }

    function selectSlot(start, end, el) {
        selectedSlot = start;
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');

        document.getElementById('bookingDate').value = selectedDate;
        document.getElementById('startTime').value = start;

        const [year, month, day] = selectedDate.split('-').map(Number);
        const dateObj = new Date(year, month - 1, day);
        const options = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
        document.getElementById('summaryDate').textContent = dateObj.toLocaleDateString('en-US', options);
        document.getElementById('summaryTime').textContent = formatTime12(start) + ' — ' + formatTime12(end);

        document.getElementById('guestForm').classList.add('visible');
        setTimeout(() => {
            document.getElementById('guestForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    function formatTime12(time24) {
        const [h, m] = time24.split(':').map(Number);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const h12 = h % 12 || 12;
        return h12 + ':' + String(m).padStart(2, '0') + ' ' + ampm;
    }

    // Prevent double submit
    document.getElementById('guestForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = 'Booking...';
    });

    init();
</script>
</body>
</html>
