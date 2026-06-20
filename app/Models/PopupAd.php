<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupAd extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'image',
        'order',
        'status',
    ];

    /**
     * Hər popup üçün çoxlu tərcümələr
     */
    public function translations()
    {
        return $this->hasMany(PopupAdTranslate::class);
    }
    public function translates()
    {
        return $this->hasMany(PopupAdTranslate::class);
    }
    /**
     * Aktiv dili avtomatik gətirmək üçün helper
     */
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

}
