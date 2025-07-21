<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status',
        'image',
        'pdf',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'pdf_url',
    ];

    /**
     * Get all translations for this report
     */
    public function translates()
    {
        return $this->hasMany(ReportTranslate::class);
    }

    /**
     * Get translation for specific language
     */
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

    /**
     * Scope for active reports
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered reports
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get image URL attribute
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return app(ImageService::class)->getUrl($this->image);
        }
        return null;
    }

    /**
     * Get PDF URL attribute
     */
    public function getPdfUrlAttribute()
    {
        if ($this->pdf) {
            return asset('storage/' . $this->pdf);
        }
        return null;
    }

    /**
     * Boot method for auto-incrementing order and file deletion
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($report) {
            if (!$report->order) {
                $maxOrder = static::max('order');
                $report->order = $maxOrder + 1;
            }
        });

        // Delete associated files on deletion
        static::deleting(function ($report) {
            if ($report->image) {
                app(ImageService::class)->delete($report->image);
            }
            if ($report->pdf) {
                \Storage::disk('public')->delete($report->pdf);
            }
        });
    }
}
