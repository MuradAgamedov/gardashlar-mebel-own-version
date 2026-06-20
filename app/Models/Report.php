<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
        'pdf',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = [
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

    /**
     * Scope for active reports
     */

    /**
     * Scope for ordered reports
     */

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
            if ($report->pdf) {
                \Storage::disk('public')->delete($report->pdf);
            }
        });
    }
}
