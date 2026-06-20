<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'order',
        'status',
        'discount_percent'
    ];

    protected $primaryKey = 'id';

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
        'discount_percent' => 'decimal:2'
    ];

    public function translates(): HasMany
    {
        return $this->hasMany(CompanyTranslate::class);
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
