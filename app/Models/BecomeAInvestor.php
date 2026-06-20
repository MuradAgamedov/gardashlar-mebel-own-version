<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BecomeAInvestor extends Model
{
    use HasFactory, App\Traits\Translatable;

    protected $table = 'become_a_investors';
    protected $fillable = []; // No direct fields on this table

    /**
     * Get all translations for this investor
     */
    public function translates()
    {
        return $this->hasMany(BecomeAInvestorTranslate::class);
    }

    /**
     * Get translation for specific language
     */

    /**
     * Get or create the first investor record
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
