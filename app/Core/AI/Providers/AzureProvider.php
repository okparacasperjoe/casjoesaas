<?php
namespace App\Core\AI\Providers;
class AzureProvider implements ProviderInterface {
    protected $ep; protected $key; protected $dep;
    public function __construct($ep, $key, $dep) { $this->ep=$ep; $this->key=$key; $this->dep=$dep; }
    public function getProviderName(): string { return "azure"; }
    public function generateText(string $p, array $c=[]): string { return "Azure Pending"; }
    public function analyzeText(string $t, string $i): array { return []; }
}
