<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestionsComplaintsTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'suggestions_complaints_id',
        'language_id',
        'title',
        'description',
        'form_title',
        'form_description',
    ];

    protected $casts = [
        'suggestions_complaints_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the suggestions complaints that owns this translation
     */
    public function suggestionsComplaints()
    {
        return $this->belongsTo(SuggestionsComplaints::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
