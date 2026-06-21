<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ServiceAdvantageRepositoryInterface;
use App\Interfaces\Services\Admin\ServiceAdvantageServiceInterface;
use App\Services\BaseService;

class ServiceAdvantageService extends BaseService implements ServiceAdvantageServiceInterface
{
    protected ServiceAdvantageRepositoryInterface $serviceAdvantageRepository;

    public function __construct(ServiceAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ServiceAdvantageRepository = $repository;
    }

    /**
     * Get all advantages for a service with translations
     *
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getServiceAdvantages($serviceId)
    {
        return $this->ServiceAdvantageRepository->getServiceAdvantages($serviceId);
    }

    /**
     * Find advantage by ID with translations
     *
     * @param int $id
     * @return \App\Models\ServiceAdvantage
     */
    public function findWithTranslations($id)
    {
        return $this->ServiceAdvantageRepository->findWithTranslations($id);
    }

    /**
     * Get service with translations
     *
     * @param int $serviceId
     * @return \App\Models\Service
     */
    public function getServiceWithTranslations($serviceId)
    {
        return $this->ServiceAdvantageRepository->getServiceWithTranslations($serviceId);
    }

    /**
     * Get active languages ordered by order field
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages()
    {
        return $this->ServiceAdvantageRepository->getActiveLanguages();
    }
}
