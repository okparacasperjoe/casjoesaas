<?php

namespace App\Core\Services\AI;

use App\Core\Services\AIService;
use App\Core\Services\EventBus;

abstract class AIAgent
{
    protected $aiService;
    protected $name;

    public function __construct()
    {
        $this->aiService = new AIService();
    }

    abstract protected function getSystemPrompt(): string;
    
    /**
     * In a fully autonomous setup, this would return JSON schema for tools.
     * For now, it returns a list of capabilities the prompt should know about.
     */
    protected function getTools(): array 
    {
        return [];
    }

    public function processMessage(int $tenantId, string $message): string
    {
        // 1. Gather Context (RAG from Event Bus)
        $recentEvents = EventBus::getRecentEvents($tenantId, 15);
        $context = "--- SYSTEM CONTEXT: RECENT BUSINESS EVENTS ---\n";
        if (empty($recentEvents)) {
            $context .= "No recent events found.\n";
        } else {
            foreach ($recentEvents as $event) {
                $context .= "[{$event['created_at']}] [{$event['module']}] {$event['event_type']}: " . $event['payload'] . "\n";
            }
        }
        $context .= "----------------------------------------------\n";

        // 2. Build the full system prompt
        $fullSystemPrompt = $this->getSystemPrompt() . "\n\n" . $context;

        // 3. Call the LLM
        $response = $this->aiService->generateText($message, $fullSystemPrompt);

        // 4. Intercept Actions
        if (preg_match('/\[ACTION:\s*([A-Z_]+)\s*\|\s*({.*?})\s*\]/s', $response, $matches)) {
            $action = $matches[1];
            $payload = json_decode($matches[2], true);
            
            // Execute action
            $actionResult = $this->executeAction($tenantId, $action, $payload);
            
            // Remove the action block from the response and append the result
            $response = preg_replace('/\[ACTION:\s*[A-Z_]+\s*\|\s*{.*?}\s*\]/s', '', $response);
            $response = trim($response) . "\n\n*(System Note: " . $actionResult . ")*";
        }

        return $response;
    }

    protected function executeAction(int $tenantId, string $action, ?array $payload): string
    {
        // Override this in subclasses to handle specific actions
        return "Action {$action} intercepted, but no handler was found.";
    }
}
