<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStaticDataTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_static_data_id',
        'language_id',
        'title',
        'description',
        'card_1_image_title',
        'card_1_image_alt_text',
        'card_2_image_title',
        'card_2_image_alt_text',
        'card_3_image_title',
        'card_3_image_alt_text',
    ];

    protected $casts = [
        'service_static_data_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the service static data that owns this translation
     */
    public function serviceStaticData()
    {
        return $this->belongsTo(ServiceStaticData::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
