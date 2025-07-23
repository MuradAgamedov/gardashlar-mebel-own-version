<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'language_id',
        'address',
    ];

    protected $casts = [
        'address_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the address that owns this translation
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
} 