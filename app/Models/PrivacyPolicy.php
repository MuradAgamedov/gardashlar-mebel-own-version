<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivacyPolicy extends Model
{
    use HasFactory, App\Traits\Translatable;

    protected $fillable = [];

    public function translates(): HasMany
    {
        return $this->hasMany(PrivacyPolicyTranslate::class);
    }

} 