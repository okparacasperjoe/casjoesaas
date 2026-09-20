<?php
namespace App\Core\AI\Providers;

/**
 * MuseGlimmerProvider
 *
 * Connects to Meta's Muse Glimmer 30B model via OpenRouter's OpenAI-compatible API.
 * OpenRouter Base URL : https://openrouter.ai/api/v1
 * Model ID           : meta/muse-glimmer-30b
 *
 * Requires an OpenRouter API key (https://openrouter.ai/keys).
 */
class MuseGlimmerProvider implements ProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct(string $apiKey, string $model = 'meta/muse-glimmer-30b')
    {
        $this->apiKey  = $apiKey;
        $this->model   = !empty($model) ? $model : 'meta/muse-glimmer-30b';
        $this->baseUrl = 'https://openrouter.ai/api/v1/chat/completions';
    }

    public function getProviderName(): string
    {
        return 'muse_glimmer';
    }

    public function generateText(string $prompt, array $config = []): string
    {
        if (empty($this->apiKey)) {
            return 'Error: Muse Glimmer – no OpenRouter API key configured.';
        }

        $payload = [
            'model'      => $this->model,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens'  => $config['max_tokens']  ?? 1024,
            'temperature' => $config['temperature'] ?? 0.7,
        ];

        $ch = curl_init($this->baseUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'HTTP-Referer: https://casjoe.com',   // OpenRouter recommended header
                'X-Title: Casjoe Cori AI',
            ],
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($err) {
            return 'Error: Muse Glimmer cURL failed – ' . $err;
        }

        $data = json_decode($response, true);

        if ($httpCode >= 400 || isset($data['error'])) {
            $errMsg = $data['error']['message'] ?? ($data['error'] ?? $response);
            if (is_array($errMsg)) $errMsg = json_encode($errMsg);
            return 'Error: Muse Glimmer API ' . $httpCode . ' – ' . $errMsg;
        }

        return trim($data['choices'][0]['message']['content'] ?? 'Error: Muse Glimmer returned an empty response.');
    }

    public function analyzeText(string $text, string $instruction): array
    {
        return [];
    }
}
