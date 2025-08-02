<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'image_path'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->image_path)) {
            return '';
        }
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get image by keyword
     */
    public static function getByKeyword($keyword)
    {
        return static::where('keyword', $keyword)->first();
    }

    /**
     * Get image URL by keyword
     */
    public static function getImageUrl($keyword)
    {
        $image = static::getByKeyword($keyword);
        return $image ? $image->image_url : '';
    }
}
