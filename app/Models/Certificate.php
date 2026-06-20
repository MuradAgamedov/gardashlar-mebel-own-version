<?php

namespace App\Models;

use App\Services\ImageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder, \App\Traits\HasImage;

    protected $fillable = [
        'order',
        'status',
        'image',
        'date',
    ];

    protected $casts = [
        'order' => 'integer',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Get all translations for this certificate
     */
    public function translates()
    {
        return $this->hasMany(CertificateTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Scope for active certificates
     */

    /**
     * Scope for ordered certificates
     */



    /**
     * Boot method for auto-incrementing order and file cleanup
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment order on creation
        static::creating(function ($certificate) {
            if (!$certificate->order) {
                $maxOrder = static::max('order');
                $certificate->order = $maxOrder + 1;
            }
        });

        // Delete associated files when certificate is deleted
        static::deleting(function ($certificate) {
            $imageService = app(ImageService::class);
            
            if ($certificate->image) {
                $imageService->delete($certificate->image);
            }
        });
    }
} 