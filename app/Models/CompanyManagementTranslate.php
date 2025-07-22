<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyManagementTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_management_id',
        'language_id',
        'content',
    ];

    protected $casts = [
        'company_management_id' => 'integer',
        'language_id' => 'integer',
    ];

    /**
     * Get the company management that owns this translation
     */
    public function companyManagement()
    {
        return $this->belongsTo(CompanyManagement::class);
    }

    /**
     * Get the language for this translation
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
