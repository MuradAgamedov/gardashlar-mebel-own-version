<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'status'
    ];

    protected $primaryKey = 'id';

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($company) {
            if (!isset($company->order)) {
                $maxOrder = static::max('order');
                $company->order = $maxOrder + 1;
            }
        });
    }

    public function translates(): HasMany
    {
        return $this->hasMany(CompanyTranslate::class);
    }

    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = \App\Models\Language::getMainLanguage()->id;
        }
        
        return $this->translates()->where('language_id', $languageId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function companyProducts()
    {
        return $this->hasMany(CompanyProduct::class);
    }
}
