<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAPartner extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $table = 'become_a_partners';

    protected $fillable = [
        'content'
    ];

    /**
     * Get the translations for this partner
     */
    public function translates()
    {
        return $this->hasMany(BecomeAPartnerTranslate::class);
    }

    /**
     * Scope for ordering
     */

    /**
     * Get first record or create if not exists
     */
    public static function getFirstOrCreate()
    {
        $record = static::first();
        
        if (!$record) {
            $record = static::create();
        }
        
        return $record;
    }
}
