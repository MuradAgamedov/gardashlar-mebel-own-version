<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceAdvantage extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'service_id',
        'icon',
        'status',
        'order'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected $appends = [
        'icon_url',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($advantage) {
            if (!isset($advantage->order)) {
                $maxOrder = static::where('service_id', $advantage->service_id)->max('order');
                $advantage->order = $maxOrder + 1;
            }
        });

        static::deleting(function ($advantage) {
            $imageService = app(ImageService::class);
            
            // Delete icon file
            if ($advantage->icon) {
                $imageService->delete($advantage->icon);
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function translates(): HasMany
    {
        return $this->hasMany(ServiceAdvantageTranslate::class);
    }

    public function getIconUrlAttribute()
    {
        return $this->getIconUrl($this->icon);
    }

    private function getIconUrl($iconPath)
    {
        if (empty($iconPath)) {
            return '';
        }

        $imageService = app(ImageService::class);
        return $imageService->getUrl($iconPath);
    }
} 