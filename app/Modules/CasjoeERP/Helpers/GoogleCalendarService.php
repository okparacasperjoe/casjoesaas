<?php

namespace App\Modules\CasjoeERP\Helpers;

use App\Core\Database;

/**
 * Google Calendar Service
 * Handles OAuth, event creation, and free/busy checks using raw cURL.
 */
class GoogleCalendarService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $pdo;

    const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    const CALENDAR_API = 'https://www.googleapis.com/calendar/v3';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();

        // Load from system_settings
        $stmt = $this->pdo->prepare("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('google_client_id', 'google_client_secret')");
        $stmt->execute();
        $settings = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->clientId = $settings['google_client_id'] ?? '';
        $this->clientSecret = $settings['google_client_secret'] ?? '';

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'app.casjoe.com';
        $this->redirectUri = $protocol . '://' . $host . '/erp/scheduler/google/callback';
    }

    /**
     * Check if Google Calendar integration is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }

    /**
     * Get the OAuth authorization URL
     */
    public function getAuthUrl(int $profileId): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.events',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $profileId
        ];

        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCode(string $code): ?array
    {
        $data = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $response = $this->curlPost(self::TOKEN_URL, $data);

        if (isset($response['access_token'])) {
            return $response;
        }

        error_log('Google OAuth Error: ' . json_encode($response));
        return null;
    }

    /**
     * Refresh an expired access token
     */
    public function refreshToken(string $refreshToken): ?array
    {
        $data = [
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token'
        ];

        $response = $this->curlPost(self::TOKEN_URL, $data);

        if (isset($response['access_token'])) {
            return $response;
        }

        error_log('Google Token Refresh Error: ' . json_encode($response));
        return null;
    }

    /**
     * Get a valid access token for a profile, refreshing if needed
     */
    public function getAccessToken(array $profile): ?string
    {
        if (empty($profile['google_refresh_token'])) {
            return null;
        }

        // Check if token is still valid (with 5-min buffer)
        if (!empty($profile['google_access_token']) && !empty($profile['google_token_expires_at'])) {
            $expiresAt = strtotime($profile['google_token_expires_at']);
            if ($expiresAt > time() + 300) {
                return $profile['google_access_token'];
            }
        }

        // Refresh the token
        $tokens = $this->refreshToken($profile['google_refresh_token']);
        if (!$tokens) {
            return null;
        }

        $expiresAt = date('Y-m-d H:i:s', time() + ($tokens['expires_in'] ?? 3600));

        // Update the database
        $stmt = $this->pdo->prepare("UPDATE erp_scheduler_profiles SET google_access_token = ?, google_token_expires_at = ? WHERE id = ?");
        $stmt->execute([$tokens['access_token'], $expiresAt, $profile['id']]);

        return $tokens['access_token'];
    }

    /**
     * Create a Google Calendar event with auto-generated Meet link
     */
    public function createEvent(string $accessToken, array $eventData): ?array
    {
        $url = self::CALENDAR_API . '/calendars/primary/events?conferenceDataVersion=1';

        $body = [
            'summary' => $eventData['summary'],
            'description' => $eventData['description'] ?? '',
            'start' => [
                'dateTime' => $eventData['start'],
                'timeZone' => $eventData['timezone'] ?? 'Africa/Lagos'
            ],
            'end' => [
                'dateTime' => $eventData['end'],
                'timeZone' => $eventData['timezone'] ?? 'Africa/Lagos'
            ],
            'attendees' => [
                ['email' => $eventData['guest_email']]
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid('casjoe-', true),
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ]
                ]
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 30],
                    ['method' => 'popup', 'minutes' => 10]
                ]
            ]
        ];

        $response = $this->curlPostJson($url, $body, $accessToken);

        if (isset($response['id'])) {
            return [
                'event_id' => $response['id'],
                'html_link' => $response['htmlLink'] ?? '',
                'meet_link' => $response['conferenceData']['entryPoints'][0]['uri'] ?? ''
            ];
        }

        error_log('Google Calendar Create Event Error: ' . json_encode($response));
        return null;
    }

    /**
     * Check free/busy for a time range
     */
    public function getFreeBusy(string $accessToken, string $timeMin, string $timeMax, string $timezone = 'Africa/Lagos'): array
    {
        $url = self::CALENDAR_API . '/freeBusy';

        $body = [
            'timeMin' => $timeMin,
            'timeMax' => $timeMax,
            'timeZone' => $timezone,
            'items' => [['id' => 'primary']]
        ];

        $response = $this->curlPostJson($url, $body, $accessToken);

        $busy = [];
        if (isset($response['calendars']['primary']['busy'])) {
            $busy = $response['calendars']['primary']['busy'];
        }

        return $busy;
    }

    // --- cURL helpers ---

    private function curlPost(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 15
        ]);
        $response = curl_exec($ch);

        return json_decode($response, true) ?? [];
    }

    private function curlPostJson(string $url, array $body, string $accessToken): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ],
            CURLOPT_TIMEOUT => 15
        ]);
        $response = curl_exec($ch);

        return json_decode($response, true) ?? [];
    }
}
