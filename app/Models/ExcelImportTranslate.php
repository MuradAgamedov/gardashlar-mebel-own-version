<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExcelImportTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'excel_import_id',
        'language_id',
        'title',
        'description',
    ];

    /**
     * Get the excel import that owns the translate.
     */
    public function excelImport(): BelongsTo
    {
        return $this->belongsTo(ExcelImport::class);
    }

    /**
     * Get the language that owns the translate.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
