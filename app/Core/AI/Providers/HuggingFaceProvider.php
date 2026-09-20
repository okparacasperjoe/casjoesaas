<?php
namespace App\Core\AI\Providers;

class HuggingFaceProvider implements ProviderInterface {
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = "https://router.huggingface.co/hf-inference/models/";
    protected string $fallbackUrl = "https://router.huggingface.co/models/";

    public function __construct(string $apiKey, string $model) { 
        $this->apiKey = $apiKey; 
        $sanitized = (!empty($model) && stripos($model, 'flan-t5') === false) ? $model : "Qwen/Qwen2.5-72B-Instruct";
        $this->model = $sanitized; 
    }

    public function getProviderName(): string { return "huggingface"; }

    private function makeRequest(string $url, string $prompt, array $config = []): array {
        $payload = [
            "inputs" => $prompt,
            "parameters" => [
                "max_new_tokens" => $config["max_tokens"] ?? 350,
                "return_full_text" => false,
                "temperature" => 0.7
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $errNo = curl_errno($ch);
        $errStr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [
            'errNo' => $errNo,
            'errStr' => $errStr,
            'httpCode' => $httpCode,
            'response' => $response,
            'url' => $url
        ];
    }

    public function generateText(string $prompt, array $config = []): string {
        $modelsToTry = array_unique([$this->model, "Qwen/Qwen2.5-72B-Instruct", "meta-llama/Meta-Llama-3-8B-Instruct", "HuggingFaceH4/zephyr-7b-beta"]);
        $baseUrls = [$this->baseUrl, $this->fallbackUrl];

        $lastError = "";

        foreach ($modelsToTry as $mdl) {
            foreach ($baseUrls as $base) {
                $url = rtrim($base, '/') . '/' . $mdl;
                $res = $this->makeRequest($url, $prompt, $config);

                if ($res['errNo']) {
                    $lastError = "Error: cURL failed - " . $res['errStr'] . " (URL: " . $url . ")";
                    continue;
                }

                $data = json_decode($res['response'], true);

                if ($res['httpCode'] >= 400) {
                    $errMsg = $data['error'] ?? $res['response'];
                    if (is_array($errMsg)) $errMsg = json_encode($errMsg);
                    // If model is loading (HTTP 503) or not found (HTTP 404/410), try next
                    $lastError = "Error: HF API " . $res['httpCode'] . " - " . $errMsg;
                    continue;
                }

                if (isset($data['error'])) {
                    $lastError = "Error: HF API - " . (is_array($data['error']) ? json_encode($data['error']) : $data['error']);
                    continue;
                }

                if (is_array($data) && isset($data[0]["generated_text"])) {
                    return trim($data[0]["generated_text"]);
                } elseif (is_array($data) && isset($data["generated_text"])) {
                    return trim($data["generated_text"]);
                } elseif (is_string($data) && !empty($data)) {
                    return trim($data);
                } else {
                    $lastError = "Error: Unexpected Response from [" . $url . "] - " . $res['response'];
                    continue;
                }
            }
        }

        return $lastError ?: "Error: Unable to get AI generation from HuggingFace router.";
    }

    public function analyzeText(string $text, string $instruction): array { return []; }
}
