<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSlider extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder, \App\Traits\HasImage;

    protected $fillable = [
        'order',
        'status',
        'image',
        'link',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Get all translations for this slider
     */
    public function translates()
    {
        return $this->hasMany(PartnerSliderTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active sliders
     */

    /**
     * Scope for ordered sliders
     */



    /**
     * Boot method for auto-incrementing order and file cleanup
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($slider) {
            if (!$slider->order) {
                $maxOrder = static::max('order');
                $slider->order = $maxOrder + 1;
            }
        });

        // Delete associated files when slider is deleted
        static::deleting(function ($slider) {
            $imageService = app(ImageService::class);
            
            if ($slider->image) {
                $imageService->delete($slider->image);
            }
        });
    }
}
