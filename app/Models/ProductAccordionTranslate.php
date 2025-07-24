<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAccordionTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_accordion_id',
        'language_id',
        'title',
        'description'
    ];

    public function accordion(): BelongsTo
    {
        return $this->belongsTo(ProductAccordion::class, 'product_accordion_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
