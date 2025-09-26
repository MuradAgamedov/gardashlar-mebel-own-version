<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id',
        'language_id',
        'title',
        'description',
        'image',
        'image_alt_text'
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return '';
        }
        return asset('storage/' . $this->image);
    }
}
