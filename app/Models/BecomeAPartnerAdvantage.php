<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAPartnerAdvantage extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
        'icon',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'icon_url',
    ];

    /**
     * Get all translations for this advantage
     */
    public function translates()
    {
        return $this->hasMany(BecomeAPartnerAdvantageTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active advantages
     */

    /**
     * Scope for ordered advantages
     */

    /**
     * Get icon URL
     */
    public function getIconUrlAttribute()
    {
        return $this->getIconUrl($this->icon);
    }

    /**
     * Helper method to get icon URL
     */
    private function getIconUrl($iconPath)
    {
        if (!$iconPath) {
            return null;
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($iconPath);
    }

    /**
     * Boot method for auto-incrementing order and file cleanup
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($advantage) {
            if (!$advantage->order) {
                $maxOrder = static::max('order');
                $advantage->order = $maxOrder + 1;
            }
        });

        // Delete associated files when advantage is deleted
        static::deleting(function ($advantage) {
            $imageService = app(ImageService::class);
            
            if ($advantage->icon) {
                $imageService->delete($advantage->icon);
            }
        });
    }
}
