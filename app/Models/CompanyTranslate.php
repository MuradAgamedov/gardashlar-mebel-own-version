<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'language_id',
        'title',
        'card_image',
        'card_image_alt_text',
        'inner_image',
        'inner_image_alt_text',
        'description',
        "slug"
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
