<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyPolicyTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'warranty_policy_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'warranty_policy_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the warranty policy that owns this translation
     */
    public function warrantyPolicy()
    {
        return $this->belongsTo(WarrantyPolicy::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
