<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllSeoPages extends Model
{
    use HasFactory;

    protected $table = 'all_seo_pages';

    protected $fillable = [
        'is_indexed',
        'is_followed',
        'page_header',
        'page_footer',
    ];

    protected $casts = [
        'is_indexed' => 'boolean',
        'is_followed' => 'boolean',
    ];
}
