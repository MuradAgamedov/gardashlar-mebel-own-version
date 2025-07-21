<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KivCategoryTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'kiv_category_id',
        'language_id',
        'title',
        'subtitle',
    ];

    protected $casts = [
        'kiv_category_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the category that owns this translation
     */
    public function category()
    {
        return $this->belongsTo(KivCategory::class, 'kiv_category_id');
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 