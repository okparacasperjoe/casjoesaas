<?php
namespace App\Core\AI\Providers;

class OpenAIProvider implements ProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model)
    {
        $this->apiKey = $apiKey;
        $this->model = $model ?: "gpt-4"; // Default to GPT-4 if not set
    }
    public function getProviderName(): string { return "openai"; }
    public function generateText(string $prompt, array $config = []): string
    {
        $url = "https://api.openai.com/v1/chat/completions";
        $payload = [
            "model" => $this->model,
            "messages" => [
                ["role" => "system", "content" => "You are a helpful business assistant."],
                ["role" => "user", "content" => $prompt]
            ],
            "max_tokens" => $config["max_tokens"] ?? 300,
            "temperature" => $config["temperature"] ?? 0.7
        ];
        $response = $this->makeRequest($url, $payload);
        
        if (isset($response['error'])) {
            return "Error: OpenAI API - " . (is_array($response['error']) ? json_encode($response['error']) : $response['error']);
        }
        
        return $response["choices"][0]["message"]["content"] ?? "Error: OpenAI returned an empty response";
    }
    public function analyzeText(string $text, string $instruction): array { return []; }
    protected function makeRequest($url, $payload) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ]);
        $response = curl_exec($ch);

        return json_decode($response, true);
    }
}
