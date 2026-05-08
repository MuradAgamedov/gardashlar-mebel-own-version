<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compaign;

class CompaignsSitemapController extends Controller
{
     public function index(Request $request)
    {
        $compaigns = Compaign::with("translates.language")->where('status', true)->get();
        
        $transformedCompaigns = $compaigns->map(function ($compaign) {
            $translations = [];
            
            foreach ($compaign->translates as $translate) {
                $translations[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            return $translations;
        });
        
        return response()->json([
            'success' => true,
            'data' => $transformedCompaigns
        ]);
    }
}
