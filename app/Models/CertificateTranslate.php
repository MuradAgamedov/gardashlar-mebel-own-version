<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'language_id',
        'image_alt_text',
        'title',
    ];

    protected $casts = [
        'certificate_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the certificate that owns this translation
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 