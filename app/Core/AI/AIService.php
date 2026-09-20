<?php
namespace App\Core\AI;

use App\Core\AI\Providers\ProviderInterface;
use App\Core\AI\Providers\HuggingFaceProvider;
use App\Core\AI\Providers\OllamaProvider;
use App\Core\AI\Providers\ReplicateProvider;
use App\Core\AI\Providers\AzureProvider;
use App\Core\AI\Providers\TensorFlowProvider;
use App\Core\AI\Providers\OpenAIProvider;
use App\Core\AI\Providers\GeminiProvider;
use App\Core\AI\Providers\GroqProvider;
use App\Core\AI\Providers\MuseGlimmerProvider;
use App\Core\Database;
use App\Core\TenantContext;

/**
 * CreditExhaustedException
 * Thrown when a tenant has no AI tokens remaining.
 * Callers should catch this and show a graceful manual-mode fallback.
 */
class CreditExhaustedException extends \RuntimeException {}

class AIService
{
    protected ProviderInterface $provider;
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadProvider();
    }

    protected function loadProvider(): void {
        $providerName = $this->getSetting("ai_provider", "openai");
        
        switch ($providerName) {
            case "groq":
                $key = $this->getSetting("ai_groq_key", "");
                $model = $this->getSetting("ai_groq_model", "llama-3.3-70b-versatile");
                $this->provider = new GroqProvider($key, $model);
                break;
            case "openai":
                $key = $this->getSetting("ai_openai_key", "") ?: $this->getSetting("openai_api_key", "");
                $model = $this->getSetting("ai_openai_model", "gpt-4");
                $this->provider = new OpenAIProvider($key, $model);
                break;
            case "gemini":
                $key = $this->getSetting("ai_gemini_key", getenv('GEMINI_API_KEY') ?: "");
                $model = $this->getSetting("ai_gemini_model", "gemini-pro");
                $this->provider = new GeminiProvider($key, $model);
                break;
            case "ollama":
                $url = $this->getSetting("ai_ollama_url", "http://localhost:11434");
                $model = $this->getSetting("ai_ollama_model", "llama2");
                $this->provider = new OllamaProvider($url, $model);
                break;
            case "replicate":
                $key = $this->getSetting("ai_replicate_key", "");
                $this->provider = new ReplicateProvider($key, "");
                break;
            case "azure":
                $this->provider = new AzureProvider("", "", "");
                break;
            case "tensorflow":
                $this->provider = new TensorFlowProvider("");
                break;
            case "muse_glimmer":
                $key   = $this->getSetting("ai_muse_glimmer_key", "");
                $model = $this->getSetting("ai_muse_glimmer_model", "meta/muse-glimmer-30b");
                $this->provider = new MuseGlimmerProvider($key, $model);
                break;
            case "huggingface":
            default:
                $dbKey = $this->getSetting("ai_huggingface_key", "");
                // Fallback to the known working key from GeminiService if DB is empty
                $apiKey = !empty($dbKey) ? $dbKey : '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';
                
                // Fallback Model: Qwen 2.5 72B Instruct (Active Chat model on HuggingFace Router)
                $dbModel = $this->getSetting("ai_huggingface_model", "Qwen/Qwen2.5-72B-Instruct");
                $model = (empty($dbModel) || stripos($dbModel, 'flan-t5') !== false) ? 'Qwen/Qwen2.5-72B-Instruct' : $dbModel;
                $this->provider = new HuggingFaceProvider($apiKey, $model);
                break;
        }
    }

    protected function getSetting(string $key, string $default = ""): string {
        try {
            $sql = "SELECT setting_value FROM system_settings WHERE setting_key = :key LIMIT 1";
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute(["key" => $key]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result["setting_value"] : $default;
        } catch (\Exception $e) { return $default; }
    }
    /**
     * Generate text with automatic AI credit gate.
     *
     * @param  string $prompt
     * @param  array  $config   Optional provider config
     * @param  string $action   Action label for audit log  (default: 'generate')
     * @param  int    $userId   User triggering the call    (0 = system/cron)
     * @throws CreditExhaustedException when tenant has no tokens
     */
    public function generate(string $prompt, array $config = [], string $action = 'generate', int $userId = 0): string
    {
        $tenantId = TenantContext::getTenantId();

        // Estimate token usage: ~1 token per 4 chars is a standard approximation
        $estimatedTokens = max(50, (int)ceil(strlen($prompt) / 4) + 50);

        $credits = new AICreditService();

        if (!$credits->consume($tenantId, $userId, $action, $estimatedTokens, strlen($prompt))) {
            throw new CreditExhaustedException(
                'AI credits exhausted. Please top up your AI credit balance to continue using AI features.'
            );
        }

        // Try primary provider
        $result = $this->provider->generateText($prompt, $config);

        // If primary provider returned an error, try fallback providers gracefully
        if (stripos(trim($result), 'Error:') === 0) {
            $errorLog = [$this->provider->getProviderName() => $result];

            // 1. Try Groq Provider fallback if not already primary
            if ($this->provider->getProviderName() !== 'groq') {
                try {
                    $groqKey = $this->getSetting("ai_groq_key", "");
                    if (!empty($groqKey)) {
                        $fallbackGroq = new GroqProvider($groqKey, $this->getSetting("ai_groq_model", "llama-3.3-70b-versatile"));
                        $fallbackRes = $fallbackGroq->generateText($prompt, $config);
                        if (stripos(trim($fallbackRes), 'Error:') !== 0) {
                            return $fallbackRes;
                        }
                        $errorLog['groq'] = $fallbackRes;
                    }
                } catch (\Exception $e) {}
            }

            // 2. Try Gemini Provider fallback if not already primary
            if ($this->provider->getProviderName() !== 'gemini') {
                try {
                    $geminiKey = $this->getSetting("ai_gemini_key", getenv('GEMINI_API_KEY') ?: (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : ''));
                    if (!empty($geminiKey) && $geminiKey !== 'YOUR_API_KEY') {
                        $fallbackGemini = new GeminiProvider($geminiKey, $this->getSetting("ai_gemini_model", "gemini-pro"));
                        $fallbackRes = $fallbackGemini->generateText($prompt, $config);
                        if (stripos(trim($fallbackRes), 'Error:') !== 0) {
                            return $fallbackRes;
                        }
                        $errorLog['gemini'] = $fallbackRes;
                    }
                } catch (\Exception $e) {}
            }

            // 2. Try OpenAI Provider fallback if not already primary
            if ($this->provider->getProviderName() !== 'openai') {
                try {
                    $openAiKey = $this->getSetting("ai_openai_key", "") ?: $this->getSetting("openai_api_key", "");
                    if (!empty($openAiKey)) {
                        $fallbackOpenAi = new OpenAIProvider($openAiKey, $this->getSetting("ai_openai_model", "gpt-4o-mini"));
                        $fallbackRes = $fallbackOpenAi->generateText($prompt, $config);
                        if (stripos(trim($fallbackRes), 'Error:') !== 0) {
                            return $fallbackRes;
                        }
                        $errorLog['openai'] = $fallbackRes;
                    }
                } catch (\Exception $e) {}
            }

            // If all providers failed, throw combined error
            throw new \RuntimeException($result);
        }

        return $result;
    }

    public function analyze(string $text, string $instruction): array { return $this->provider->analyzeText($text, $instruction); }
}
