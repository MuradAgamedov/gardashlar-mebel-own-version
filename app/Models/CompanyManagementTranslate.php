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
        'title',
        'content',
    ];

    public function companyManagement()
    {
        return $this->belongsTo(CompanyManagement::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
