<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload an image file
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
                'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'generate_unique_name' => true,
            ];

            $options = array_merge($defaultOptions, $options);

            // Validate file
            if (!$this->validateFile($file, $options)) {
                throw new \Exception('File validation failed. Check file type and validity.');
            }

            // Generate filename
            $filename = $this->generateFilename($file, $options['generate_unique_name']);

            // Store file
            $filePath = $file->storeAs($path, $filename, $options['disk']);

            if ($filePath) {
                return $filePath;
            }

            throw new \Exception('File storage failed. File path is null.');

        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete an image file
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }
            return true; // File doesn't exist, consider it deleted
        } catch (\Exception $e) {
            \Log::error('Image delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an image (delete old, upload new)
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
            throw new \Exception('File is not valid. Error: ' . $file->getError());
        }

        // Check file type
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $options['allowed_types'])) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $options['allowed_types']) . '. Got: ' . $extension);
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
     * Get full URL for image
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


} 