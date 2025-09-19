<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Şəkli yüklə və həmişə WebP kimi saxla
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function upload(UploadedFile $file, string $path = 'uploads', array $options = [])
    {
        try {
            $defaultOptions = [
                'disk' => 'public',
                'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'generate_unique_name' => true,
                'quality' => 85, // WebP keyfiyyəti (0-100)
                'max_width' => null,  // istəsən ölçü limitləri də qoya bilərsən
                'max_height' => null,
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
            $relativePath = trim($path, '/').'/'.$webpName;

            // Orijinal faylı müvəqqəti yolundan oxu və WebP-ə çevir
            $webpBinary = $this->convertToWebp($file, $options['quality'], $options['max_width'], $options['max_height']);

            if ($webpBinary === null) {
                throw new \Exception('WebP conversion failed.');
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
        $allowedMimeTypes = ['image/jpeg','image/png','image/gif','image/webp'];
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
     * GD ilə WebP-ə çevrilmə (şəffaflıq, ölçü limitləri və s.)
     */
    private function convertToWebp(UploadedFile $file, int $quality = 85, ?int $maxWidth = null, ?int $maxHeight = null): ?string
    {
        // GD tələb olunur
        if (!function_exists('imagewebp')) {
            throw new \Exception('GD WebP support is not available. Enable PHP-GD with WebP.');
        }

        $mime = $file->getMimeType();
        $srcPath = $file->getPathname();

        // Orijinaldan image resource yarat
        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($srcPath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($srcPath);
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($srcPath);
                break;
            case 'image/webp':
                // Artıq webp-dirsə də yenidən oxuyub yazaq (keyfiyyət normalizasiya)
                $image = @imagecreatefromwebp($srcPath);
                break;
            default:
                $image = null;
        }

        if (!$image) {
            return null;
        }

        // İstəyə görə ölçünü limitlə (proportionally)
        $origW = imagesx($image);
        $origH = imagesy($image);

        $targetW = $origW;
        $targetH = $origH;

        if ($maxWidth || $maxHeight) {
            $ratio = $origW / $origH;

            if ($maxWidth && $targetW > $maxWidth) {
                $targetW = $maxWidth;
                $targetH = (int) round($maxWidth / $ratio);
            }

            if ($maxHeight && $targetH > $maxHeight) {
                $targetH = $maxHeight;
                $targetW = (int) round($maxHeight * $ratio);
            }
        }

        if ($targetW !== $origW || $targetH !== $origH) {
            $resized = imagecreatetruecolor($targetW, $targetH);

            // PNG/GIF şəffaflıq üçün alpha ayarları
            if (in_array($mime, ['image/png','image/gif'])) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefilledrectangle($resized, 0, 0, $targetW, $targetH, $transparent);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);
            imagedestroy($image);
            $image = $resized;
        }

        // Şəffaflıq üçün alpha saxla (PNG/GIF mənbələrində)
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Output-u buffer-ə yaz
        ob_start();
        $ok = imagewebp($image, null, max(0, min(100, $quality)));
        imagedestroy($image);

        if (!$ok) {
            ob_end_clean();
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
