<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;
    protected $fillable = [
        'status',
        'order',
        'keyword',
        'opengraph_image',
        'opengraph_image_width',
        'opengraph_image_height'
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at', 'order', 'status', 'keyword'];

    public function translates(): HasMany
    {
        return $this->hasMany(PageTranslate::class);
    }

}
