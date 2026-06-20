<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;

interface ServiceServiceInterface extends BaseServiceInterface
{
    public function getServiceBySlugWithRelations(string $slug, int $languageId);
}
