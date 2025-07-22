<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyManagement extends Model
{
    use HasFactory;

    protected $table = 'company_managements';

    protected $fillable = ['status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function translates()
    {
        return $this->hasMany(CompanyManagementTranslate::class);
    }
}
