<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'status',
        'order',
        'slug'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    public function translates(): HasMany
    {
        return $this->hasMany(ServiceTranslate::class);
    }

    public function advantages(): HasMany
    {
        return $this->hasMany(ServiceAdvantage::class);
    }

} 