<?php

namespace App\Repositories;

use App\Models\Service;
use App\Models\ServiceAdvantage;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class ServiceAdvantageRepository
{
    protected $serviceAdvantage;
    protected $service;
    protected $language;

    public function __construct(ServiceAdvantage $serviceAdvantage, Service $service, Language $language)
    {
        $this->serviceAdvantage = $serviceAdvantage;
        $this->service = $service;
        $this->language = $language;
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
