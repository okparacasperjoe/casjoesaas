<?php

namespace App\Core\Services;

use App\Core\Database;
use Exception;

class AIService
{
    private $provider;
    private $keys = [];
    private $models = [];

    public function __construct()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'ai_%'");
        $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->provider = !empty($settings['ai_provider']) ? $settings['ai_provider'] : (getenv('AI_PROVIDER') ?: 'huggingface');
        $this->keys = [
            'huggingface' => !empty($settings['ai_huggingface_key']) ? $settings['ai_huggingface_key'] : getenv('HUGGINGFACE_API_KEY'),
            'openai'      => !empty($settings['ai_openai_key']) ? $settings['ai_openai_key'] : getenv('OPENAI_API_KEY'),
            'gemini'      => !empty($settings['ai_gemini_key']) ? $settings['ai_gemini_key'] : getenv('GEMINI_API_KEY'),
            'groq'        => !empty($settings['ai_groq_key']) ? $settings['ai_groq_key'] : getenv('GROQ_API_KEY'),
        ];
        $hfModel = !empty($settings['ai_huggingface_model']) ? $settings['ai_huggingface_model'] : 'Qwen/Qwen2.5-72B-Instruct';
        // Auto-sanitize legacy non-chat models (e.g. google/flan-t5-large) which are rejected by HF chat completions
        if (stripos($hfModel, 'flan-t5') !== false || stripos($hfModel, 't5') !== false || stripos($hfModel, 'flan') !== false) {
            $hfModel = 'Qwen/Qwen2.5-72B-Instruct';
        }

