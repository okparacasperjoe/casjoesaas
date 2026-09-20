<?php
namespace App\Core\AI\Providers;
class ReplicateProvider implements ProviderInterface {
    protected $key; protected $model;
    public function __construct($key, $model) { $this->key=$key; $this->model=$model; }
    public function getProviderName(): string { return "replicate"; }
    public function generateText(string $p, array $c=[]): string { return "Replicate implementation pending"; }
    public function analyzeText(string $t, string $i): array { return []; }
}
