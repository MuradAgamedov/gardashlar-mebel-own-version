<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ServiceAdvantageRepositoryInterface extends BaseRepositoryInterface
{
    //

    /**
     * Get all advantages for a service with translations
     *
     * @param int $serviceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getServiceAdvantages($serviceId);

    /**
     * Find advantage by ID with translations
     *
     * @param int $id
     * @return \App\Models\ServiceAdvantage
     */
    public function findWithTranslations($id);

    /**
     * Get service with translations
     *
     * @param int $serviceId
     * @return \App\Models\Service
     */
    public function getServiceWithTranslations($serviceId);

    /**
     * Get active languages ordered by order field
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLanguages();
}
