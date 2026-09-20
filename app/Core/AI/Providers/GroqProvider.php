<?php
namespace App\Core\AI\Providers;

class GroqProvider implements ProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model = "llama-3.3-70b-versatile")
    {
        $this->apiKey = $apiKey;
        $this->model = !empty($model) ? $model : "llama-3.3-70b-versatile";
    }

    public function getProviderName(): string { return "groq"; }

    public function generateText(string $prompt, array $config = []): string
    {
        $url = "https://api.groq.com/openai/v1/chat/completions";
        $payload = [
            "model" => $this->model,
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ],
            "max_tokens" => $config["max_tokens"] ?? 500,
            "temperature" => $config["temperature"] ?? 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($err) {
            return "Error: Groq cURL failed - " . $err;
        }

        $data = json_decode($response, true);
        if ($httpCode >= 400 || isset($data['error'])) {
            $errMsg = $data['error']['message'] ?? ($data['error'] ?? $response);
            if (is_array($errMsg)) $errMsg = json_encode($errMsg);
            return "Error: Groq API " . $httpCode . " - " . $errMsg;
        }

        return trim($data["choices"][0]["message"]["content"] ?? "Error: Groq returned empty response");
    }

    public function analyzeText(string $text, string $instruction): array { return []; }
}
