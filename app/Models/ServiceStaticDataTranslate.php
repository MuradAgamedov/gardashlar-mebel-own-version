<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceStaticDataTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_static_data_id',
        'language_id',
        'title',
        'description',
        'card_1_title',
        'card_1_image_alt_text',
        'card_2_title',
        'card_2_image_alt_text',
        'card_3_title',
        'card_3_image_alt_text'
    ];

    protected $casts = [
        'service_static_data_id' => 'integer',
        'language_id' => 'integer'
    ];

    public function serviceStaticData(): BelongsTo
    {
        return $this->belongsTo(ServiceStaticData::class, 'service_static_data_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
