<?php

namespace App\Core\Controllers;

class MCPController {
    
    public function handle() {
        // Ensure the response is treated as JSON API
        header('Content-Type: application/json');
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'OPTIONS') {
            // Handle CORS preflight if necessary
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(204);
            exit;
        }
        
        // Add CORS headers for actual requests
        header('Access-Control-Allow-Origin: *');
        
        if ($method === 'GET') {
            // For SSE (Server-Sent Events) or basic health check
            // Based on MCP standard, could be SSE endpoint or just capability info
            echo json_encode([
                "jsonrpc" => "2.0",
                "id" => null,
                "result" => [
                    "status" => "MCP Server is running",
                    "capabilities" => [
                        "tools" => [],
                        "resources" => [],
                        "prompts" => []
                    ]
                ]
            ]);
            exit;
        }
        
        if ($method === 'POST') {
            // Handle JSON-RPC 2.0 requests
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['jsonrpc'])) {
                http_response_code(400);
                echo json_encode([
                    "jsonrpc" => "2.0",
                    "error" => [
                        "code" => -32700,
                        "message" => "Parse error"
                    ],
                    "id" => null
                ]);
                exit;
            }
            
            // Basic valid JSON-RPC fallback response
            echo json_encode([
                "jsonrpc" => "2.0",
                "id" => $input['id'] ?? null,
                "result" => [
                    "message" => "Command received",
                    "handled" => true
                ]
            ]);
            exit;
        }
        
        // Method not allowed
        http_response_code(405);
        echo json_encode([
            "error" => "Method not allowed"
        ]);
        exit;
    }
}
