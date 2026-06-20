<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;

interface SocialNetworkServiceInterface extends BaseServiceInterface
{
    public function getByType(string $type);
    public function getByTypes(array $types);
    public function getAvailableTypes(): array;
}
