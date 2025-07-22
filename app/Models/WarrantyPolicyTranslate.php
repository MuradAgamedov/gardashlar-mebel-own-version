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
        'title',
        'content',
    ];

    public function warrantyPolicy()
    {
        return $this->belongsTo(WarrantyPolicy::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
