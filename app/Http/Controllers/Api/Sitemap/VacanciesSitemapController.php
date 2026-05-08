<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VacanciesSitemapController extends Controller
{
    /**
     * Generate vacancies sitemap
     */
    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Vacancies sitemap generated successfully'
        ]);
    }
}
