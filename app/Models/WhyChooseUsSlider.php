<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhyChooseUsSlider extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder, \App\Traits\HasImage;

    protected $fillable = [
        'order',
        'status',
        'image',
        'video_link',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Scope for active sliders
     */

    /**
     * Scope for ordering by order field
     */



    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($slider) {
            if (!isset($slider->order)) {
                $maxOrder = static::max('order');
                $slider->order = $maxOrder + 1;
            }
        });

        static::deleting(function ($slider) {
            $imageService = app(ImageService::class);
            
            // Delete image file
            if ($slider->image) {
                $imageService->delete($slider->image);
            }
        });
    }
}
