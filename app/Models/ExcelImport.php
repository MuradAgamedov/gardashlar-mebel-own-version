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
}
