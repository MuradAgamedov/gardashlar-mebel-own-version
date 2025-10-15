<?php

namespace App\Repositories;

use App\Models\Service;
use App\Models\ServiceAdvantage;
use App\Models\Language;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceAdvantageRepository
{
    protected $serviceAdvantage;
    protected $service;
    protected $language;
    protected $imageService;

    public function __construct(
        ServiceAdvantage $serviceAdvantage, 
        Service $service, 
        Language $language,
        ImageService $imageService
    ) {
        $this->serviceAdvantage = $serviceAdvantage;
        $this->service = $service;
        $this->language = $language;
        $this->imageService = $imageService;
    }

    /**
     * Get all advantages for a service with translations
     *
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getServiceAdvantages($serviceId)
    {
        return $this->serviceAdvantage->with(['translates.language'])
            ->where('service_id', $serviceId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Find advantage by ID with translations
     *
     * @param int $id
     * @return \App\Models\ServiceAdvantage
     */
    public function findWithTranslations($id)
    {
        return $this->serviceAdvantage->with(['translates.language'])->findOrFail($id);
    }

    /**
     * Get service with translations
     *
     * @param int $serviceId
     * @return \App\Models\Service
     */
    public function getServiceWithTranslations($serviceId)
    {
        return $this->service->with(['translates.language'])
            ->findOrFail($serviceId);
    }

    /**
     * Get active languages ordered by order field
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->language->active()
            ->orderBy('order')
            ->get();
    }
}
