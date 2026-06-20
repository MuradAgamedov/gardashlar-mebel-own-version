<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\CompanyManagementResource;

use App\Http\Controllers\Controller;
use App\Traits\LocaleFromHeader;
use App\Interfaces\Services\Front\CompanyManagementServiceInterface;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanyManagementController extends Controller
{
    use LocaleFromHeader;

    protected CompanyManagementServiceInterface $companyManagementService;

    public function __construct(CompanyManagementServiceInterface $companyManagementService)
    {
        $this->companyManagementService = $companyManagementService;
    }

   
    public function index(Request $request)
    {
        try {
            // Get language ID from header
            $languageId = $this->getLanguageIdFromHeader($request); // Default to Azerbaijani (1)
            
            // Validate language ID
            $language = Language::find($languageId);
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            // Get company management with cache
            $companyManagement = Cache::rememberForever("company_management_{$languageId}", function () use ($languageId) {
                return CompanyManagement::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->first();
            });

            if (!$companyManagement) {
                return $this->notFound('Company management not found');
            }

            $translation = $companyManagement->translates->first();

            $companyManagementData = [
                'id' => $companyManagement->id,
                'content' => $translation ? $translation->content : ''
            ];

            return $this->success($companyManagementData, 'Company management retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving company management: ' . $e->getMessage());
        }
    }
} 