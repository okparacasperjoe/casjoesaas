<?php

namespace App\Modules\CasjoeCloud\Controllers;

class AssetController
{
    public function serve($params)
    {
        $tenantId = $params['tenant_id'];
        $filename = $params['filename'];

        // Basic Security: prevent directory traversal
        if (preg_match('/[^a-zA-Z0-9._-]/', $filename) || !is_numeric($tenantId)) {
            http_response_code(400);
            die("Invalid Request");
        }

        // Check Existence across all possible root structures (due to /app/app vs /app hosting paths)
        $possiblePaths = [
            __DIR__ . '/../../../../storage/tenants/' . $tenantId . '/cloud/' . $filename,
            __DIR__ . '/../../../../../storage/tenants/' . $tenantId . '/cloud/' . $filename,
            __DIR__ . '/../../../../../../storage/tenants/' . $tenantId . '/cloud/' . $filename,
            $_SERVER['DOCUMENT_ROOT'] . '/../storage/tenants/' . $tenantId . '/cloud/' . $filename,
            $_SERVER['DOCUMENT_ROOT'] . '/../../storage/tenants/' . $tenantId . '/cloud/' . $filename,
            $_SERVER['DOCUMENT_ROOT'] . '/../../../storage/tenants/' . $tenantId . '/cloud/' . $filename,
            $_SERVER['DOCUMENT_ROOT'] . '/../app/storage/tenants/' . $tenantId . '/cloud/' . $filename,
            $_SERVER['DOCUMENT_ROOT'] . '/../../app/storage/tenants/' . $tenantId . '/cloud/' . $filename,
            '/home/sites/40a/2/20d0736ce2/storage/tenants/' . $tenantId . '/cloud/' . $filename,
            '/home/sites/40a/2/20d0736ce2/app/storage/tenants/' . $tenantId . '/cloud/' . $filename,
            '/home/sites/40a/2/20d0736ce2/app/app/storage/tenants/' . $tenantId . '/cloud/' . $filename
        ];

        $path = null;
        foreach ($possiblePaths as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }

        if (!$path) {
            http_response_code(404);
            die("File not found");
        }

        // Serve File
        $mime = @mime_content_type($path) ?: 'application/octet-stream';
        header("Content-Type: $mime");
        header("Content-Length: " . filesize($path));
        
        // Cache Headers (Optional but good for images)
        header("Cache-Control: public, max-age=86400");
        
        @ob_clean();
        readfile($path);
        exit;
    }
}
