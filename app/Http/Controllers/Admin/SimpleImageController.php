<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SimpleImage;
use Illuminate\Http\Request;

class SimpleImageController extends Controller
{
    /**
     * Download image by keyword
     */
    public function downloadImage(string $keyword)
    {
        $simpleImage = SimpleImage::where('keyword', $keyword)->first();
        
        if (!$simpleImage || !$simpleImage->image_path) {
            abort(404, 'Image not found for keyword: ' . $keyword);
        }
        
        $imagePath = storage_path('app/public/' . $simpleImage->image_path);
        
        if (!file_exists($imagePath)) {
            abort(404, 'Image file not found');
        }
        
        $filename = $keyword . '.' . pathinfo($imagePath, PATHINFO_EXTENSION);
        
        return response()->download($imagePath, $filename);
    }
}
