<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupAdTranslate extends Model
{
    protected $fillable = [
        'popup_ad_id',
        'locale',
        'title',
        'description',
        'image_alt_text',
    ];

    public function popupAd()
    {
        return $this->belongsTo(PopupAd::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'locale', 'lang_code');
    }
}
