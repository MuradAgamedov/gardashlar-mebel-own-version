<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kiv extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
        'image',
        'video_link',
        'kiv_category_id',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
        'kiv_category_id' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Get the category that owns this KIV
     */
    public function category()
    {
        return $this->belongsTo(KivCategory::class, 'kiv_category_id');
    }

    /**
     * Scope for active KIVs
     */

    /**
     * Scope for ordered KIVs
     */

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getImageUrl($this->image);
    }

    /**
     * Helper method to get image URL
     */
    private function getImageUrl($imagePath)
    {
        if (!$imagePath) {
            return null;
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($imagePath);
    }

    /**
     * Boot method for auto-incrementing order and file cleanup
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($kiv) {
            if (!$kiv->order) {
                $maxOrder = static::max('order');
                $kiv->order = $maxOrder + 1;
            }
        });

        // Delete associated files when KIV is deleted
        static::deleting(function ($kiv) {
            $imageService = app(ImageService::class);
            
            if ($kiv->image) {
                $imageService->delete($kiv->image);
            }
        });
    }
} 