<?php
namespace App\Core\AI\Providers;
class OllamaProvider implements ProviderInterface {
    protected string $baseUrl; protected string $model;
    public function __construct(string $baseUrl, string $model) { $this->baseUrl = rtrim($baseUrl, "/"); $this->model = $model ?: "llama2"; }
    public function getProviderName(): string { return "ollama"; }
    public function generateText(string $prompt, array $config = []): string {
        $url = $this->baseUrl . "/api/generate";
        $payload = [ "model" => $this->model, "prompt" => $prompt, "stream" => false ];
        $ch = curl_init($url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        $response = curl_exec($ch); $data = json_decode($response, true);
        return $data["response"] ?? "";
    }
    public function analyzeText(string $text, string $instruction): array { return []; }
}
