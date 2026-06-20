<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoPage extends Model
{
    use App\Traits\Translatable, App\Traits\HasStatusAndOrder;
    protected $fillable = [
        'category_id',
        'status',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'status' => 'boolean',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function translates(): HasMany
    {
        return $this->hasMany(SeoPageTranslate::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

}
