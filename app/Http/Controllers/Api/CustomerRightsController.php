<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Models\CustomerRights;
use App\Models\Language;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerRightsController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = Language::find($languageId);
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            $customerRights = Cache::rememberForever("customer_rights_{$languageId}", function () use ($languageId) {
                return CustomerRights::with(['translates' => fn($q) => $q->where('language_id', $languageId)])->first();
            });

            if (!$customerRights) {
                return $this->notFound('Customer rights not found');
            }

            return $this->success(new ContentPageResource($customerRights), 'Customer rights retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving customer rights: ' . $e->getMessage());
        }
    }
}
