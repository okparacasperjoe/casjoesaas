<?php

namespace App\Core\Services;

class GeminiService
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        // ideally load from .env or settings
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : 'YOUR_API_KEY');
    }

    public function generateEmailContent($prompt, $tone = 'professional')
    {
        $fullPrompt = "You are a professional AI Assistant for Casjoe ERP. Tone: $tone. Generate content for the following request:\n\n$prompt";
        return $this->generateReal($fullPrompt);
    }

    public function generateReal($prompt) {
        if ($this->apiKey === 'YOUR_API_KEY' || empty($this->apiKey)) {
            return "Error: Gemini API Key is missing. Please add it to your .env file.";
        }

        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];
        
        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            return "cURL Error: " . $err;
        }

        $decoded = json_decode($response, true);
        
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            return $decoded['candidates'][0]['content']['parts'][0]['text'];
        }

        return "AI Error: Could not generate response. " . ($decoded['error']['message'] ?? '');
    }
}
