<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupAdTranslate extends Model
{
    protected $table = 'popup_ad_translates';

    protected $fillable = [
        'popup_ad_id',
        'language_id',
        'locale',
        'title',
        'description',
        'image_alt_text',
        'link',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function popup()
    {
        return $this->belongsTo(PopupAd::class, 'popup_ad_id');
    }
}