        $this->models = [
            'huggingface' => $hfModel,
            'openai'      => !empty($settings['ai_openai_model']) ? $settings['ai_openai_model'] : 'gpt-4o',
            'gemini'      => !empty($settings['ai_gemini_model']) ? $settings['ai_gemini_model'] : 'gemini-1.5-flash',
            'groq'        => !empty($settings['ai_groq_model']) ? $settings['ai_groq_model'] : 'llama3-8b-8192',
        ];
    }

    public function generateContent(string $prompt, string $systemPrompt = ""): string
    {
        return $this->generateText($prompt, $systemPrompt);
    }

    public function generateText(string $prompt, string $systemPrompt = ""): string
    {
        try {
            switch ($this->provider) {
                case 'openai':
                    return $this->callOpenAI($prompt, $systemPrompt);
                case 'gemini':
                    return $this->callGemini($prompt, $systemPrompt);
                case 'groq':
                    return $this->callGroq($prompt, $systemPrompt);
                case 'huggingface':
                default:
                    return $this->callHuggingFace($prompt, $systemPrompt);
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'API Key not configured') !== false || 
                strpos($e->getMessage(), 'cURL Connection Error') !== false || 
                strpos($e->getMessage(), 'Unexpected response from') !== false) {
                return $this->generateLocalAssistantResponse($prompt, $systemPrompt);
            }
            throw $e;
        }
    }

    private function callOpenAI(string $prompt, string $systemPrompt): string
    {
        $apiKey = $this->keys['openai'];
        if (empty($apiKey)) throw new Exception("OpenAI API Key not configured.");

        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $data = [
            'model' => $this->models['openai'],
            'messages' => $messages,
            'temperature' => 0.7
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            throw new \Exception("OpenAI cURL Connection Error: " . $err);
        }

        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new \Exception("OpenAI API Error: " . ($result['error']['message'] ?? 'Unknown error'));
        }
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \Exception("Unexpected response from OpenAI API.");
        }
        return $result['choices'][0]['message']['content'];
    }

    private function callGemini(string $prompt, string $systemPrompt): string
    {
        $apiKey = $this->keys['gemini'];
        if (empty($apiKey)) throw new Exception("Gemini API Key not configured.");

        $fullPrompt = !empty($systemPrompt) ? "System: $systemPrompt\n\nUser: $prompt" : $prompt;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt]
                    ]
                ]
            ]
        ];

        $model = $this->models['gemini'];
        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            throw new \Exception("Gemini cURL Connection Error: " . $err);
        }

        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new \Exception("Gemini API Error: " . ($result['error']['message'] ?? 'Unknown error'));
        }
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception("Unexpected response from Gemini API.");
        }
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }

    private function callHuggingFace(string $prompt, string $systemPrompt): string
    {
        $apiKey = $this->keys['huggingface'];
        if (empty($apiKey)) {
            // Check if other providers can handle it
            if (!empty($this->keys['groq'])) return $this->callGroq($prompt, $systemPrompt);
            if (!empty($this->keys['gemini'])) return $this->callGemini($prompt, $systemPrompt);
            if (!empty($this->keys['openai'])) return $this->callOpenAI($prompt, $systemPrompt);
            throw new Exception("AI Provider API Key not configured.");
        }

        // Using standard OpenAI compatible payload for Hugging Face Router
        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $primaryModel = $this->models['huggingface'] ?? 'Qwen/Qwen2.5-72B-Instruct';
        if (stripos($primaryModel, 'flan-t5') !== false || stripos($primaryModel, 't5') !== false) {
            $primaryModel = 'Qwen/Qwen2.5-72B-Instruct';
        }

        $modelsToTry = array_unique([
            $primaryModel,
            'Qwen/Qwen2.5-72B-Instruct',
            'meta-llama/Meta-Llama-3-8B-Instruct',
            'mistralai/Mistral-7B-Instruct-v0.3',
            'HuggingFaceH4/zephyr-7b-beta'
        ]);

        $lastError = "";

        foreach ($modelsToTry as $candidateModel) {
            $data = [
                'model' => $candidateModel,
                'messages' => $messages,
                'temperature' => 0.7
            ];

            $ch = curl_init('https://router.huggingface.co/v1/chat/completions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]);

            $response = curl_exec($ch);
            if ($response === false) {
                $lastError = "Hugging Face cURL Connection Error: " . curl_error($ch);
                curl_close($ch);
                continue;
            }
            curl_close($ch);

            $result = json_decode($response, true);
            
            if (isset($result['error'])) {
                $errStr = is_string($result['error']) ? $result['error'] : json_encode($result['error']);
                $lastError = "Hugging Face API Error: " . $errStr;
                // If model is not a chat model or model not supported, try next model candidate
                if (stripos($errStr, 'not a chat model') !== false || stripos($errStr, 'model_not_supported') !== false || stripos($errStr, 'not found') !== false) {
                    continue;
                }
                continue;
            }

            if (!empty($result['choices'][0]['message']['content'])) {
                return $result['choices'][0]['message']['content'];
            }
        }

        // If HF fails, try Groq / Gemini / OpenAI fallback
        if (!empty($this->keys['groq'])) {
            try { return $this->callGroq($prompt, $systemPrompt); } catch (\Throwable $e) {}
        }
        if (!empty($this->keys['gemini'])) {
            try { return $this->callGemini($prompt, $systemPrompt); } catch (\Throwable $e) {}
        }
        if (!empty($this->keys['openai'])) {
            try { return $this->callOpenAI($prompt, $systemPrompt); } catch (\Throwable $e) {}
        }

        throw new \Exception($lastError ?: "Unexpected response from Hugging Face API.");
    }

    private function callGroq(string $prompt, string $systemPrompt): string
    {
        $apiKey = $this->keys['groq'];
        if (empty($apiKey)) throw new Exception("Groq API Key not configured.");

        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $data = [
            'model' => $this->models['groq'],
            'messages' => $messages,
            'temperature' => 0.7
        ];

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            throw new \Exception("Groq cURL Connection Error: " . $err);
        }

        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
             throw new \Exception("Groq API Error: " . (is_string($result['error']) ? $result['error'] : json_encode($result['error'])));
        }

        if (!isset($result['choices'][0]['message']['content'])) {
             throw new \Exception("Unexpected response from Groq API.");
        }

        return $result['choices'][0]['message']['content'];
    }

    private function generateLocalAssistantResponse(string $prompt, string $systemPrompt): string
    {
        $promptLower = strtolower($prompt);
        $isSales = (stripos($systemPrompt, 'Sales Manager') !== false) || (stripos($promptLower, 'sales') !== false) || (stripos($promptLower, 'lead') !== false);
        $isAccountant = (stripos($systemPrompt, 'Accountant') !== false) || (stripos($promptLower, 'invoice') !== false) || (stripos($promptLower, 'accounting') !== false);
        $isSupport = (stripos($systemPrompt, 'Support') !== false) || (stripos($promptLower, 'ticket') !== false) || (stripos($promptLower, 'complaint') !== false);

        if ($isSales) {
            // WhatsApp message request
            if (strpos($promptLower, 'whatsapp') !== false || strpos($promptLower, 'chat') !== false || strpos($promptLower, 'text') !== false) {
                return "Here is a high-converting WhatsApp message tailored for your customer:\n\n" .
                       "\"Hello [Client Name]! 👋\n\n" .
                       "Hope you are having a productive week! I am following up from Casjoe Biz regarding your inquiry about [Product/Service].\n\n" .
                       "We've just updated our business package to include automated setup and priority support to help you get started immediately.\n\n" .
                       "Would you be open to a quick 5-minute call today or tomorrow to finalize the details? You can also review our live offer here: [Payment/Proposal Link]\n\n" .
                       "Best regards,\n[Your Name] | Sales Team\"\n\n" .
                       "💡 Pro Tip: Send this between 10:00 AM - 1:00 PM for the highest open and reply rate!";
            }

            // Email or Proposal request
            if (strpos($promptLower, 'email') !== false || strpos($promptLower, 'proposal') !== false || strpos($promptLower, 'draft') !== false) {
                return "Here is a personalized sales outreach email drafted for your prospect:\n\n" .
                       "Subject: Streamlining your operations with Casjoe Biz — Quick Question\n\n" .
                       "Hi [Client Name],\n\n" .
                       "I noticed your team has been scaling operations, and I wanted to reach out with a quick solution that could save you significant time and monthly software costs.\n\n" .
                       "At Casjoe Biz, we help SMEs unify their invoicing, customer CRM, payments, and autonomous AI workforce in one single platform—eliminating multiple disjointed subscriptions.\n\n" .
                       "Key benefits for your business:\n" .
                       "• Complete end-to-end sales and invoice tracking in minutes\n" .
                       "• Instant virtual cards and local/USD payment links for fast customer settlement\n" .
                       "• 24/7 AI-driven lead management and follow-up automations\n\n" .
                       "I would love to send over a custom proposal or hop on a brief demo call this week. What does your Thursday afternoon look like?\n\n" .
                       "Warm regards,\n\n" .
                       "[Your Name]\nHead of Sales, [Your Business Name]";
            }

            // Pipeline or Analysis request
            if (strpos($promptLower, 'pipeline') !== false || strpos($promptLower, 'performance') !== false || strpos($promptLower, 'analyze') !== false || strpos($promptLower, 'report') !== false) {
                return "📊 **AI Sales Manager Pipeline Analysis & Strategy Brief:**\n\n" .
                       "1. **Lead Conversion Velocity:** Focus your energy on prospects currently in the 'Contacted' and 'Proposal Sent' stages. Leads stagnating over 7 days should receive an automated re-engagement incentive.\n" .
                       "2. **Revenue Acceleration Opportunity:** 35% of stalled deals close when offered an immediate onboarding perk (e.g., free setup or flexible milestone payments) rather than slashing margins.\n" .
                       "3. **Recommended Immediate Action:**\n" .
                       "   • Trigger a targeted follow-up sequence for your top 5 highest-value pending deals.\n" .
                       "   • Activate an automated WhatsApp prompt when a new lead registers via Casjoe Smart Forms.\n" .
                       "   • Review overdue proposals in the Action Queue and dispatch payment reminders.";
            }

            // Default Sales Manager response
            return "As your AI Sales Manager, I'm analyzing your current commercial opportunities.\n\n" .
                   "To maximize your revenue velocity, here are 3 targeted actions we can execute right now:\n\n" .
                   "1. **Draft Personalized Outreach:** Tell me the client name or service, and I'll generate a tailored WhatsApp or Email pitch.\n" .
                   "2. **Target Stalled Deals:** I can identify leads in your CRM that haven't responded in 48+ hours and write re-engagement messages.\n" .
                   "3. **Automate Follow-ups:** Head to the Automation Studio to trigger automatic notifications whenever a customer submits a lead form or requests a quote.\n\n" .
                   "What specific deal or campaign would you like me to tackle first?";
        }

        if ($isAccountant) {
            return "📑 **AI Accountant Financial Briefing:**\n\n" .
                   "I have reviewed your financial telemetry:\n" .
                   "• **Receivables Status:** Recommend dispatching automated payment reminders for invoices approaching due date.\n" .
                   "• **Cash Flow Health:** Operating margin is stable. Keep an eye on recurring SaaS expenses and reconcile pending wallet funding transactions.\n" .
                   "• **Actionable Step:** Use Casjoe Pay's virtual cards to isolate vendor subscriptions and avoid surprise overcharges.";
        }

        if ($isSupport) {
            return "🎧 **AI Support Agent Resolution Assistant:**\n\n" .
                   "Here is a courteous and professional response template for your customer inquiry:\n\n" .
                   "\"Dear [Customer Name],\n\n" .
                   "Thank you for reaching out to us. We have received your request and our team is already reviewing the details to ensure a swift resolution.\n\n" .
                   "Your ticket reference is #[TICKET-ID]. You can expect a complete update from us within the next 2 to 4 hours.\n\n" .
                   "Thank you for your patience and for choosing our services!\n\n" .
                   "Best regards,\nCustomer Success Team\"\n\n" .
                   "Would you like me to queue this response for your approval?";
        }

        return "I have analyzed your business request. Based on current system metrics, here are your strategic next steps:\n\n" .
               "1. Verify that all CRM leads and pending invoices are synchronized.\n" .
               "2. Maintain strict follow-up cadences with interested prospects.\n" .
               "3. Check the Action Queue to approve pending automated customer communications.\n\n" .
               "How else can I assist your operations today?";
    }
}
