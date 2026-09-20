<?php

namespace App\Modules\CasjoeERP\Controllers;

use App\Core\Database;
use App\Core\TenantContext;
use App\Core\Mailer;
use App\Modules\CasjoeERP\Helpers\GoogleCalendarService;
use PDO;

class SchedulerController
{
    private $pdo;
    private $tenantId;
    private $userId;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->tenantId = TenantContext::getTenantId();
        $user = \App\Core\Auth::user();
        $this->userId = $user['id'] ?? 0;
    }

    /**
     * Scheduler Dashboard — upcoming bookings
     */
    public function index()
    {
        $profile = $this->getProfile();
        $filter = $_GET['filter'] ?? 'all';

        // 1. Get upcoming bookings for Overview (limit 5)
        $bookings = [];
        if ($profile) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM erp_scheduler_bookings
                 WHERE profile_id = ? AND booking_date >= CURDATE() AND status = 'confirmed'
                 ORDER BY booking_date ASC, start_time ASC LIMIT 5"
            );
            $stmt->execute([$profile['id']]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 2. Get filtered bookings for Appointments Log
        $allBookings = [];
        if ($profile) {
            $sql = "SELECT * FROM erp_scheduler_bookings WHERE profile_id = ?";
            $params = [$profile['id']];

            if ($filter === 'upcoming') {
                $sql .= " AND booking_date >= CURDATE() AND status = 'confirmed'";
            } elseif ($filter === 'cancelled') {
                $sql .= " AND status = 'cancelled'";
            }

            $sql .= " ORDER BY booking_date DESC, start_time DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $allBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 3. Stats
        $stats = ['upcoming' => 0, 'today' => 0, 'total' => 0];
        if ($profile) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM erp_scheduler_bookings WHERE profile_id = ? AND booking_date >= CURDATE() AND status = 'confirmed'");
            $stmt->execute([$profile['id']]);
            $stats['upcoming'] = $stmt->fetchColumn();

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM erp_scheduler_bookings WHERE profile_id = ? AND booking_date = CURDATE() AND status = 'confirmed'");
            $stmt->execute([$profile['id']]);
            $stats['today'] = $stmt->fetchColumn();

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM erp_scheduler_bookings WHERE profile_id = ?");
            $stmt->execute([$profile['id']]);
            $stats['total'] = $stmt->fetchColumn();
        }

        // 4. Default availability & settings
        $defaultAvailability = [
            'monday'    => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'],
            'tuesday'   => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'],
            'wednesday' => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'],
            'thursday'  => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'],
            'friday'    => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'],
            'saturday'  => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
            'sunday'    => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'],
        ];

        $availability = $defaultAvailability;
        if ($profile && !empty($profile['availability'])) {
            $availability = json_decode($profile['availability'], true) ?: $defaultAvailability;
        }

        $googleService = new GoogleCalendarService();
        $googleConfigured = $googleService->isConfigured();
        $googleConnected = $profile && !empty($profile['google_refresh_token']);

        require __DIR__ . '/../Views/scheduler/index.php';
    }

    /**
     * Scheduler Settings — profile setup
     */
    public function settings()
    {
        header('Location: /erp/scheduler?tab=settings');
        exit;
    }

    /**
     * Save scheduler settings
     */
    public function saveSettings()
    {
        $title = trim($_POST['title'] ?? '30-Minute Meeting');
        $description = trim($_POST['description'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $duration = (int)($_POST['duration'] ?? 30);
        $timezone = $_POST['timezone'] ?? 'Africa/Lagos';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Build slug from title if empty
        if (empty($slug)) {
            $user = \App\Core\Auth::user();
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $user['name'] ?? 'meeting'));
            $slug = trim($slug, '-');
        }

        // Build availability JSON
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $availability = [];
        foreach ($days as $day) {
            $availability[$day] = [
                'enabled' => isset($_POST['avail_' . $day]),
                'start'   => $_POST['avail_' . $day . '_start'] ?? '09:00',
                'end'     => $_POST['avail_' . $day . '_end'] ?? '17:00',
            ];
        }

        $meetingLink = trim($_POST['meeting_link'] ?? '');

        $profile = $this->getProfile();

        try {
            $this->pdo->exec("ALTER TABLE erp_scheduler_profiles ADD COLUMN meeting_link VARCHAR(500) NULL AFTER is_active");
        } catch (\Exception $e) {}

        if ($profile) {
            // Update
            $stmt = $this->pdo->prepare(
                "UPDATE erp_scheduler_profiles
                 SET title = ?, description = ?, slug = ?, duration = ?, timezone = ?, availability = ?, is_active = ?, meeting_link = ?
                 WHERE id = ?"
            );
            $stmt->execute([$title, $description, $slug, $duration, $timezone, json_encode($availability), $isActive, $meetingLink, $profile['id']]);
        } else {
            // Create
            $stmt = $this->pdo->prepare(
                "INSERT INTO erp_scheduler_profiles (tenant_id, user_id, slug, title, description, duration, timezone, availability, is_active, meeting_link)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$this->tenantId, $this->userId, $slug, $title, $description, $duration, $timezone, json_encode($availability), $isActive, $meetingLink]);
        }

        header('Location: /erp/scheduler?saved=1&tab=settings');
        exit;
    }

    /**
     * Bookings list
     */
    public function bookings()
    {
        $profile = $this->getProfile();
        $bookings = [];
        $filter = $_GET['filter'] ?? 'all';

        if ($profile) {
            $sql = "SELECT * FROM erp_scheduler_bookings WHERE profile_id = ?";
            $params = [$profile['id']];

            if ($filter === 'upcoming') {
                $sql .= " AND booking_date >= CURDATE() AND status = 'confirmed'";
            } elseif ($filter === 'cancelled') {
                $sql .= " AND status = 'cancelled'";
            }

            $sql .= " ORDER BY booking_date DESC, start_time DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../Views/scheduler/bookings.php';
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking()
    {
        $id = $_POST['id'] ?? 0;
        $reason = trim($_POST['cancel_reason'] ?? '');
        $profile = $this->getProfile();

        if ($profile) {
            $stmt = $this->pdo->prepare("UPDATE erp_scheduler_bookings SET status = 'cancelled', cancel_reason = ? WHERE id = ? AND profile_id = ?");
            $stmt->execute([$reason, $id, $profile['id']]);
            $this->notifyCancellation($id);
        }

        header('Location: /erp/scheduler?tab=bookings');
        exit;
    }

    /**
     * Public booking page — no auth required
     */
    public function publicPage($params)
    {
        $slug = $params['slug'] ?? '';

        $stmt = $this->pdo->prepare(
            "SELECT p.*, u.name as host_name, u.email as host_email
             FROM erp_scheduler_profiles p
             JOIN users u ON u.id = p.user_id
             WHERE p.slug = ? AND p.is_active = 1"
        );
        $stmt->execute([$slug]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile) {
            http_response_code(404);
            echo "This booking page is not available.";
            return;
        }

        $availability = json_decode($profile['availability'], true) ?? [];

        require __DIR__ . '/../Views/scheduler/public_booking.php';
    }

    /**
     * AJAX: Get available time slots for a given date
     */
    public function getAvailableSlots($params)
    {
        header('Content-Type: application/json');
        $slug = $params['slug'] ?? '';
        $date = $_GET['date'] ?? '';

        if (empty($date)) {
            echo json_encode(['error' => 'Date required']);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM erp_scheduler_profiles WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile) {
            echo json_encode(['error' => 'Profile not found', 'slots' => []]);
            return;
        }

        $availability = json_decode($profile['availability'], true) ?? [];
        $dayName = strtolower(date('l', strtotime($date)));

        if (!isset($availability[$dayName]) || !$availability[$dayName]['enabled']) {
            echo json_encode(['slots' => []]);
            return;
        }

        $dayConfig = $availability[$dayName];
        $duration = (int)($profile['duration'] ?? 30);
        if ($duration <= 0) $duration = 30;

        // Generate time slots
        $startTime = strtotime($date . ' ' . $dayConfig['start']);
        $endTime = strtotime($date . ' ' . $dayConfig['end']);
        $slots = [];

        $tz = $profile['timezone'] ?? 'Africa/Lagos';
        try {
            $dt = new \DateTime('now', new \DateTimeZone($tz));
            $todayInTz = $dt->format('Y-m-d');
            $nowInTz = $dt->getTimestamp();
        } catch (\Exception $e) {
            $todayInTz = date('Y-m-d');
            $nowInTz = time();
        }

        // If date is before today, return empty
        if ($date < $todayInTz) {
            echo json_encode(['slots' => []]);
            return;
        }

        while ($startTime + ($duration * 60) <= $endTime) {
            // If date is today, skip past slots (with 10 min grace)
            if ($date === $todayInTz) {
                $slotStamp = strtotime($date . ' ' . date('H:i:s', $startTime));
                if ($slotStamp <= ($nowInTz + 600)) {
                    $startTime += ($duration * 60);
                    continue;
                }
            }

            $slotStart = date('H:i', $startTime);
            $slotEnd = date('H:i', $startTime + ($duration * 60));
            $slots[] = ['start' => $slotStart, 'end' => $slotEnd];
            $startTime += ($duration * 60);
        }

        // Remove already booked slots
        $bookedTimes = [];
        try {
            $stmt = $this->pdo->prepare(
                "SELECT start_time, end_time FROM erp_scheduler_bookings
                 WHERE profile_id = ? AND booking_date = ? AND status = 'confirmed'"
            );
            $stmt->execute([$profile['id'], $date]);
            $booked = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($booked as $b) {
                $bookedTimes[] = substr($b['start_time'], 0, 5);
            }
        } catch (\Exception $e) {}

        $slots = array_filter($slots, function ($slot) use ($bookedTimes) {
            return !in_array($slot['start'], $bookedTimes);
        });

        // Check blocked dates
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM erp_scheduler_blocked_dates WHERE profile_id = ? AND blocked_date = ?");
            $stmt->execute([$profile['id'], $date]);
            if ($stmt->fetch()) {
                $slots = []; // Entire day blocked
            }
        } catch (\Exception $e) {}

        // Check Google Calendar free/busy if connected
        if (!empty($profile['google_refresh_token']) && !empty($slots)) {
            try {
                $gcal = new GoogleCalendarService();
                $accessToken = $gcal->getAccessToken($profile);
                if ($accessToken) {
                    $timeMin = $date . 'T' . $dayConfig['start'] . ':00';
                    $timeMax = $date . 'T' . $dayConfig['end'] . ':00';
                    $tz = $profile['timezone'] ?? 'Africa/Lagos';

                    $timeMinISO = date('c', strtotime($timeMin));
                    $timeMaxISO = date('c', strtotime($timeMax));

                    $busySlots = $gcal->getFreeBusy($accessToken, $timeMinISO, $timeMaxISO, $tz);

                    // Filter out busy times
                    $slots = array_filter($slots, function ($slot) use ($busySlots, $date) {
                        $slotStart = strtotime($date . ' ' . $slot['start']);
                        $slotEnd = strtotime($date . ' ' . $slot['end']);

                        foreach ($busySlots as $busy) {
                            $busyStart = strtotime($busy['start']);
                            $busyEnd = strtotime($busy['end']);
                            if ($slotStart < $busyEnd && $slotEnd > $busyStart) {
                                return false; // Overlap
                            }
                        }
                        return true;
                    });
                }
            } catch (\Exception $e) {
                error_log('Google FreeBusy check failed: ' . $e->getMessage());
            }
        }

        echo json_encode(['slots' => array_values($slots)]);
    }

    /**
     * Process a booking submission — public, no auth
     */
    public function submitBooking($params)
    {
        $slug = $params['slug'] ?? '';

        $stmt = $this->pdo->prepare("SELECT * FROM erp_scheduler_profiles WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile) {
            http_response_code(404);
            echo "Booking page not found.";
            return;
        }

        $guestName = trim($_POST['guest_name'] ?? '');
        $guestEmail = trim($_POST['guest_email'] ?? '');
        $guestPhone = trim($_POST['guest_phone'] ?? '');
        $guestNotes = trim($_POST['guest_notes'] ?? '');
        $bookingDate = $_POST['booking_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';

        if (empty($guestName) || empty($guestEmail) || empty($bookingDate) || empty($startTime)) {
            header('Location: /book/' . $slug . '?error=missing_fields');
            exit;
        }

        $duration = (int)$profile['duration'];
        $endTime = date('H:i', strtotime($startTime) + ($duration * 60));
        $cancelToken = bin2hex(random_bytes(32));

        // Check for double booking
        $stmt = $this->pdo->prepare(
            "SELECT id FROM erp_scheduler_bookings
             WHERE profile_id = ? AND booking_date = ? AND start_time = ? AND status = 'confirmed'"
        );
        $stmt->execute([$profile['id'], $bookingDate, $startTime]);
        if ($stmt->fetch()) {
            header('Location: /book/' . $slug . '?error=slot_taken');
            exit;
        }

        // Try to create a Google Calendar event
        $meetLink = '';
        $eventId = '';

        if (!empty($profile['google_refresh_token'])) {
            try {
                $gcal = new GoogleCalendarService();
                $accessToken = $gcal->getAccessToken($profile);
                if ($accessToken) {
                    $tz = $profile['timezone'] ?? 'Africa/Lagos';
                    $startISO = date('c', strtotime($bookingDate . ' ' . $startTime));
                    $endISO = date('c', strtotime($bookingDate . ' ' . $endTime));

                    $result = $gcal->createEvent($accessToken, [
                        'summary' => $profile['title'] . ' with ' . $guestName,
                        'description' => "Booked via Casjoe Scheduler\n\nGuest: $guestName\nEmail: $guestEmail\nPhone: $guestPhone\nNotes: $guestNotes",
                        'start' => $startISO,
                        'end' => $endISO,
                        'timezone' => $tz,
                        'guest_email' => $guestEmail
                    ]);

                    if ($result) {
                        $eventId = $result['event_id'];
                        $meetLink = $result['meet_link'];
                    }
                }
            } catch (\Exception $e) {
                error_log('Google Calendar event creation failed: ' . $e->getMessage());
            }
        }

        if (empty($meetLink) && !empty($profile['meeting_link'])) {
            $meetLink = $profile['meeting_link'];
        }

        // Auto-create or find a lead in CRM
        $leadId = null;
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM erp_crm_leads WHERE tenant_id = ? AND email = ? LIMIT 1");
            $stmt->execute([$profile['tenant_id'], $guestEmail]);
            $lead = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lead) {
                $leadId = $lead['id'];
                \App\Core\Services\LeadScoringService::addPoints($profile['tenant_id'], (int)$leadId, 'Booking scheduled via Scheduler', 25);
            } else {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO erp_crm_leads (tenant_id, name, email, phone, source, status) VALUES (?, ?, ?, ?, 'Scheduler', 'contacted')"
                );
                $stmt->execute([$profile['tenant_id'], $guestName, $guestEmail, $guestPhone]);
                $leadId = $this->pdo->lastInsertId();
                \App\Core\Services\LeadScoringService::addPoints($profile['tenant_id'], (int)$leadId, 'Booking scheduled via Scheduler', 25);
            }
        } catch (\Exception $e) {
            error_log('Auto-lead creation failed: ' . $e->getMessage());
        }

        // Save booking
        $stmt = $this->pdo->prepare(
            "INSERT INTO erp_scheduler_bookings (tenant_id, profile_id, guest_name, guest_email, guest_phone, guest_notes, booking_date, start_time, end_time, google_event_id, meet_link, cancel_token, lead_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $profile['tenant_id'], $profile['id'],
            $guestName, $guestEmail, $guestPhone, $guestNotes,
            $bookingDate, $startTime, $endTime,
            $eventId, $meetLink, $cancelToken, $leadId
        ]);
        $bookingId = $this->pdo->lastInsertId();

        // Get host info
        $stmt = $this->pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$profile['user_id']]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send confirmation email to guest
        $formattedDate = date('l, F j, Y', strtotime($bookingDate));
        $formattedStart = date('g:i A', strtotime($startTime));
        $formattedEnd = date('g:i A', strtotime($endTime));

        $meetSection = '';
        if ($meetLink) {
            $meetSection = "<p style='margin-top:15px;'><strong>📹 Google Meet Link:</strong><br><a href='$meetLink' style='color: #4285f4;'>$meetLink</a></p>";
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $baseUrl = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'app.casjoe.com');
        $cancelUrl = $baseUrl . '/book/' . $slug . '/cancel?token=' . $cancelToken;

        // Google Calendar add link for guest
        $gcalLink = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            . '&text=' . urlencode($profile['title'] . ' with ' . ($host['name'] ?? 'Host'))
            . '&dates=' . date('Ymd\THis', strtotime($bookingDate . ' ' . $startTime)) . '/' . date('Ymd\THis', strtotime($bookingDate . ' ' . $endTime))
            . '&details=' . urlencode("Meeting booked via Casjoe Scheduler.\n" . ($meetLink ? "Join: $meetLink" : ''))
            . '&sf=true';

        $emailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #000066, #4e54c8); padding: 30px; border-radius: 12px 12px 0 0; text-align: center;'>
                    <h1 style='color: #fff; margin: 0; font-size: 24px;'>📅 Meeting Confirmed!</h1>
                </div>
                <div style='padding: 30px; background: #f9f9f9; border: 1px solid #eee; border-radius: 0 0 12px 12px;'>
                    <p style='color: #333; font-size: 16px;'>Hi <strong>$guestName</strong>,</p>
                    <p style='color: #555;'>Your meeting has been successfully booked. Here are the details:</p>
                    <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #FFA600; margin: 20px 0;'>
                        <p><strong>📋 Meeting:</strong> {$profile['title']}</p>
                        <p><strong>👤 Host:</strong> " . htmlspecialchars($host['name'] ?? 'Host') . "</p>
                        <p><strong>📅 Date:</strong> $formattedDate</p>
                        <p><strong>🕐 Time:</strong> $formattedStart — $formattedEnd</p>
                        <p><strong>⏱ Duration:</strong> {$duration} minutes</p>
                    </div>
                    $meetSection
                    <div style='text-align: center; margin-top: 25px;'>
                        <a href='$gcalLink' style='display: inline-block; background: #4285f4; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-right: 10px;'>📅 Add to Google Calendar</a>
                    </div>
                    <p style='margin-top: 30px; color: #888; font-size: 13px; text-align: center;'>
                        Need to cancel? <a href='$cancelUrl' style='color: #e74a3b;'>Click here to cancel</a>
                    </p>
                </div>
            </div>
        ";

        try {
            Mailer::send($guestEmail, "Meeting Confirmed: {$profile['title']}", $emailBody, false);
        } catch (\Exception $e) {
            error_log('Booking confirmation email failed: ' . $e->getMessage());
        }

        // Send notification email to host
        $hostEmailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #000066, #4e54c8); padding: 30px; border-radius: 12px 12px 0 0; text-align: center;'>
                    <h1 style='color: #fff; margin: 0; font-size: 24px;'>🔔 New Booking!</h1>
                </div>
                <div style='padding: 30px; background: #f9f9f9; border: 1px solid #eee; border-radius: 0 0 12px 12px;'>
                    <p style='color: #333; font-size: 16px;'>Hi <strong>" . htmlspecialchars($host['name'] ?? 'Host') . "</strong>,</p>
                    <p style='color: #555;'>Someone just booked an appointment with you. Here are the details:</p>
                    <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #4e54c8; margin: 20px 0;'>
                        <p><strong>👤 Guest:</strong> $guestName ($guestEmail)</p>
                        <p><strong>📞 Phone:</strong> " . ($guestPhone ?: 'Not provided') . "</p>
                        <p><strong>📅 Date:</strong> $formattedDate</p>
                        <p><strong>🕐 Time:</strong> $formattedStart — $formattedEnd</p>
                        <p><strong>📋 Meeting:</strong> {$profile['title']}</p>
                        " . ($guestNotes ? "<p><strong>📝 Notes:</strong> " . nl2br(htmlspecialchars($guestNotes)) . "</p>" : "") . "
                    </div>
                    $meetSection
                    <p style='margin-top: 30px; color: #888; font-size: 13px; text-align: center;'>
                        View all bookings in your <a href='$baseUrl/erp/scheduler/bookings' style='color: #4e54c8;'>Scheduler Dashboard</a>
                    </p>
                </div>
            </div>
        ";

        try {
            Mailer::send($host['email'], "New Booking: {$profile['title']} with $guestName", $hostEmailBody, false);
        } catch (\Exception $e) {
            error_log('Host booking notification email failed: ' . $e->getMessage());
        }

        // Redirect to confirmation page
        $booking = [
            'id' => $bookingId,
            'guest_name' => $guestName,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'meet_link' => $meetLink,
            'host_name' => $host['name'] ?? 'Host',
            'title' => $profile['title'],
            'duration' => $duration,
            'gcal_link' => $gcalLink
        ];

        require __DIR__ . '/../Views/scheduler/confirmation.php';
    }

    /**
     * Cancel booking via token (public)
     */
    public function publicCancel($params)
    {
        $slug = $params['slug'] ?? '';
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            echo "Invalid cancel link.";
            return;
        }

        $stmt = $this->pdo->prepare(
            "SELECT b.*, p.slug, p.title, u.name as host_name
             FROM erp_scheduler_bookings b
             JOIN erp_scheduler_profiles p ON p.id = b.profile_id
             JOIN users u ON u.id = p.user_id
             WHERE b.cancel_token = ? AND p.slug = ?"
        );
        $stmt->execute([$token, $slug]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            echo "Booking not found or already cancelled.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = trim($_POST['cancel_reason'] ?? '');
            $stmt = $this->pdo->prepare("UPDATE erp_scheduler_bookings SET status = 'cancelled', cancel_reason = ? WHERE id = ?");
            $stmt->execute([$reason, $booking['id']]);

            $this->notifyCancellation($booking['id']);

            $cancelled = true;
            require __DIR__ . '/../Views/scheduler/confirmation.php';
        } else {
            require __DIR__ . '/../Views/scheduler/public_cancel.php';
        }
    }

    /**
     * Google OAuth — redirect to Google
     */
    public function googleAuth()
    {
        $profile = $this->getProfile();
        if (!$profile) {
            header('Location: /erp/scheduler/settings?error=profile_required');
            exit;
        }

        $gcal = new GoogleCalendarService();
        if (!$gcal->isConfigured()) {
            header('Location: /erp/scheduler/settings?error=google_not_configured');
            exit;
        }

        $authUrl = $gcal->getAuthUrl($profile['id']);
        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Google OAuth callback — save tokens
     */
    public function googleCallback()
    {
        error_log('Scheduler OAuth callback: ' . json_encode($_GET));

        // Check for error from Google
        if (isset($_GET['error'])) {
            error_log('Google OAuth returned error: ' . $_GET['error']);
            header('Location: /erp/scheduler/settings?error=' . urlencode($_GET['error']));
            exit;
        }

        $code = $_GET['code'] ?? '';
        $profileId = $_GET['state'] ?? '';

        if (empty($code) || empty($profileId)) {
            error_log('Scheduler OAuth: missing code or state');
            header('Location: /erp/scheduler/settings?error=oauth_cancelled');
            exit;
        }

        $gcal = new GoogleCalendarService();
        $tokens = $gcal->exchangeCode($code);

        if (!$tokens) {
            error_log('Scheduler OAuth: token exchange failed');
            header('Location: /erp/scheduler/settings?error=token_exchange_failed');
            exit;
        }

        error_log('Scheduler OAuth: tokens received, refresh=' . (isset($tokens['refresh_token']) ? 'yes' : 'no'));

        $expiresAt = date('Y-m-d H:i:s', time() + ($tokens['expires_in'] ?? 3600));

        // Use profile ID only (it's unique), don't filter by tenant
        $stmt = $this->pdo->prepare(
            "UPDATE erp_scheduler_profiles SET google_access_token = ?, google_refresh_token = ?, google_token_expires_at = ? WHERE id = ?"
        );
        $stmt->execute([
            $tokens['access_token'],
            $tokens['refresh_token'] ?? '',
            $expiresAt,
            $profileId
        ]);

        $rows = $stmt->rowCount();
        error_log("Scheduler OAuth: profile $profileId updated, rows=$rows");

        header('Location: /erp/scheduler?google_connected=1&tab=settings');
        exit;
    }

    /**
     * Disconnect Google Calendar
     */
    public function googleDisconnect()
    {
        $profile = $this->getProfile();
        if ($profile) {
            $stmt = $this->pdo->prepare(
                "UPDATE erp_scheduler_profiles SET google_access_token = NULL, google_refresh_token = NULL, google_token_expires_at = NULL WHERE id = ?"
            );
            $stmt->execute([$profile['id']]);
        }

        header('Location: /erp/scheduler?google_disconnected=1&tab=settings');
        exit;
    }

    // Helpers
    private function getProfile()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM erp_scheduler_profiles WHERE tenant_id = ? AND user_id = ?");
        $stmt->execute([$this->tenantId, $this->userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Send cancellation notifications to both host and guest
     */
    private function notifyCancellation($bookingId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT b.*, p.title as meeting_title, u.name as host_name, u.email as host_email, p.timezone
             FROM erp_scheduler_bookings b
             JOIN erp_scheduler_profiles p ON p.id = b.profile_id
             JOIN users u ON u.id = p.user_id
             WHERE b.id = ?"
        );
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) return;

        $formattedDate = date('l, F j, Y', strtotime($booking['booking_date']));
        $formattedTime = date('g:i A', strtotime($booking['start_time']));

        $reasonSection = "";
        if (!empty($booking['cancel_reason'])) {
            $reasonSection = "
                <div style='margin-top: 20px; padding: 15px; background: #fff8f8; border: 1px solid #ffebeb; border-radius: 8px;'>
                    <p style='margin: 0; color: #666; font-size: 0.9rem;'><strong>Reason for cancellation:</strong></p>
                    <p style='margin: 5px 0 0 0; color: #333;'>{$booking['cancel_reason']}</p>
                </div>
            ";
        }

        // Email to Guest
        $guestEmailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #e74a3b; padding: 30px; border-radius: 12px 12px 0 0; text-align: center;'>
                    <h1 style='color: #fff; margin: 0; font-size: 24px;'>❌ Meeting Cancelled</h1>
                </div>
                <div style='padding: 30px; background: #f9f9f9; border: 1px solid #eee; border-radius: 0 0 12px 12px;'>
                    <p style='color: #333; font-size: 16px;'>Hi <strong>{$booking['guest_name']}</strong>,</p>
                    <p style='color: #555;'>Your meeting has been cancelled. Here were the details:</p>
                    <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #e74a3b; margin: 20px 0;'>
                        <p><strong>📋 Meeting:</strong> {$booking['meeting_title']}</p>
                        <p><strong>👤 Host:</strong> {$booking['host_name']}</p>
                        <p><strong>📅 Date:</strong> $formattedDate</p>
                        <p><strong>🕐 Time:</strong> $formattedTime</p>
                    </div>
                    $reasonSection
                    <p style='color: #888; font-size: 13px; margin-top: 20px;'>If you'd like to reschedule, please visit the booking page again.</p>
                </div>
            </div>
        ";

        // Email to Host
        $hostEmailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #5a5c69; padding: 30px; border-radius: 12px 12px 0 0; text-align: center;'>
                    <h1 style='color: #fff; margin: 0; font-size: 24px;'>❌ Booking Cancelled</h1>
                </div>
                <div style='padding: 30px; background: #f9f9f9; border: 1px solid #eee; border-radius: 0 0 12px 12px;'>
                    <p style='color: #333; font-size: 16px;'>Hi <strong>{$booking['host_name']}</strong>,</p>
                    <p style='color: #555;'>A booking has been cancelled:</p>
                    <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #5a5c69; margin: 20px 0;'>
                        <p><strong>👤 Guest:</strong> {$booking['guest_name']} ({$booking['guest_email']})</p>
                        <p><strong>📋 Meeting:</strong> {$booking['meeting_title']}</p>
                        <p><strong>📅 Date:</strong> $formattedDate</p>
                        <p><strong>🕐 Time:</strong> $formattedTime</p>
                    </div>
                    $reasonSection
                </div>
            </div>
        ";

        try {
            Mailer::send($booking['guest_email'], "Cancelled: {$booking['meeting_title']}", $guestEmailBody, false);
            Mailer::send($booking['host_email'], "Cancelled: {$booking['meeting_title']} with {$booking['guest_name']}", $hostEmailBody, false);
        } catch (\Exception $e) {
            error_log('Cancellation email failed: ' . $e->getMessage());
        }
    }
}
