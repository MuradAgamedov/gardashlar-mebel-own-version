<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualProjectOrderTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'individual_project_order_id',
        'language_id',
        'title',
        'description'
    ];

    public function individualProjectOrder(): BelongsTo
    {
        return $this->belongsTo(IndividualProjectOrder::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
