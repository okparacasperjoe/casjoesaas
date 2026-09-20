<?php

namespace App\Core\Services;

use Exception;

class WebResearchService
{
    private $aiService;

    public function __construct($tenantId)
    {
        $this->aiService = new AIService($tenantId);
    }

    /**
     * Researches a company website and returns an AI-generated profile.
     */
    public function researchCompany($url, $companyName)
    {
        if (empty($url)) {
            throw new Exception("URL is required for web research.");
        }

        // Ensure URL has http/https
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }

        // 1. Fetch Website Content
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n",
                'timeout' => 10
            ]
        ]);

        $html = @file_get_contents($url, false, $context);
        
        if (!$html) {
            throw new Exception("Could not access or read the website: $url");
        }

        // 2. Extract meaningful text (Strip script/style tags)
        $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
        $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $text);
        $text = strip_tags($text);
        
        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Limit to ~3000 words to avoid token limits
        $text = substr(trim($text), 0, 15000); 

        // 3. AI Analysis
        $prompt = "You are an expert Sales Researcher. Read the following text scraped from the website of a company named '{$companyName}'. 
        
        Generate a structured 'Research Brief' formatted exactly like this:
        
        ### Company Overview
        [2-3 sentences explaining what they do]
        
        ### Potential Pain Points
        - [Point 1 based on their industry or offerings]
        - [Point 2]
        
        ### Sales Strategy
        [1 paragraph on how to pitch to them]
        
        Website Content:
        " . $text;

        return $this->aiService->generateText($prompt);
    }
}
