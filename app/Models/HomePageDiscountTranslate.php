<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageDiscountTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_page_discount_id',
        'language_id',
        'title',
        'subtitle',
        'button_link',
    ];

    protected $casts = [
        'home_page_discount_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the home page discount that owns this translation
     */
    public function homePageDiscount()
    {
        return $this->belongsTo(HomePageDiscount::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
