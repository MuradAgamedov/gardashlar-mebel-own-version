<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Şəkli yüklə və həmişə WebP kimi saxla
     */
    public function upload(UploadedFile $file, string $path = 'uploads', array $options = [])
    {
        try {
            $defaultOptions = [
                'disk' => 'public',
                'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'generate_unique_name' => true,
            ];
            $options = array_merge($defaultOptions, $options);

            if (!$this->validateFile($file, $options)) {
                throw new \Exception('File validation failed. Check file type and validity.');
            }

            // Qovluğu yoxla/yara
            if (!Storage::disk($options['disk'])->exists($path)) {
                Storage::disk($options['disk'])->makeDirectory($path);
            }

            // Fayl adı (.webp uzantısı ilə)
            $webpName = $this->generateFilenameWithExtension($file, 'webp', $options['generate_unique_name']);
            $relativePath = trim($path, '/') . '/' . $webpName;

            // Orijinal faylı müvəqqəti yolundan oxu və WebP-ə çevir
            $webpBinary = $this->convertToWebp($file);

            if ($webpBinary === null) {
                // WebP uğursuz olarsa, orijinal formatda saxla
                \Log::warning('WebP conversion failed, saving in original format', [
                    'file' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType()
                ]);

                $originalExtension = strtolower($file->getClientOriginalExtension());
                $originalName = $this->generateFilenameWithExtension($file, $originalExtension, $options['generate_unique_name']);
                $relativePath = trim($path, '/') . '/' . $originalName;

                $saved = Storage::disk($options['disk'])->putFileAs($path, $file, $originalName, 'public');
                if (!$saved) {
                    throw new \Exception('Failed to save original image file.');
                }

                return $relativePath;
            }

            // Storage-a yaz
            $saved = Storage::disk($options['disk'])->put($relativePath, $webpBinary, 'public');
            if (!$saved) {
                throw new \Exception('Unable to save WebP file to storage.');
            }

            // Yekun yoxlama
            if (!Storage::disk($options['disk'])->exists($relativePath)) {
                throw new \Exception('File was not properly stored. Check storage permissions.');
            }

            return $relativePath;

        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    public function delete(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                $result = Storage::disk($disk)->delete($path);
                if (!$result) {
                    \Log::warning('Failed to delete image file: ' . $path);
                }
                return $result;
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Image delete failed: ' . $e->getMessage(), [
                'path' => $path,
                'disk' => $disk
            ]);
            return false;
        }
    }

    public function update(UploadedFile $file, ?string $oldPath = null, string $path = 'uploads', array $options = [])
    {
        if ($oldPath) {
            $this->delete($oldPath, $options['disk'] ?? 'public');
        }
        return $this->upload($file, $path, $options);
    }

    private function validateFile(UploadedFile $file, array $options): bool
    {
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

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $options['allowed_types'])) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $options['allowed_types']) . '. Got: ' . $extension);
        }

        $mimeType = $file->getMimeType();
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new \Exception('Invalid MIME type. Allowed types: ' . implode(', ', $allowedMimeTypes) . '. Got: ' . $mimeType);
        }

        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw new \Exception('Unable to read image file. File may be corrupted.');
        }

        return true;
    }

    /**
     * GD ilə WebP-ə çevrilmə (keyfiyyət dərəcəsi olmadan)
     */
    private function convertToWebp(UploadedFile $file): ?string
    {
        if (!function_exists('imagewebp')) {
            throw new \Exception('PHP-GD WebP support is not available.');
        }

        $mime = $file->getMimeType();
        $srcPath = $file->getPathname();

        // Şəkil resursu yarat
        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($srcPath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($srcPath);
                if ($image !== false) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($srcPath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($srcPath);
                break;
            default:
                $image = null;
        }

        if (!$image) {
            \Log::error('Image resource creation failed', ['mime' => $mime, 'path' => $srcPath]);
            return null;
        }

        // Keyfiyyətlə bağlı heç bir parametr yoxdur (default WebP)
        ob_start();
        $ok = imagewebp($image);
        imagedestroy($image);

        if (!$ok) {
            ob_end_clean();
            \Log::error('WebP encoding failed', ['mime' => $mime]);
            return null;
        }

        $data = ob_get_clean();
        return $data === false ? null : $data;
    }

    private function generateFilenameWithExtension(UploadedFile $file, string $forceExtension, bool $unique = true): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if ($unique) {
            $randomNumber = mt_rand(1000, 9999);
            $timestamp = time();
            return Str::slug($originalName) . '_' . $timestamp . '_' . $randomNumber . '.' . $forceExtension;
        }
        return Str::slug($originalName) . '.' . $forceExtension;
    }

    public function getUrl(string $path, string $disk = 'public'): string
    {
        if (empty($path)) {
            return '';
        }
        return Storage::disk($disk)->url($path);
    }

    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
