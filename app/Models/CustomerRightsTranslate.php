<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRightsTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_rights_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'customer_rights_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the customer rights that owns this translation
     */
    public function customerRights()
    {
        return $this->belongsTo(CustomerRights::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
