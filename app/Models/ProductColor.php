<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'color_code'
    ];

    /**
     * Validation rules for color code
     */
    public static function rules()
    {
        return [
            'color_code' => 'required|string|unique:product_colors,color_code'
        ];
    }

    /**
     * Update validation rules (excluding current record)
     */
    public static function updateRules($id)
    {
        return [
            'color_code' => 'required|string|unique:product_colors,color_code,' . $id
        ];
    }
}
