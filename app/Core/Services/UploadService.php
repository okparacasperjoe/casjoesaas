<?php

namespace App\Core\Services;

class UploadService
{
    /**
     * Upload a file to a specified directory.
     *
     * @param array $file The $_FILES['input_name'] array.
     * @param string $directory Sub-directory inside public/uploads/
     * @param array $allowedExtensions Array of allowed extensions (lowercase).
     * @return string The public URL path to the uploaded file.
     * @throws \Exception If upload fails or validation error.
     */
    public function upload($file, $directory = 'misc', $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4'])
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \Exception('Invalid file parameter.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \Exception('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \Exception('Exceeded filesize limit.');
            default:
                throw new \Exception('Unknown errors.');
        }

        // 1. Validate Extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            throw new \Exception('Invalid file format. Allowed: ' . implode(', ', $allowedExtensions));
        }

        // 2. Prepare Directory
        // __DIR__ = app/Core/Services, so go 3 levels up to reach project root
        $uploadRoot = __DIR__ . '/../../../public/uploads/' . $directory;
        
        if (!is_dir($uploadRoot)) {
            if (!mkdir($uploadRoot, 0755, true)) {
                throw new \Exception('Failed to create upload directory.');
            }
        }

        // 3. Generate Secure Name
        $filename = uniqid('file_', true) . '.' . $ext;
        $destination = $uploadRoot . '/' . $filename;

        // 4. Move File
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Failed to move uploaded file.');
        }

        // 5. Return Public URL
        return '/uploads/' . $directory . '/' . $filename;
    }
}
