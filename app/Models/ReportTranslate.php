<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'language_id',
        'title',
        'description',
    ];

    protected $casts = [
        'report_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the report that owns this translation
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
