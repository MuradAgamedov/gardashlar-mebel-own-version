<?php

namespace App\Http\Controllers\Api\Sitemap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServicesSitemapController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::with("translates.language")->where('status', true)->orderBy('order')->get();
        
        $transformedServices = $services->map(function ($service) {
            $translations = [];
            
            foreach ($service->translates as $translate) {
                $translations[$translate->language->lang_code ?? 'unknown'] = $translate->slug ?? '';
            }
            
            return $translations;
        });
        
        return $this->success($transformedServices);
    }
}
