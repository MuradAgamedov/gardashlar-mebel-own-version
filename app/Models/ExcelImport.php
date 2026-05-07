<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelImport extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'file_url',
    ];

    /**
     * Get the full file URL.
     */
    public function getFullFileUrlAttribute()
    {
        return asset($this->file_url);
    }

    /**
     * Get or create the single Excel import record.
     * Always ensures only one record exists.
     */
    public static function getOrCreateSingle()
    {
        $excelImport = self::first();
        
        if (!$excelImport) {
            $excelImport = self::create([
                'file_url' => null,
            ]);
        }
        
        return $excelImport;
    }

    /**
     * Update the single record with new file URL.
     */
    public static function updateSingleFile($filePath)
    {
        $excelImport = self::first();
        
        if ($excelImport) {
            $excelImport->update(['file_url' => $filePath]);
        } else {
            $excelImport = self::create(['file_url' => $filePath]);
        }
        
        return $excelImport;
    }
}
