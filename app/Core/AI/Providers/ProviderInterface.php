<?php
namespace App\Core\AI\Providers;
interface ProviderInterface {
    public function generateText(string $prompt, array $config = []): string;
    public function analyzeText(string $text, string $instruction): array;
    public function getProviderName(): string;
}
