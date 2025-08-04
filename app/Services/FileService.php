<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload a file
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $options
     * @return string|false
     */
    public function upload(UploadedFile $file, string $path = 'uploads', array $options = [])
    {
        try {
            // Default options
            $defaultOptions = [
                'disk' => 'public',
                'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
                'generate_unique_name' => true,
                'max_size' => 10240, // 10MB default
            ];

            $options = array_merge($defaultOptions, $options);

            // Validate file
            if (!$this->validateFile($file, $options)) {
                throw new \Exception('File validation failed. Check file type and validity.');
            }

            // Check disk availability
            if (!Storage::disk($options['disk'])->exists($path)) {
                // Try to create directory
                try {
                    Storage::disk($options['disk'])->makeDirectory($path);
                } catch (\Exception $e) {
                    throw new \Exception('Unable to create upload directory. Check storage permissions.');
                }
            }

            // Generate filename
            $filename = $this->generateFilename($file, $options['generate_unique_name']);

            // Store file
            $filePath = $file->storeAs($path, $filename, $options['disk']);

            if ($filePath) {
                // Verify file was actually stored
                if (!Storage::disk($options['disk'])->exists($filePath)) {
                    throw new \Exception('File was not properly stored. Check storage permissions.');
                }
                
                return $filePath;
            }

            throw new \Exception('File storage failed. File path is null.');

        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'path' => $path,
                'options' => $options
            ]);
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                $result = Storage::disk($disk)->delete($path);
                if (!$result) {
                    \Log::warning('Failed to delete file: ' . $path);
                }
                return $result;
            }
            return true; // File doesn't exist, consider it deleted
        } catch (\Exception $e) {
            \Log::error('File delete failed: ' . $e->getMessage(), [
                'path' => $path,
                'disk' => $disk
            ]);
            return false;
        }
    }

    /**
     * Update a file (delete old, upload new)
     *
     * @param UploadedFile $file
     * @param string|null $oldPath
     * @param string $path
     * @param array $options
     * @return string|false
     */
    public function update(UploadedFile $file, ?string $oldPath = null, string $path = 'uploads', array $options = [])
    {
        // Delete old file if exists
        if ($oldPath) {
            $this->delete($oldPath, $options['disk'] ?? 'public');
        }

        // Upload new file
        return $this->upload($file, $path, $options);
    }

    /**
     * Validate uploaded file
     *
     * @param UploadedFile $file
     * @param array $options
     * @return bool
     */
    private function validateFile(UploadedFile $file, array $options): bool
    {
        // Check if file is valid
        if (!$file->isValid()) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            ];
            
            $errorMessage = $errorMessages[$file->getError()] ?? 'Unknown upload error occurred.';
            throw new \Exception('File is not valid. Error: ' . $errorMessage);
        }

        // Check file size
        if (isset($options['max_size']) && $file->getSize() > ($options['max_size'] * 1024)) {
            throw new \Exception('File size exceeds maximum allowed size of ' . $this->formatBytes($options['max_size'] * 1024));
        }

        // Check file type by extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $options['allowed_types'])) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $options['allowed_types']) . '. Got: ' . $extension);
        }

        // Check MIME type for PDF
        $mimeType = $file->getMimeType();
        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ];
        
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new \Exception('Invalid MIME type. Allowed types: ' . implode(', ', $allowedMimeTypes) . '. Got: ' . $mimeType);
        }

        return true;
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @param bool $unique
     * @return string
     */
    private function generateFilename(UploadedFile $file, bool $unique = true): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        if ($unique) {
            $filename = Str::slug($originalName) . '_' . time() . '_' . Str::random(8) . '.' . $extension;
        } else {
            $filename = Str::slug($originalName) . '.' . $extension;
        }

        return $filename;
    }

    /**
     * Get full URL for file
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function getUrl(string $path, string $disk = 'public'): string
    {
        if (empty($path)) {
            return '';
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Check if file exists
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
} 