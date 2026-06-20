<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
        'customer_image',
        'company_logo',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'customer_image_url',
        'company_logo_url',
    ];

    /**
     * Get all translations for this testimonial
     */
    public function translates()
    {
        return $this->hasMany(TestimonialTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active testimonials
     */

    /**
     * Scope for ordered testimonials
     */

    /**
     * Get customer image URL
     */
    public function getCustomerImageUrlAttribute()
    {
        return $this->getImageUrl($this->customer_image);
    }

    /**
     * Get company logo URL
     */
    public function getCompanyLogoUrlAttribute()
    {
        return $this->getImageUrl($this->company_logo);
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
        static::creating(function ($testimonial) {
            if (!$testimonial->order) {
                $maxOrder = static::max('order');
                $testimonial->order = $maxOrder + 1;
            }
        });

        // Delete associated files when testimonial is deleted
        static::deleting(function ($testimonial) {
            $imageService = app(ImageService::class);
            
            if ($testimonial->customer_image) {
                $imageService->delete($testimonial->customer_image);
            }
            
            if ($testimonial->company_logo) {
                $imageService->delete($testimonial->company_logo);
            }
        });
    }
}
