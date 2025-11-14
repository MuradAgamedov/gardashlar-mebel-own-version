<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupAdTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'popup_ad_id',
        'locale',
        'title',
        'description',
        'image_alt_text',
    ];

    /**
     * Əsas popup ilə əlaqə
     */
    public function popupAd()
    {
        return $this->belongsTo(PopupAd::class, 'popup_ad_id');
    }
}
