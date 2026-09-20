<?php
namespace App\Core\AI\Providers;

class GeminiProvider implements ProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model = "gemini-pro")
    {
        $this->apiKey = $apiKey;
        $this->model = $model ?: "gemini-pro";
    }
    public function getProviderName(): string { return "gemini"; }
    public function generateText(string $prompt, array $config = []): string
    {
        // Google Gemini REST API
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key=" . $this->apiKey;
        $payload = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            "generationConfig" => [
                "maxOutputTokens" => $config["max_tokens"] ?? 300
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        
        $response = curl_exec($ch);

        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            return "Error: Gemini API - " . (is_array($data['error']) ? json_encode($data['error']) : $data['error']);
        }
        
        return $data["candidates"][0]["content"]["parts"][0]["text"] ?? "Error: Gemini returned an empty response";
    }
    public function analyzeText(string $text, string $instruction): array { return []; }
}
