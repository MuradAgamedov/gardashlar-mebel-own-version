<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AllSeoPagesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class AllSeoPagesController extends Controller
{
    protected $allSeoPagesRepository;

    public function __construct(AllSeoPagesRepository $allSeoPagesRepository)
    {
        $this->allSeoPagesRepository = $allSeoPagesRepository;
    }

    
    public function index(): JsonResponse
    {
        try {
            $cacheKey = "api:all-seo-pages:settings";

            $seoSettings = Cache::rememberForever($cacheKey, function () {
                $seoPages = $this->allSeoPagesRepository->get();
                
                return [
                    'is_indexed' => (bool) $seoPages->is_indexed,
                    'is_followed' => (bool) $seoPages->is_followed,
                    'rolette_actions' => (bool) $seoPages->rolette_actions,
                    'popup_ads_show' => (bool) $seoPages->popup_ads_show,
                    'page_header' => $seoPages->page_header,
                    'page_footer' => $seoPages->page_footer,
                ];
            });

            return $this->success($seoSettings);

        } catch (\Throwable $e) {
            report($e);
            return $this->serverError('Error retrieving SEO settings.');
        }
    }

}
