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
        $vacancies = Vacancy::all();
        return response()->json([
            'success' => true,
            'message' => 'Vacancies sitemap generated successfully',
            'data' => $vacancies
        ]);
    }
}
