<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressTypeTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_type_id',
        'language_id',
        'title',
    ];

    protected $casts = [
        'address_type_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the address type that owns this translation
     */
    public function addressType()
    {
        return $this->belongsTo(AddressType::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 