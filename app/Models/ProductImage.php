<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_color_id',
        'image',
        'order',
        'is_main',
        'status'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_main' => 'boolean',
        'status' => 'boolean'
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Scope for active images
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordering by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope for main image
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->getImageUrl($this->image);
    }

    /**
     * Get image URL helper method
     */
    private function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($imagePath);
    }

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Product color relationship
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    /**
     * Translations relationship
     */
    public function translates(): HasMany
    {
        return $this->hasMany(ProductImageTranslate::class);
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (!isset($image->order)) {
                $maxOrder = static::where('product_id', $image->product_id)->max('order');
                $image->order = $maxOrder + 1;
            }
        });

        static::deleting(function ($image) {
            $imageService = app(ImageService::class);
            
            // Delete image file
            if ($image->image) {
                $imageService->delete($image->image);
            }
        });
    }
}
