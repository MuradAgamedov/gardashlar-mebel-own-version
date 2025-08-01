<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryTranslate extends Model
{
    protected $fillable = [
        'dictionary_id',
        'language_id',
        'value'
    ];

    protected $casts = [
        'dictionary_id' => 'integer',
        'language_id' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at', 'dictionary_id', 'language_id'];

    public function dictionary(): BelongsTo
    {
        return $this->belongsTo(Dictionary::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
