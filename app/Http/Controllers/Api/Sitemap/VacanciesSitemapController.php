<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacancy;

class VacanciesSitemapController extends Controller
{
    /**
     * Generate vacancies sitemap
     */
    public function index(Request $request)
    {
        $vacancies = Vacancy::with("translates.language")->where('status', true)->get();
        
        $transformedVacancies = $vacancies->map(function ($vacancy) {
            $translations = [];
            
            foreach ($vacancy->translates as $translate) {
                $translations[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            return $translations;
        });
        
        return response()->json([
            'success' => true,
            'data' => $transformedVacancies
        ]);
    }
}
