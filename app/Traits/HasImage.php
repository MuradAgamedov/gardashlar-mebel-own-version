<?php

namespace App\Traits;

use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

trait HasImage
{
    /**
     * Get the full URL for the image
     */
    public function getImageUrlAttribute()
    {
        $field = $this->imageField ?? 'image';
        $image = $this->getAttribute($field);

        if (!$image) {
            return null;
        }

        // Check if it's already a full URL
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        // Check if it's a storage path
        if (strpos($image, 'storage/') === 0) {
            return asset($image);
        }

        // Delegate to ImageService if exists
        if (class_exists(ImageService::class)) {
            try {
                return app(ImageService::class)->getUrl($image);
            } catch (\Exception $e) {
                // Fallback to public storage disk
            }
        }

        // Fallback to public storage disk
        return Storage::disk('public')->url($image);
    }
}
