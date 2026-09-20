<?php
namespace App\Core\AI\Providers;
class TensorFlowProvider implements ProviderInterface {
    protected $ep;
    public function __construct($ep) { $this->ep=$ep; }
    public function getProviderName(): string { return "tensorflow"; }
    public function generateText(string $p, array $c=[]): string { return "TF Pending"; }
    public function analyzeText(string $t, string $i): array { return []; }
}
